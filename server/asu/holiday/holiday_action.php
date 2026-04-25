<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $data->act;

    try {
        if ($act == 'insert') {
            $holiday_date = $data->holiday_date;
            $holiday_name = $data->holiday_name;

            $sql = "INSERT INTO holiday (holiday_date, holiday_name) VALUES (:holiday_date, :holiday_name)";
            $query = $conn->prepare($sql);
            $query->bindParam(':holiday_date', $holiday_date);
            $query->bindParam(':holiday_name', $holiday_name);
            $query->execute();

            echo json_encode(array('status' => true, 'message' => 'เพิ่มวันหยุดสำเร็จ'));
        } elseif ($act == 'delete') {
            $id = $data->id;
            $sql = "DELETE FROM holiday WHERE id = :id";
            $query = $conn->prepare($sql);
            $query->bindParam(':id', $id);
            $query->execute();

            echo json_encode(array('status' => true, 'message' => 'ลบวันหยุดสำเร็จ'));
        }
    } catch (PDOException $e) {
        echo json_encode(array('status' => false, 'message' => $e->getMessage()));
    }
}
?>
