<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $datas = array();

    try {
        // Duty types: 1 entry per duty name (vn), not per sub-role
        $sql = "SELECT vn.id as vn_id, vn.name, vn.DN,
                       (SELECT vns2.color FROM ven_name_sub AS vns2
                        WHERE vns2.ven_name_id = vn.id ORDER BY vns2.srt ASC LIMIT 1) AS color
                FROM ven_name AS vn
                ORDER BY vn.srt ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $res = $query->fetchAll(PDO::FETCH_OBJ);

        $sql = "SELECT 
                    v.color, v.comment, v.vn_id, v.vns_id, v.DN, v.ven_com_id,
                    v.id, v.ven_date, v.ven_time, v.u_role, v.price, v.ven_com_name, v.ven_name,
                    p.fname, p.name, p.sname 
                FROM ven AS v 
                INNER JOIN `profile` AS p ON v.user_id = p.id
                LEFT JOIN ven_name_sub AS vns ON v.vns_id = vns.id
                WHERE v.status IN (1, 2, 5) 
                ORDER BY v.ven_date DESC, v.ven_time ASC, vns.srt ASC
                LIMIT 5000";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
            foreach ($result as $rs) {
                $rs->DN == 'กลางวัน' ? $d = '☀️' : $d = '🌙';
                
                $vc_count = 0;
                if ($rs->ven_com_id) {
                    $vc_arr = json_decode($rs->ven_com_id, true);
                    if (is_array($vc_arr)) {
                        $vc_count = count($vc_arr);
                    }
                }
                
                array_push($datas, array(
                    'id' => $rs->id,
                    'title' => $d.' '.$rs->fname.$rs->name.' '.$rs->sname,
                    'start' => $rs->ven_date . ' ' . $rs->ven_time,
                    'backgroundColor' => $rs->color,
                    'comment' => $rs->comment ? $rs->comment : '',
                    'extendedProps' => array(
                        'u_name' => $rs->fname.$rs->name.' '.$rs->sname,
                        'u_role' => $rs->u_role,
                        'ven_com_name' => $rs->ven_com_name,
                        'ven_name' => $rs->ven_name,
                        'vn_id' => (int)$rs->vn_id,
                        'vns_id' => (int)$rs->vns_id,
                        'vc_count' => $vc_count
                    )
                ));
            }
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'success', 'respJSON' => $datas, 'res' => $res));
            exit;
        }

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'ไม่พบข้อมูล', 'respJSON' => $datas, 'res' => $res));
        exit;
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Error: ' . $e->getMessage()));
        exit;
    }
}
?>