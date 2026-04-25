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

    try {
        $id     = $data->id;

        $sql = "SELECT status FROM ven WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $rs = $query->fetch(PDO::FETCH_OBJ);

        if ($rs) {
            if ($rs->status == 1) {
                $status = 5;
            } elseif ($rs->status == 5) {
                $status = 1;
            } else {
                $status = $rs->status;
            }
            $sql = "UPDATE ven SET ven.status=:status WHERE id = :id";

            $query = $conn->prepare($sql);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ok'));
            exit;
        }


        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'no-action'));
        exit;
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
    }
}
