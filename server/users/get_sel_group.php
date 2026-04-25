<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "SELECT name FROM `group` ORDER BY name ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($result)) {
            $datas = array_map(function ($row) {
                return ['name' => $row['name']];
            }, $result);

            $response = [
                'status' => true,
                'message' => 'Success',
                'data' => $datas
            ];
            http_response_code(200);
            echo json_encode($response);
            exit;
        }

        $response = [
            'status' => true,
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
        http_response_code(400);
        echo json_encode($response);
        exit;
    }
}
?>
