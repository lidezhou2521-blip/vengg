<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";
require_once('../../authen.php');

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $data->act;
    $datas = array();

    try {
        switch ($act) {
            case 'insert':
                $line = $data->line;

                $sql = "INSERT INTO line(name, token, status) 
                        VALUE(:name, :token, 1)";
                $query = $conn->prepare($sql);
                $query->bindParam(':name', $line->name, PDO::PARAM_STR);
                $query->bindParam(':token', $line->token, PDO::PARAM_STR);

                if ($query->execute()) {
                    http_response_code(200);
                    echo json_encode(array('status' => true, 'message' => 'บันทึกข้อมูลสำเร็จ'));
                    exit;
                }
                break;

            case 'update':
                $line = $data->line;

                $sql = "UPDATE line SET 
                            name = :name,
                            token = :token
                            WHERE id = :id";
                $query = $conn->prepare($sql);
                $query->bindParam(':name', $line->name, PDO::PARAM_STR);
                $query->bindParam(':token', $line->token, PDO::PARAM_STR);
                $query->bindParam(':id', $line->id, PDO::PARAM_INT);

                if ($query->execute()) {
                    http_response_code(200);
                    echo json_encode(array('status' => true, 'message' => 'บันทึกข้อมูลสำเร็จ'));
                    exit;
                }
                break;

            case 'set_st':
                $sql = "UPDATE line SET 
                            status = :status
                            WHERE id = :id";
                $query = $conn->prepare($sql);
                $query->bindParam(':status', $data->st, PDO::PARAM_INT);
                $query->bindParam(':id', $data->id, PDO::PARAM_INT);

                if ($query->execute()) {
                    http_response_code(200);
                    echo json_encode(array('status' => true, 'message' => 'บันทึกข้อมูลสำเร็จ'));
                    exit;
                }
                break;

            case 'del':
                $sql = "DELETE FROM line WHERE id = :id";
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $data->id, PDO::PARAM_INT);

                if ($query->execute()) {
                    http_response_code(200);
                    echo json_encode(array('status' => true, 'message' => 'ลบข้อมูลสำเร็จ'));
                    exit;
                }
                break;
        }

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => '------'));
        exit;
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'ERROR เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}
