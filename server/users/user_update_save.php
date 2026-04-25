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
    if ($_SESSION['AD_ROLE'] != 9) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Unauthorized'));
        exit;
    }

    if (!isset($data->user) || empty($data->user) || !isset($data->user->user_id) || empty($data->user->user_id)) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'User data not provided'));
        exit;
    }

    $user = $data->user;
    $user_id = $user->user_id;

    if (isset($user->st) && is_numeric($user->st)) {
        $st = intval($user->st);
    } else {
        $st = 0;
    }
    

    $datas = array();

    try {
        $sql = "SELECT id FROM profile WHERE user_id = :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if ($query->rowCount() === 0) {
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'No data found'));
            exit;
        }

        $date_time = Date("Y-m-d h:i:s");
        $sql = "UPDATE profile SET 
                fname = :fname,
                name = :name,
                sname = :sname,
                dep = :dep,
                workgroup = :workgroup,
                phone = :phone,
                bank_account = :bank_account,
                bank_comment = :bank_comment,
                st = :st,
                updated_at = :updated_at
                WHERE user_id = :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':fname', $user->fname, PDO::PARAM_STR);
        $query->bindParam(':name', $user->name, PDO::PARAM_STR);
        $query->bindParam(':sname', $user->sname, PDO::PARAM_STR);
        $query->bindParam(':dep', $user->dep, PDO::PARAM_STR);
        $query->bindParam(':workgroup', $user->workgroup, PDO::PARAM_STR);
        $query->bindParam(':phone', $user->phone, PDO::PARAM_STR);
        $query->bindParam(':bank_account', $user->bank_account, PDO::PARAM_STR);
        $query->bindParam(':bank_comment', $user->bank_comment, PDO::PARAM_STR);
        $query->bindParam(':st', $st, PDO::PARAM_INT);
        $query->bindParam(':updated_at', $date_time, PDO::PARAM_STR);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'Success'));
        exit;
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'ERROR occurred: ' . $e->getMessage()));
        exit;
    }
}
?>
