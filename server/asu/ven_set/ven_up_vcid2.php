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
    
    
    $sms = ''; 
    $datas = array();   

    try{        
        
        $data_event = $data->data_event; 

        $id         = $data_event->id;            
        $ven_com_idb = $data_event->ven_com_idb ;       

        $sql = "UPDATE ven SET ven_com_idb=:ven_com_idb  WHERE id = :id";   

        $query = $conn->prepare($sql);
        $query->bindParam(':ven_com_idb',$ven_com_idb, PDO::PARAM_STR);
        $query->bindParam(':id',$id, PDO::PARAM_INT);
        $query->execute();

        if($sms ==''){$sms='ok.';}

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => $sms));
        exit;  
    
        
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}




