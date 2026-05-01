<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");
date_default_timezone_set("Asia/Bangkok");

include 'vendor/autoload.php';

include_once "./dbconfig.php";
include_once "./function.php";

$error = '';

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($data->month) && preg_match('/^\d{4}-\d{2}$/', $data->month)) {
        // Valid format, continue with the code
        $month = $data->month;
        $DATE_MONTH = date($data->month);
        // Rest of your code here
    } else {
        // Invalid format, handle the error
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Invalid month format'));
        exit;
    }

    // $DATE_MONTH = date("2023-06");
    $users = array();
    $vens = array();
    $ven_users = array();
    $ven_coms = array();

    $datas = array();
    $price_all = 0;

    try {

        $sql = "SELECT user_id,fname,name,sname,phone,bank_account,bank_comment,workgroup
                FROM profile 
                WHERE status=10 
                ORDER BY st";
        $query = $conn->prepare($sql);
        $query->execute();
        $users = $query->fetchAll(PDO::FETCH_OBJ);

        $sql = "SELECT v.*, vns.srt as vns_srt, vn.srt as vn_srt
                FROM ven v
                LEFT JOIN ven_name_sub vns ON v.vns_id = vns.id
                LEFT JOIN ven_name vn ON vns.ven_name_id = vn.id
                WHERE v.ven_month = :date_month 
                    AND (v.status = 1 OR v.status = 2) 
                ORDER BY v.user_id, vn.srt ASC, vns.srt ASC";
        $query = $conn->prepare($sql);
        $query->bindParam(':date_month', $DATE_MONTH, PDO::PARAM_STR);
        $query->execute();

        $query->execute();
        $vens = $query->fetchAll(PDO::FETCH_OBJ);

        $sql = "SELECT 
                vn.`name` AS vn_name,
                vc.*
            FROM ven_com AS vc
            INNER JOIN ven_name AS vn ON vc.vn_id = vn.id
            WHERE vc.ven_month=:date_month OR vc.id IN (SELECT ven_com_idb FROM ven WHERE ven_month=:date_month2)
            ORDER BY 
                CASE 
                    WHEN vn.`name` LIKE '%ตรวจสอบการจับ%' THEN 3
                    WHEN vn.`name` LIKE '%หมายจับ%' OR vn.`name` LIKE '%ค้น%' THEN 1 
                    ELSE 2 
                END ASC,
                CAST(vc.ven_com_num AS DECIMAL) ASC";
        $query = $conn->prepare($sql);
        $query->bindParam(':date_month', $DATE_MONTH, PDO::PARAM_STR);
        $query->bindParam(':date_month2', $DATE_MONTH, PDO::PARAM_STR);
        $query->execute();
        $ven_coms = $query->fetchAll(PDO::FETCH_OBJ);

        $excluded_duties = isset($data->excluded_duties) ? $data->excluded_duties : array();

        foreach ($users as $user) {
            $ven_users = array();
            $D_c = 0;
            $N_c = 0;
            $D_price = 0;
            $N_price = 0;

            foreach ($vens as $ven) {
                if ($ven->user_id == $user->user_id) {

                    // เงื่อนไขไม่เบิก: ven_com_id ว่าง/array ว่าง AND ven_com_idb ว่าง AND ven_com_num_all ว่าง
                    $com_id_raw = trim((string)$ven->ven_com_id);
                    $com_id_arr = json_decode($com_id_raw, true);
                    $com_id_empty = ($com_id_raw === '' || $com_id_raw === 'null' || $com_id_raw === '[]'
                        || (is_array($com_id_arr) && count($com_id_arr) === 0)
                        || $com_id_arr === null);
                    $com_idb = trim((string)$ven->ven_com_idb);
                    $com_num = trim((string)$ven->ven_com_num_all);
                    $is_no_claim_db = (
                        ($com_id_empty &&
                            ($com_idb === '' || $com_idb === 'null') &&
                            ($com_num === '' || $com_num === 'null'))
                        || $ven->price <= 0
                    );

                    $is_excluded = false;
                    foreach ($excluded_duties as $ex) {
                        if ($ex->user_id == $ven->user_id && $ex->day == (int)date('j', strtotime($ven->ven_date)) && $ex->ven_name == $ven->ven_name) {
                            $is_excluded = true;
                            break;
                        }
                    }

                    if (!$is_excluded) {
                        // คำนวณเงินเฉพาะที่เบิกได้
                        if (!$is_no_claim_db && $ven->price > 0) {
                            if ($ven->DN == 'กลางวัน') {
                                $D_price += $ven->price;
                                $D_c++;
                            }
                            if ($ven->DN == 'กลางคืน') {
                                $N_price += $ven->price;
                                $N_c++;
                            }
                            $price_all += $ven->price;
                        }

                        // หา ven_name จาก ven_table หรือใช้จาก u_role
                        $ven_name_found = $ven->ven_name ? $ven->ven_name : $ven->u_role;

                        array_push($ven_users, array(
                            "id" => $ven->id,
                            "ven_date" => $ven->ven_date,
                            "DN" => $ven->DN,
                            "ven_com_idb" => $ven->ven_com_idb,
                            "ven_name" => $ven_name_found,
                            "price" => $is_no_claim_db ? 0 : $ven->price,
                            "is_no_claim" => $is_no_claim_db,
                            "vn_srt" => $ven->vn_srt,
                            "vns_srt" => $ven->vns_srt,
                            "counted" => false
                        ));
                    }
                }
            }

            if (count($ven_users) > 0) {
                $vcs_arr = array();

                // คำนวณ price_sum จาก ven_users ทั้งหมดก่อน (ไม่ใช่ใน loop ven_coms)
                $price_sum = 0;
                foreach ($ven_users as $vus) {
                    $price_sum += $vus['price'];
                }

                foreach ($ven_coms as $vcs) {
                    $vsc_price = 0;
                    $v_count = 0;
                    $v_count_no_claim = 0;
                    foreach ($ven_users as &$vus) {
                        if ($vus['counted']) continue;

                        $duty_name = $vus['ven_name'];
                        $command_name = $vcs->vn_name;

                        $is_match = false;
                        if (strpos($duty_name, 'หมายจับ') !== false || strpos($duty_name, 'ค้น') !== false) {
                            if (strpos($command_name, 'หมายจับ') !== false || strpos($command_name, 'ค้น') !== false) $is_match = true;
                        } else if (strpos($duty_name, 'ศาลแขวง') !== false) {
                            if (strpos($command_name, 'ศาลแขวง') !== false) $is_match = true;
                        } else if (strpos($duty_name, 'เปิดทำการ') !== false) {
                            if (strpos($command_name, 'เปิดทำการ') !== false) $is_match = true;
                        } else if (strpos($duty_name, 'ตรวจสอบการจับ') !== false) {
                            if (strpos($command_name, 'ตรวจสอบการจับ') !== false) $is_match = true;
                        } else if ($duty_name == $command_name) {
                            $is_match = true;
                        }

                        if ($is_match) {
                            $vus['counted'] = true;
                            if ($vus['is_no_claim']) {
                                ++$v_count_no_claim;
                            } else {
                                $vsc_price += $vus['price'];
                                ++$v_count;
                            }
                        }
                    }
                    unset($vus);

                    array_push($vcs_arr, array(
                        "id" => $vcs->id,
                        "ven_name" => $vcs->vn_name,
                        "price" => $vsc_price,
                        "v_count" => $v_count,
                        "v_count_no_claim" => $v_count_no_claim
                    ));
                }
                array_push($datas, array(
                    "uid" => $user->user_id,
                    "vcs_arr" => $vcs_arr,
                    "name" => $user->fname . $user->name . ' ' . $user->sname,
                    "workgroup" => $user->workgroup ?? '',
                    "vens" => $ven_users,
                    "D_c" => $D_c,
                    "N_c" => $N_c,
                    "D_price" => $D_price,
                    "N_price" => $N_price,
                    "price_sum" => $price_sum,
                    "phone" => $user->phone,
                    "bank_account" => $user->bank_account,
                    "bank_comment" => $user->bank_comment
                ));
            }
        }

        $ven_coms_arr = array();
        foreach ($ven_coms as $vc) {
            array_push($ven_coms_arr, array(
                "id" => $vc->id,
                "ven_com_num" => $vc->ven_com_num,
                "ven_com_date" => $vc->ven_com_date
            ));
        }

        $price_all_text = Convert($price_all);

        http_response_code(200);
        echo json_encode(array(
            'status' => true,
            'message' => 'Ok.',
            'datas' => $datas,
            "price_all" => $price_all,
            "price_all_text" => $price_all_text,
            "ven_coms" => $ven_coms,
            'month' => DateThai_ym($DATE_MONTH),
            'date_doc' => DateThai_full(date("Y-m-d")),
            'days_in_month' => (int)date('t', strtotime($DATE_MONTH . '-01')),
        ));
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
    }
}
