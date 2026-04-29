<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sms_err = array();
    $_ven_com_id = '';
    
    if(isset($data->ven_com_id)){
        $_ven_com_id = cleanData($data->ven_com_id);
    } else {
        array_push($sms_err,'ven_com_id : null');
    }    

    if($sms_err){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => implode(", ", $sms_err)));
        exit;
    }
    
    try {
        $sql = "UPDATE ven SET status = 1 WHERE status = 2 AND ven_com_idb = :ven_com_idb";
        $query = $conn->prepare($sql);   
        $query->bindParam(':ven_com_idb', $_ven_com_id, PDO::PARAM_STR);   
        
        if ($query->execute()) {
            $count = $query->rowCount();
            if ($count > 0) {
                echo json_encode(array(
                    'status' => true,
                    'message' => "อนุมัติเวรจำนวน $count รายการเรียบร้อยแล้ว"
                ));
            } else {
                echo json_encode(array(
                    'status' => true,
                    'message' => "ไม่มีเวรที่ต้องอนุมัติ (หรืออนุมัติไปแล้ว)"
                ));
            }
        } else {
            echo json_encode(array('status' => false, 'message' => 'ไม่สามารถอนุมัติเวรได้'));
        }
        exit;      
    } catch(PDOException $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()));
        exit;
    }
}
