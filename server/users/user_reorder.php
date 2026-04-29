<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

require_once "../connect.php";
require_once "../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['AD_ROLE'] != 9) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Unauthorized'));
        exit;
    }

    if (isset($data->updates) && is_array($data->updates)) {
        try {
            $conn->beginTransaction();
            $sql = "UPDATE profile SET st = :st WHERE user_id = :uid";
            $query = $conn->prepare($sql);

            foreach ($data->updates as $item) {
                $query->bindParam(':st', $item->st, PDO::PARAM_INT);
                $query->bindParam(':uid', $item->uid, PDO::PARAM_INT);
                $query->execute();
            }

            $conn->commit();
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'อัพเดทลำดับเรียบร้อย'));
            exit;

        } catch (PDOException $e) {
            $conn->rollback();
            http_response_code(400);
            echo json_encode(array('status' => false, 'message' => 'ERROR: ' . $e->getMessage()));
            exit;
        }
    } else {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'No updates provided'));
        exit;
    }
}
?>
