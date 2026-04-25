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
    $id = $data->id;

    try {
        $sql = "SELECT v.*, p.fname, p.name, p.sname 
                FROM ven AS v 
                INNER JOIN `profile` AS p ON v.user_id = p.user_id
                WHERE v.id = :id
                ORDER BY v.ven_date DESC
                LIMIT 1";
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $res = $query->fetch(PDO::FETCH_OBJ);

        $res_ven = array(
            "id" => $res->id,
            "user_id" => $res->user_id,
            "ven_com_id" => json_decode($res->ven_com_id),
            "ven_com_idb" => $res->ven_com_idb,
            "ven_date" => $res->ven_date,
            "ven_time" => $res->ven_time,
            "ven_month" => $res->ven_month,
            "DN" => $res->DN,
            "ven_com_name" => $res->ven_com_name,
            "ven_com_num_all" => $res->ven_com_num_all,
            "ven_name" => $res->ven_name,
            "u_role" => $res->u_role,
            "price" => $res->price,
            "fname" => $res->fname,
            "name" => $res->name,
            "sname" => $res->sname,
            "status" => $res->status
        );

        $sql = "SELECT 
                    vc.ven_com_num,
                    vc.ven_com_date,
                    vc.ven_month,
                    vc.`status`,
                    vc.id AS vc_id,
                    vn.id AS vn_id,
                    vn.name,
                    vn.DN
                FROM ven_com AS vc
                INNER JOIN ven_name AS vn ON vc.vn_id = vn.id 
                WHERE ven_month = :ven_month";
        $query = $conn->prepare($sql);
        $query->bindParam(':ven_month', $res->ven_month, PDO::PARAM_STR);
        $query->execute();

        $res_ven_coms = $query->fetchAll(PDO::FETCH_OBJ);

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'สำเร็จ', 'res_ven' => $res_ven, 'respJSON' => $res_ven, 'ven_coms' => $res_ven_coms));
        exit;
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()));
        exit;
    }
}
