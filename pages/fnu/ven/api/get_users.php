<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../../../../server/authen.php';
require_once '../../../../server/connect.php';

try {
    if (!isset($conn)) {
        throw new Exception("Database connection variable (\$conn) not found.");
    }

    $query = "SELECT user_id as id, name, fname, sname, workgroup 
              FROM profile 
              WHERE status = 10 
              ORDER BY fname ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(array("status" => true, "users" => $users));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array("status" => false, "message" => $e->getMessage()));
}
?>
