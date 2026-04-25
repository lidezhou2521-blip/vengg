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
        $sql = "SELECT vns.name as u_role, vns.price, vns.color, vn.name, vn.DN
                FROM ven_name_sub AS vns
                INNER JOIN ven_name AS vn ON vn.id = vns.ven_name_id";
        $query = $conn->prepare($sql);
        $query->execute();
        $res = $query->fetchAll(PDO::FETCH_OBJ);

        $sql = "SELECT 
                    v.color, v.comment,
                    v.id, v.ven_date, v.ven_time, v.u_role, v.price, v.ven_com_name, 
                    p.name, p.sname 
                FROM ven AS v 
                INNER JOIN `profile` AS p ON v.user_id = p.user_id
                LEFT JOIN ven_name_sub AS vns ON v.vns_id = vns.id
                WHERE v.status IN (1, 2, 5) 
                ORDER BY v.ven_date DESC, v.ven_time ASC
                LIMIT 1000";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
            foreach ($result as $rs) {
                array_push($datas, array(
                    'id' => $rs->id,
                    'title' => $rs->name . ' ' . $rs->sname,
                    'start' => $rs->ven_date . ' ' . $rs->ven_time,
                    'backgroundColor' => $rs->color,
                    'comment' => $rs->comment ? $rs->comment : ''
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