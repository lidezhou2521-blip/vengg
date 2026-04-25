<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datas = array();

    $act = $data->act;

    try {
        if ($act == 'insert') {
            /**     รับค่า  */
            $id = time();
            $ven_date       = $data->ven_date;
            $user_id        = $data->uid;

            $ven_com_id     = json_encode(array($data->vc_id));
            $ven_com_idb    = $data->vc_id;
            $ven_month      = $data->ven_month;
            $vn_id          = $data->vn_id;
            $vns_id         = $data->vns_id;
            $DN             = $data->DN;

            $ven_name       = $data->ven_name;
            $ven_com_name   = $data->ven_name;
            $ven_com_num    = $data->ven_com_num;

            $u_role         = $data->u_role;
            $price          = $data->price;
            $color          = $data->color;
            $vn_srt         = $data->vn_srt;
            $vns_srt        = $data->vns_srt;
            $status         = 2;

            $update_at      = '';
            $create_at      = '';


            if($price > 0 && ($DN == 'กลางวัน' || $DN == 'กลางคืน')){

                /** เช็ควันเวลาที่อยู่เวรไม่ได้ */
                $ven_date_u1 = date("Y-m-d", strtotime('+1 day', strtotime($ven_date)));
                $ven_date_d1 = date("Y-m-d", strtotime('-1 day', strtotime($ven_date)));
    
                $sql_VU = "SELECT v.*, p.fname, p.name, p.sname 
                            FROM ven AS v
                            INNER JOIN `profile` AS p ON p.user_id = v.user_id
                            WHERE v.user_id = :user_id AND v.ven_date >= :ven_date_d1 AND v.ven_date <= :ven_date_u1 AND (v.status = 1 OR v.status = 2)";
                $query_VU = $conn->prepare($sql_VU);
                $query_VU->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $query_VU->bindParam(':ven_date_d1', $ven_date_d1);
                $query_VU->bindParam(':ven_date_u1', $ven_date_u1);
                $query_VU->execute();
                $res_VU = $query_VU->fetchAll(PDO::FETCH_OBJ);
                $warning_msg = '';
                if ($query_VU->rowCount()) {
                    foreach ($res_VU as $ru) {
                        if ($DN == 'กลางวัน' && $ru->ven_date == $ven_date_d1 && $ru->DN == 'กลางคืน') {
                            $warning_msg = $ru->fname.$ru->name.' '.$ru->sname. "\n มีเวรกลางคืน \nวันก่อนหน้านี้(".DateThai($ven_date_d1).")";
                            $color = '#ff0000'; // highlight color
                        }
                        if ($DN == 'กลางคืน'  && $ru->ven_date == $ven_date_u1 && $ru->DN == 'กลางวัน') {
                            $warning_msg = $ru->fname.$ru->name.' '.$ru->sname. "\n มีเวรกลางวัน \nวันถัดไป(".DateThai($ven_date_u1).")";
                            $color = '#ff0000'; // highlight color
                        }
                        // เช็คเวรหมายจับ-ค้น ชนกับเวรกลางวันในวันเดียวกัน
                        if ($ru->ven_date == $ven_date && $ru->DN == 'กลางวัน' && strpos($ven_name, 'หมายจับ-ค้น') !== false && $DN == 'กลางคืน') {
                            $warning_msg = $ru->fname.$ru->name.' '.$ru->sname. "\n มีเวรกลางวัน \nในวันเดียวกัน(".DateThai($ven_date).")";
                            $color = '#ff0000'; // highlight color
                        }
                        // เช็คเวรกลางวันชนกับเวรหมายจับ-ค้น (กลางคืน) ที่มีอยู่แล้วในวันเดียวกัน
                        if ($ru->ven_date == $ven_date && $ru->DN == 'กลางคืน' && $DN == 'กลางวัน' && strpos($ru->ven_name, 'หมายจับ-ค้น') !== false) {
                            $warning_msg = $ru->fname.$ru->name.' '.$ru->sname. "\n มีเวรหมายจับ-ค้น (กลางคืน) \nในวันเดียวกัน(".DateThai($ven_date).")";
                            $color = '#ff0000'; // highlight color
                        }
                    }
                }
            } 


            $ref1           = generateRandomString();
            $ref2           =  $ref1;
            $status         = 2;
            $update_at      = Date("Y-m-d H:i:s");
            $create_at      = Date("Y-m-d H:i:s");

            $ven_time = '';


            $comment = isset($warning_msg) ? $warning_msg : '';

            $sql = "INSERT INTO ven(id, user_id, ven_com_id, ven_com_idb, ven_date, ven_time, ven_month, vn_id, vns_id, 
                        DN, ven_com_name, ven_com_num_all, ven_name, u_role, price, color, ref1, ref2, comment, `status`, update_at, create_at) 
                    VALUE(:id, :user_id, :ven_com_id, :ven_com_idb, :ven_date, :ven_time, :ven_month, :vn_id, :vns_id, 
                        :DN, :ven_com_name, :ven_com_num_all, :ven_name, :u_role, :price, :color, :ref1, :ref2, :comment, :status, :update_at, :create_at);";
            $query = $conn->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->bindParam(':user_id', $user_id, PDO::PARAM_STR);
            $query->bindParam(':ven_com_id', $ven_com_id, PDO::PARAM_STR);
            $query->bindParam(':ven_com_idb', $ven_com_idb, PDO::PARAM_STR);
            $query->bindParam(':ven_date', $ven_date, PDO::PARAM_STR);
            $query->bindParam(':ven_time', $ven_time, PDO::PARAM_STR);
            $query->bindParam(':ven_month', $ven_month, PDO::PARAM_STR);
            $query->bindParam(':vn_id', $vn_id, PDO::PARAM_INT);
            $query->bindParam(':vns_id', $vns_id, PDO::PARAM_INT);
            $query->bindParam(':DN', $DN, PDO::PARAM_STR);
            $query->bindParam(':ven_com_name', $ven_com_name, PDO::PARAM_STR);
            $query->bindParam(':ven_com_num_all', $ven_com_num, PDO::PARAM_STR);
            $query->bindParam(':ven_name', $ven_name, PDO::PARAM_STR);
            $query->bindParam(':u_role', $u_role, PDO::PARAM_STR);
            $query->bindParam(':price', $price, PDO::PARAM_STR);
            $query->bindParam(':color', $color, PDO::PARAM_STR);
            $query->bindParam(':ref1', $ref1, PDO::PARAM_STR);
            $query->bindParam(':ref2', $ref2, PDO::PARAM_STR);
            $query->bindParam(':comment', $comment, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_INT);
            $query->bindParam(':update_at', $create_at, PDO::PARAM_STR);
            $query->bindParam(':create_at', $create_at, PDO::PARAM_STR);
            $query->execute();


            /** จัดลำดับ */
            $sql = "SELECT 
                        v.id,
                        v.DN,
                        v.u_role,
                        v.ven_time,	
                        vn.srt AS vn_srt,
                        vns.srt AS vns_srt
                    FROM ven AS v
                    INNER JOIN ven_name AS vn ON v.vn_id = vn.id
                    INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id
                    WHERE v.ven_date = :ven_date 
                        AND (v.status = 1 OR v.status = 2)
                    ORDER BY 
                        vn_srt ASC,
                        vns_srt ASC,
                        v.update_at ASC";
            $query = $conn->prepare($sql);
            $query->bindParam(':ven_date', $ven_date);
            $query->execute();

            $seconds = 0;
            foreach ($query->fetchAll(PDO::FETCH_OBJ) as $rs) {
                $hours = 8;
                if ($rs->DN == 'กลางคืน' || $rs->DN == 'nightCourt') {
                    $hours = 16;
                } 
                ++$seconds;
                $ven_time = date("H:i:s", mktime($hours, 30, $seconds));

                $updateSql = "UPDATE ven SET ven_time = :ven_time WHERE id = :id";
                $updateQuery = $conn->prepare($updateSql);
                $updateQuery->bindParam(':ven_time', $ven_time, PDO::PARAM_STR);
                $updateQuery->bindParam(':id', $rs->id, PDO::PARAM_INT);
                $updateQuery->execute();
            }
            /** จัดลำดับ */

            http_response_code(200);
            if (isset($warning_msg) && $warning_msg != '') {
                echo json_encode(array('status' => true, 'message' => "เพิ่มเวรสำเร็จ แต่พบข้อสังเกต:\n" . $warning_msg, 'responseJSON' => $data, 'ven_time' => $ven_time, 'icon' => 'warning'));
            } else {
                echo json_encode(array('status' => true, 'message' => 'success', 'responseJSON' => $data, 'ven_time' => $ven_time, 'icon' => 'success'));
            }
            exit;
        }
    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Error: ' . $e->getMessage()));
        exit;
    }
}
