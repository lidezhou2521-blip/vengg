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

        $sql = "SELECT user_id,fname,name,sname,phone,bank_account,bank_comment 
                FROM profile 
                WHERE status=10 
                ORDER BY st";
        $query = $conn->prepare($sql);
        $query->execute();
        $users = $query->fetchAll(PDO::FETCH_OBJ);

        $sql = "SELECT * 
                FROM ven 
                WHERE ven_month = :date_month 
                    AND (status = 1 OR status = 2) 
                ORDER BY user_id";
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
            WHERE vc.ven_month=:date_month
            ORDER BY CAST(vc.ven_com_num AS DECIMAL)";
            // ORDER BY vc.id ASC";
        $query = $conn->prepare($sql);
        $query->bindParam(':date_month', $DATE_MONTH, PDO::PARAM_STR);
        $query->execute();
        $ven_coms = $query->fetchAll(PDO::FETCH_OBJ);

        foreach ($users as $user) {
            $ven_users = array();
            $D_c = 0;
            $N_c = 0;
            $D_price = 0;
            $N_price = 0;

            foreach ($vens as $ven) {
                if ($ven->user_id == $user->user_id && $ven->price > 0) {

                    if ($ven->DN == 'กลางวัน') {
                        $D_price += $ven->price;
                        $D_c++;
                    }
                    if ($ven->DN == 'กลางคืน') {
                        $N_price += $ven->price;
                        $N_c++;
                    }
                    $price_all += $ven->price;

                    array_push($ven_users, array(
                        "ven_date" => $ven->ven_date,
                        "DN" => $ven->DN,
                        "ven_com_idb" => $ven->ven_com_idb,
                        "price" => $ven->price,
                    ));
                }
            }

            if (count($ven_users) > 0) {
                $vcs_arr = array();

                foreach ($ven_coms as $vcs) {
                    $price_sum = 0;
                    $vsc_price = 0;
                    $v_count = 0;
                    foreach ($ven_users as $vus) {
                        if ($vus['ven_com_idb'] == $vcs->id) {
                            $vsc_price += $vus['price'];
                            ++$v_count;
                        }
                        $price_sum += $vus['price'];
                    }
                    array_push($vcs_arr, array(
                        "id" => $vcs->id,
                        "ven_name" => $vcs->vn_name,
                        "price" => $vsc_price,
                        "v_count" => $v_count
                    ));
                }
                array_push($datas, array(
                    "uid" => $user->user_id,
                    "vcs_arr" => $vcs_arr,
                    "name" => $user->fname . $user->name . ' ' . $user->sname,
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

        http_response_code(200);
        echo json_encode(array(
            'status' => true,
            'message' => 'Ok.',
            'datas' => $datas,
            "price_all" => $price_all,
            "ven_coms" => $ven_coms,
            'month' => DateThai_ym($DATE_MONTH),
            'date_doc' => DateThai_full(date("Y-m-d")),
        ));
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
    }
}
