<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datas = array();

    try{
        $vn_id = $data->vn_id;

        $sql = "SELECT 
                    vn.*, 
                    vns.*,
                    vns.id AS vns_id,
                    vn.id AS vn_id,
                    vn.srt AS vn_srt,
                    vns.srt AS vns_srt
                FROM ven_name AS vn
                INNER JOIN ven_name_sub AS vns ON vn.id = vns.ven_name_id 
                WHERE vns.ven_name_id =:id 
                ORDER BY vns.srt ASC";
        $query = $conn->prepare($sql);
        $query->bindParam(':id',$vn_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                       
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สำเร็จ', 'respJSON' => $result));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล'));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}