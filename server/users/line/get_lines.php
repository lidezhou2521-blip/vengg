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

    try {
        $sql = "SELECT * FROM line ORDER BY name ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'Success', 'data' => $result));
            exit;
        }

        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'No data found'));
        exit;

    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Error occurred: ' . $e->getMessage()));
        exit;
    }
}
?>
