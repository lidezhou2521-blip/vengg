<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");


include "../../connect.php";
include "../../function.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $sms_err = array();
    $datas = array();
    $ven_id  = array();

    if($_SESSION['AD_ROLE'] != 9){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่มีสิทธิ์'));
        exit;
    }

    try{
        $ven_month = $data->ven_month;
        $sql = "UPDATE ven SET status = 1 WHERE status = 2 AND ven_month = '$ven_month'";
        $query = $conn->prepare($sql);      
        $query->execute();  
       
        if($query->rowCount()){
            http_response_code(200);
            echo json_encode(array(
                'status'    => true, 
                'message'   => 'สำเร็จ',
                'count'     => $query->rowCount(),
            ));
            exit;
        } else {
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'ไม่มีรายการ update', 'datas'=>$datas));
            exit;
        }
       
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ERROR เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}
