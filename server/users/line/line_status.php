<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

require_once "../connect.php";
require_once "../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($data->user_id) && isset($data->st)) {
        $user_id = $data->user_id;
        $st = $data->st;
    } else {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'no-data'));
        exit;
    }

    try {
        $sql = "SELECT id FROM profile WHERE user_id = :user_id";
        $query = $conn->prepare($sql);
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if (empty($result)) {
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'ไม่มี user นี้อยู่ในระบบ'));
            exit;
        }

        $date_time = date("Y-m-d h:i:s");
        $st == 10 ? $str = 1 : $str = 99;

        $sql = "UPDATE profile 
                SET status = :status, updated_at = :updated_at, st = :st
                WHERE user_id = :user_id";
        $query = $conn->prepare($sql);
        $query->bindValue(':status', $st, PDO::PARAM_STR);
        $query->bindValue(':updated_at', $date_time, PDO::PARAM_STR);
        $query->bindValue(':st', $str, PDO::PARAM_INT);
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();

        $sql = "UPDATE user 
                SET status = :status, updated_at = :updated_at
                WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindValue(':status', $st, PDO::PARAM_STR);
        $query->bindValue(':updated_at', $date_time, PDO::PARAM_STR);
        $query->bindValue(':id', $user_id, PDO::PARAM_INT);
        $query->execute();

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'สำเร็จ'));
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'ERROR เกิดข้อผิดพลาด: ' . $e->getMessage()));
    }
}
?>
