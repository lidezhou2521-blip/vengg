<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

require_once "../connect.php";
require_once "../function.php";

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($data) || !is_array($data) || !isset($data['user'])) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Invalid data provided'));
        exit;
    }

    $user = $data['user'];

    $username = $user['username'];

    try {
        $sql = "SELECT id FROM user WHERE username = :username";
        $query = $conn->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if (!empty($result)) {
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'Username already exists'));
            exit;
        }

        $st = intval($user['st'] ?? 0);
        
        $password = $user['password'];
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $conn->beginTransaction();

        $date_time = date("Y-m-d H:i:s"); // กำหนดค่าวันที่และเวลาที่สร้างผู้ใช้งานใหม่

        $sql = "INSERT INTO user(username, password_hash, role, status, created_at, updated_at) 
                VALUES (:username, :password_hash, 1, 10, :created_at, :updated_at)";
        $query = $conn->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
        $query->bindParam(':created_at', $date_time, PDO::PARAM_STR);
        $query->bindParam(':updated_at', $date_time, PDO::PARAM_STR);
        $query->execute();

        $id = $conn->lastInsertId(); // ดึง id ที่เพิ่มล่าสุด

        $sql = "INSERT INTO profile(id, user_id, fname, name, sname, dep, workgroup, phone, bank_account, bank_comment, st, status, created_at, updated_at) 
                VALUES (:id, :user_id, :fname, :name, :sname, :dep, :workgroup, :phone, :bank_account, :bank_comment, :st, 10, :created_at, :updated_at)";
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->bindParam(':user_id', $id, PDO::PARAM_INT);
        $query->bindParam(':fname', $user['fname'], PDO::PARAM_STR);
        $query->bindParam(':name', $user['name'], PDO::PARAM_STR);
        $query->bindParam(':sname', $user['sname'], PDO::PARAM_STR);
        $query->bindParam(':dep', $user['dep'], PDO::PARAM_STR);
        $query->bindParam(':workgroup', $user['workgroup'], PDO::PARAM_STR);
        $query->bindParam(':phone', $user['phone'], PDO::PARAM_STR);
        $query->bindParam(':bank_account', $user['bank_account'], PDO::PARAM_STR);
        $query->bindParam(':bank_comment', $user['bank_comment'], PDO::PARAM_STR);
        $query->bindParam(':st', $st, PDO::PARAM_INT);
        $query->bindParam(':created_at', $date_time, PDO::PARAM_STR);
        $query->bindParam(':updated_at', $date_time, PDO::PARAM_STR);
        $query->execute();

        $conn->commit();

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'Success', 'data' => $data, '$id' => $id));
        exit;

    } catch (PDOException $e) {
        $conn->rollback();
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Error occurred: ' . $e->getMessage()));
        exit;
    }
}
?>
