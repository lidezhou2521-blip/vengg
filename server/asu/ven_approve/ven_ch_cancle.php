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
    $id = $data->id;
    $datas = array();

    try{
        $conn->beginTransaction();
      
        $sql = "UPDATE ven_change as vc
        LEFT JOIN ven AS v1 ON v1.id = vc.ven_id1
        LEFT JOIN ven AS v2 ON v2.id = vc.ven_id2
        LEFT JOIN ven AS v1o ON v1o.id = vc.ven_id1_old
        LEFT JOIN ven AS v2o ON v2o.id = vc.ven_id2_old								
        SET 
            vc.`status` = 2,
            v1.`status` = 2,
            v2.`status` = 2,
            v1o.`status` = 4,
            v2o.`status` = 4
        WHERE vc.id = :id";
        $query2 = $conn->prepare($sql);
        $query2->bindParam(':id',$id, PDO::PARAM_STR);
        $query2->execute();
        $conn->commit();

        if($query2->rowCount()){                
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สำเร็จ',));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล'));
        exit;
    
    }catch(PDOException $e){
        $conn->rollback();
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}