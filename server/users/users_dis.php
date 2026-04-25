<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datas = array();

    try {
        $sql = "SELECT u.username, u.status, p.*
                FROM profile AS p 
                INNER JOIN `user` AS u ON u.id = p.user_id
                WHERE u.status <> 10
                ORDER BY p.st ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
            foreach ($result as $rs) {
                array_push($datas, array(
                    'uid' => $rs->user_id,
                    'username' => $rs->username,
                    'name' => $rs->fname . $rs->name . ' ' . $rs->sname,
                    'dep' => $rs->dep,
                    'status' => $rs->status,
                ));
            }
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'Success', 'data' => $datas));
            exit;
        }

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'No data found'));
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'An error occurred: ' . $e->getMessage()));
        exit;
    }
}
?>
