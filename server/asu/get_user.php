<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $data->uid;
    
    $datas = array();
    

    try{
        $sql = "SELECT u.username,p.*
                FROM profile as p 
                INNER JOIN `user` as u ON u.id = p.user_id
                WHERE u.id = :uid 
                ORDER BY p.st ASC";
        $query = $conn->prepare($sql);
        $query->bindParam('uid',$uid, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                     
            
            http_response_code(200);
            echo json_encode(array('status' => true, 'messsge' => 'สำเร็จ', 'respJSON' => $result));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'messsge' => 'ไม่พบข้อมูล'));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'messsge' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}