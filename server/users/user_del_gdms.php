<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

require_once "../connect.php";
require_once "../function.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($data) || !isset($data->user_id)) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Invalid data provided'));
        exit;
    }

    $user_id = $data->user_id;

    try {
        $sql = "SELECT id, img FROM profile WHERE user_id = :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if ($result->id == 1) {
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'User does not exist'));
            exit;
        }

        if (!empty($result->img) && file_exists('../../uploads/users/' . $result->img)) {
            unlink('../../uploads/users/' . $result->img);
        }

        $sql = "DELETE FROM profile WHERE user_id = :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();

        $sql = "DELETE FROM ven WHERE user_id = :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();

        $sql = "DELETE FROM ven_user WHERE user_id = :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
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
