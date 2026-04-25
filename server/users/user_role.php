<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $data->uid;

    try {
        // Check if the user exists
        $sql = "SELECT u.id, u.username, u.role
                FROM user AS u 
                WHERE u.id = :uid";
        $query = $conn->prepare($sql);
        $query->bindParam(':uid', $uid, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if (!empty($result)) {
            $response = [
                'status' => true,
                'message' => 'Success',
                'data' => $result
            ];
            http_response_code(200);
            echo json_encode($response);
            exit;
        }
        
        // If the user does not exist, delete the profile
        $sql = "DELETE FROM profile WHERE user_id = :uid";
        $query = $conn->prepare($sql);
        $query->bindParam(':uid', $uid, PDO::PARAM_STR);
        $query->execute();

        $response = [
            'status' => false,
            'message' => 'No data found'
        ];
        http_response_code(200);
        echo json_encode($response);
        exit;
    } catch (PDOException $e) {
        $response = [
            'status' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
        http_response_code(500);
        echo json_encode($response);
        exit;
    }
}
?>
