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

    if ($_SESSION['AD_ROLE'] != 9) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Unauthorized'));
        exit;
    }

    if (!isset($data->user) || empty($data->user) || !isset($data->user->id) || empty($data->user->id)) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'No data'));
        exit;
    }

    $user = $data->user;

    try {
        $sql = "SELECT id FROM user WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $user->id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if ($query->rowCount() === 0) {
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'User does not exist'));
            exit;
        }

        $date_time = date("Y-m-d H:i:s");
        if ($user->password != null) {
            $password = $user->password;
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $sql = "UPDATE user SET 
                    username = :username,
                    password_hash = :password_hash,
                    role = :role
                    WHERE id = :id";
            $query = $conn->prepare($sql);
            $query->bindParam(':username', $user->username, PDO::PARAM_STR);
            $query->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
            $query->bindParam(':role', $user->role, PDO::PARAM_INT);
            $query->bindParam(':id', $user->id, PDO::PARAM_INT);
            $query->execute();
        } else {
            $sql = "UPDATE user SET 
                    username = :username,
                    role = :role
                    WHERE id = :id";
            $query = $conn->prepare($sql);
            $query->bindParam(':username', $user->username, PDO::PARAM_STR);
            $query->bindParam(':role', $user->role, PDO::PARAM_INT);
            $query->bindParam(':id', $user->id, PDO::PARAM_INT);
            $query->execute();
        }

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
