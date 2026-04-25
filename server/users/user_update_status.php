<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($data->user_id) && !empty($data->user_id)) {
        $user_id = $data->user_id;
    } else {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'No data'));
        exit;
    }

    try {
        $sql = "SELECT id, status FROM profile WHERE user_id = :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if (empty($result)) {
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'User does not exist'));
            exit;
        }

        $date_time = date("Y-m-d H:i:s");
        $new_status = ($result->status == 10) ? 0 : 10;

        $sql = "UPDATE profile 
                SET status = :status, updated_at = :updated_at
                WHERE user_id = :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':status', $new_status, PDO::PARAM_INT);
        $query->bindParam(':updated_at', $date_time, PDO::PARAM_STR);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();

        $sql = "UPDATE user 
                SET status = :status, updated_at = :updated_at
                WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindParam(':status', $new_status, PDO::PARAM_INT);
        $query->bindParam(':updated_at', $date_time, PDO::PARAM_STR);
        $query->bindParam(':id', $user_id, PDO::PARAM_INT);
        $query->execute();

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'Success'));
        exit;

    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Error occurred: ' . $e->getMessage()));
        exit;
    }
}
?>
