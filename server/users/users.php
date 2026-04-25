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
                ORDER BY p.st ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
            $datas = array();
            foreach ($result as $rs) {
                $img_link = '../../assets/images/profiles/nopic.png';
                if ($rs->img && file_exists('../../uploads/users/' . $rs->img)) {
                    $img_link = '../../uploads/users/' . $rs->img;
                }
                $data = array(
                    'uid' => $rs->user_id,
                    'username' => $rs->username,
                    'name' => $rs->fname . $rs->name . ' ' . $rs->sname,
                    'dep' => $rs->dep,
                    'img' => $img_link,
                    'status' => $rs->status,
                    'st' => $rs->st,
                );
                $datas[] = $data;
            }
            $response = array(
                'status' => true,
                'message' => 'Success',
                'data' => $datas
            );
        } else {
            
            $response = array(
                'status' => false,
                'message' => 'No data found'
            );
        }

        http_response_code(200);
        echo json_encode($response);
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array('status' => false, 'message' => 'An error occurred: ' . $e->getMessage()));
        exit;
    }
}
?>
