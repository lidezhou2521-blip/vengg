<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "SELECT * FROM holiday ORDER BY holiday_date ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(array('status' => true, 'respJSON' => $result));
    } catch (PDOException $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}
?>
