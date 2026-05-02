<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../../../server/authen.php';
require_once '../../../../server/connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id) && !empty($data->new_user_id)) {
    try {
        if (!isset($conn)) {
            throw new Exception("Database connection variable (\$conn) not found.");
        }
        $db = $conn;

        $query = "UPDATE ven SET user_id = :new_user_id WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':new_user_id', $data->new_user_id);
        $stmt->bindParam(':id', $data->id);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(array("status" => true, "message" => "เปลี่ยนตัวผู้อยู่เวรสำเร็จ"));
        } else {
            http_response_code(200);
            echo json_encode(array("status" => false, "message" => "ไม่สามารถเปลี่ยนข้อมูลได้"));
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("status" => false, "message" => "Error: " . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array("status" => false, "message" => "ข้อมูลไม่ครบถ้วน"));
}
?>
