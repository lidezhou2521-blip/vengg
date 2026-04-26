<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $data->act; // 'insert' or 'delete' or 'batch_insert'

    try {
        if ($act == 'delete') {
            $id = $data->id;
            $sql = "DELETE FROM ven WHERE id = :id AND (status = 1 OR status = 2)";
            $query = $conn->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ลบสำเร็จ'));
            exit;
        }

        if ($act == 'batch_insert') {
            $assignments = $data->assignments;
            $insertCount = 0;

            // Step 1: Group assignments by ven_date, sorted by vu_order
            $byDate = array();
            foreach ($assignments as $a) {
                $d = $a->ven_date;
                if (!isset($byDate[$d])) $byDate[$d] = array();
                $byDate[$d][] = $a;
            }
            // Sort within each date by vu_order ASC
            foreach ($byDate as $d => &$items) {
                usort($items, function($a, $b) {
                    $oa = isset($a->vu_order) ? (int)$a->vu_order : 999;
                    $ob = isset($b->vu_order) ? (int)$b->vu_order : 999;
                    return $oa - $ob;
                });
            }
            unset($items);

            // Step 2: Get existing max seconds per date to continue sequence
            $existingSeconds = array();

            // Step 3: Insert in order, assigning ven_time at insert time
            foreach ($byDate as $ven_date => $items) {
                // Count existing records for this date/command to continue ven_time sequence
                $sql_exist = "SELECT COUNT(*) as cnt, MAX(SECOND(ven_time)) as max_sec, v.DN
                              FROM ven AS v
                              WHERE v.ven_date = :ven_date AND v.ven_com_idb = :vc_id AND (v.status = 1 OR v.status = 2)
                              GROUP BY v.DN";
                $q_exist = $conn->prepare($sql_exist);
                $q_exist->bindParam(':ven_date', $ven_date);
                $vc_id_tmp = $items[0]->vc_id;
                $q_exist->bindParam(':vc_id', $vc_id_tmp);
                $q_exist->execute();
                $existSecDN = array('กลางวัน' => 0, 'กลางคืน' => 0);
                foreach ($q_exist->fetchAll(PDO::FETCH_OBJ) as $ex) {
                    $existSecDN[$ex->DN] = (int)($ex->max_sec ?? 0);
                }

                foreach ($items as $a) {
                    $user_id     = $a->uid;
                    $vc_id       = $a->vc_id;
                    $vn_id       = $a->vn_id;
                    $vns_id      = $a->vns_id;
                    $DN          = $a->DN;
                    $ven_month   = $a->ven_month;
                    $ven_name    = $a->ven_name;
                    $ven_com_num = $a->ven_com_num;
                    $u_role      = $a->u_role;
                    $price       = $a->price;
                    $color       = $a->color;
                    $vu_order    = isset($a->vu_order) ? (int)$a->vu_order : 999;

                    // Check duplicate
                    $sql_check = "SELECT id FROM ven WHERE user_id = :user_id AND ven_date = :ven_date AND vns_id = :vns_id AND ven_com_idb = :vc_id AND (status = 1 OR status = 2)";
                    $q_chk = $conn->prepare($sql_check);
                    $q_chk->bindParam(':user_id', $user_id);
                    $q_chk->bindParam(':ven_date', $ven_date);
                    $q_chk->bindParam(':vns_id', $vns_id);
                    $q_chk->bindParam(':vc_id', $vc_id);
                    $q_chk->execute();
                    if ($q_chk->rowCount() > 0) continue;

                    // Assign ven_time based on vu_order directly
                    $hours = ($DN == 'กลางคืน') ? 16 : 8;
                    $existSecDN[$DN]++;
                    $ven_time = date("H:i:s", mktime($hours, 30, $existSecDN[$DN]));

                    $id         = time() + rand(1, 99999);
                    $ref1       = generateRandomString();
                    $ref2       = $ref1;
                    $ven_com_id = json_encode(array($vc_id));
                    $status     = 2;
                    $update_at  = Date("Y-m-d H:i:s");
                    $create_at  = Date("Y-m-d H:i:s");

                    $sql = "INSERT INTO ven(id, user_id, ven_com_id, ven_com_idb, ven_date, ven_time, ven_month, vn_id, vns_id,
                                DN, ven_com_name, ven_com_num_all, ven_name, u_role, price, color, ref1, ref2, comment, `status`, update_at, create_at)
                            VALUE(:id, :user_id, :ven_com_id, :ven_com_idb, :ven_date, :ven_time, :ven_month, :vn_id, :vns_id,
                                :DN, :ven_com_name, :ven_com_num_all, :ven_name, :u_role, :price, :color, :ref1, :ref2, '', :status, :update_at, :create_at)";
                    $query = $conn->prepare($sql);
                    $query->bindParam(':id', $id, PDO::PARAM_INT);
                    $query->bindParam(':user_id', $user_id, PDO::PARAM_STR);
                    $query->bindParam(':ven_com_id', $ven_com_id, PDO::PARAM_STR);
                    $query->bindParam(':ven_com_idb', $vc_id, PDO::PARAM_STR);
                    $query->bindParam(':ven_date', $ven_date, PDO::PARAM_STR);
                    $query->bindParam(':ven_time', $ven_time, PDO::PARAM_STR);
                    $query->bindParam(':ven_month', $ven_month, PDO::PARAM_STR);
                    $query->bindParam(':vn_id', $vn_id, PDO::PARAM_INT);
                    $query->bindParam(':vns_id', $vns_id, PDO::PARAM_INT);
                    $query->bindParam(':DN', $DN, PDO::PARAM_STR);
                    $query->bindParam(':ven_com_name', $ven_name, PDO::PARAM_STR);
                    $query->bindParam(':ven_com_num_all', $ven_com_num, PDO::PARAM_STR);
                    $query->bindParam(':ven_name', $ven_name, PDO::PARAM_STR);
                    $query->bindParam(':u_role', $u_role, PDO::PARAM_STR);
                    $query->bindParam(':price', $price, PDO::PARAM_STR);
                    $query->bindParam(':color', $color, PDO::PARAM_STR);
                    $query->bindParam(':ref1', $ref1, PDO::PARAM_STR);
                    $query->bindParam(':ref2', $ref2, PDO::PARAM_STR);
                    $query->bindParam(':status', $status, PDO::PARAM_INT);
                    $query->bindParam(':update_at', $update_at, PDO::PARAM_STR);
                    $query->bindParam(':create_at', $create_at, PDO::PARAM_STR);
                    if ($query->execute()) {
                        $insertCount++;
                    }
                }
            }

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => "จัดเวรสำเร็จ $insertCount รายการ"));

            exit;
        }

        if ($act == 'insert') {
            $id = time() + rand(1, 999);
            $user_id    = $data->uid;
            $ven_date   = $data->ven_date;
            $ven_month  = $data->ven_month;
            $vc_id      = $data->vc_id;
            $vn_id      = $data->vn_id;
            $vns_id     = $data->vns_id;
            $DN         = $data->DN;
            $ven_name   = $data->ven_name;
            $ven_com_num = $data->ven_com_num;
            $u_role     = $data->u_role;
            $price      = $data->price;
            $color      = $data->color;

            // Check if already assigned to THIS specific duty
            $sql_check = "SELECT id FROM ven WHERE user_id = :user_id AND ven_date = :ven_date AND vns_id = :vns_id AND ven_com_idb = :vc_id AND (status = 1 OR status = 2)";
            $query_check = $conn->prepare($sql_check);
            $query_check->bindParam(':user_id', $user_id);
            $query_check->bindParam(':ven_date', $ven_date);
            $query_check->bindParam(':vns_id', $vns_id);
            $query_check->bindParam(':vc_id', $vc_id);
            $query_check->execute();
            if ($query_check->rowCount() > 0) {
                echo json_encode(array('status' => true, 'message' => 'มีรายชื่อนี้อยู่แล้ว (ข้ามการเพิ่ม)'));
                exit;
            }

            // Check for conflicts with OTHER duties
            if ($price > 0 && ($DN == 'กลางวัน' || $DN == 'กลางคืน')) {
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

                if ($query_VU->rowCount()) {
                    foreach ($res_VU as $ru) {
                        if ($DN == 'กลางวัน' && $ru->ven_date == $ven_date_d1 && $ru->DN == 'กลางคืน') {
                            $warning_msg = $ru->fname.$ru->name.' '.$ru->sname. "\n มีเวรกลางคืน วันก่อนหน้า";
                            $color = '#ff0000';
                        }
                        if ($DN == 'กลางคืน' && $ru->ven_date == $ven_date_u1 && $ru->DN == 'กลางวัน') {
                            $warning_msg = $ru->fname.$ru->name.' '.$ru->sname. "\n มีเวรกลางวัน วันถัดไป";
                            $color = '#ff0000';
                        }
                        if ($ru->ven_date == $ven_date && $ru->DN == 'กลางวัน' && strpos($ven_name, 'หมายจับ-ค้น') !== false && $DN == 'กลางคืน') {
                            $warning_msg = $ru->fname.$ru->name.' '.$ru->sname. "\n มีเวรกลางวัน ในวันเดียวกัน";
                            $color = '#ff0000';
                        }
                    }
                }
            }

            $comment    = $warning_msg;
            $ref1       = generateRandomString();
            $ref2       = $ref1;
            $ven_com_id = json_encode(array($vc_id));
            $status     = 2;
            $update_at  = Date("Y-m-d H:i:s");
            $create_at  = Date("Y-m-d H:i:s");
            $ven_time   = '';

            $sql = "INSERT INTO ven(id, user_id, ven_com_id, ven_com_idb, ven_date, ven_time, ven_month, vn_id, vns_id, 
                        DN, ven_com_name, ven_com_num_all, ven_name, u_role, price, color, ref1, ref2, comment, `status`, update_at, create_at) 
                    VALUE(:id, :user_id, :ven_com_id, :ven_com_idb, :ven_date, :ven_time, :ven_month, :vn_id, :vns_id, 
                        :DN, :ven_com_name, :ven_com_num_all, :ven_name, :u_role, :price, :color, :ref1, :ref2, :comment, :status, :update_at, :create_at);";
            $query = $conn->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->bindParam(':user_id', $user_id, PDO::PARAM_STR);
            $query->bindParam(':ven_com_id', $ven_com_id, PDO::PARAM_STR);
            $query->bindParam(':ven_com_idb', $vc_id, PDO::PARAM_STR);
            $query->bindParam(':ven_date', $ven_date, PDO::PARAM_STR);
            $query->bindParam(':ven_time', $ven_time, PDO::PARAM_STR);
            $query->bindParam(':ven_month', $ven_month, PDO::PARAM_STR);
            $query->bindParam(':vn_id', $vn_id, PDO::PARAM_INT);
            $query->bindParam(':vns_id', $vns_id, PDO::PARAM_INT);
            $query->bindParam(':DN', $DN, PDO::PARAM_STR);
            $query->bindParam(':ven_com_name', $ven_name, PDO::PARAM_STR);
            $query->bindParam(':ven_com_num_all', $ven_com_num, PDO::PARAM_STR);
            $query->bindParam(':ven_name', $ven_name, PDO::PARAM_STR);
            $query->bindParam(':u_role', $u_role, PDO::PARAM_STR);
            $query->bindParam(':price', $price, PDO::PARAM_STR);
            $query->bindParam(':color', $color, PDO::PARAM_STR);
            $query->bindParam(':ref1', $ref1, PDO::PARAM_STR);
            $query->bindParam(':ref2', $ref2, PDO::PARAM_STR);
            $query->bindParam(':comment', $comment, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_INT);
            $query->bindParam(':update_at', $update_at, PDO::PARAM_STR);
            $query->bindParam(':create_at', $create_at, PDO::PARAM_STR);
            $query->execute();

            /** จัดลำดับเวลา (Sort by time) เพื่อให้รายงานเรียงลำดับถูกต้อง */
            $sql_sort = "SELECT v.id, v.DN
                        FROM ven AS v
                        INNER JOIN ven_name AS vn ON v.vn_id = vn.id
                        INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id
                        WHERE v.ven_date = :ven_date 
                            AND (v.status = 1 OR v.status = 2)
                        ORDER BY vn.srt ASC, vns.srt ASC, v.id ASC";
            $query_sort = $conn->prepare($sql_sort);
            $query_sort->bindParam(':ven_date', $ven_date);
            $query_sort->execute();

            $seconds = 0;
            foreach ($query_sort->fetchAll(PDO::FETCH_OBJ) as $rs_sort) {
                $hours = 8;
                if ($rs_sort->DN == 'กลางคืน') {
                    $hours = 16;
                } 
                ++$seconds;
                $new_time = date("H:i:s", mktime($hours, 30, $seconds));

                $updateSql = "UPDATE ven SET ven_time = :ven_time WHERE id = :id";
                $updateQuery = $conn->prepare($updateSql);
                $updateQuery->bindParam(':ven_time', $new_time, PDO::PARAM_STR);
                $updateQuery->bindParam(':id', $rs_sort->id, PDO::PARAM_INT);
                $updateQuery->execute();
            }

            $icon = $warning_msg ? 'warning' : 'success';
            $msg  = $warning_msg ? "เพิ่มสำเร็จ (มีข้อสังเกต)" : 'เพิ่มสำเร็จ';

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => $msg, 'icon' => $icon, 'warning' => $warning_msg));
            exit;
        }

    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Error: ' . $e->getMessage()));
        exit;
    }
}
