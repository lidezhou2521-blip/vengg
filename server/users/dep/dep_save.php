<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";
require_once('../../authen.php'); 

$data = json_decode(file_get_contents("php://input"));


// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $act = $data->act;
       
    $datas = array();
    try{
        if($act =='insert'){
            $form = $data->form;

            $sql = "INSERT INTO dep(name) 
                    VALUE('$form->name');";   
            $query = $conn->prepare($sql);
            $query->execute();
            if($query->rowCount()){ 
                http_response_code(200);
                echo json_encode(array('status' => true, 'message' => 'บันทึกข้อมูลสำเร็จ'));
                exit;            
            }
        }
        if($act == 'update'){ 
            $form = $data->form;

            $sql = "UPDATE dep SET 
                        name = :name
                        WHERE id = :id";
            $query = $conn->prepare($sql);
            $query->bindParam('name',$form->name, PDO::PARAM_STR);
            $query->bindParam('id',$form->id, PDO::PARAM_INT);
            $query->execute();
            if($query->rowCount()){ 
                http_response_code(200);
                echo json_encode(array('status' => true, 'message' => 'บันทึกข้อมูลสำเร็จ'));
                exit;            
            }
        }
        
        if($act == 'del'){            
            $sql = "DELETE FROM dep WHERE id = :id";
            $query = $conn->prepare($sql);
            $query->bindParam('id',$data->id, PDO::PARAM_INT);
            $query->execute();
            if($query->rowCount()){ 
                http_response_code(200);
                echo json_encode(array('status' => true, 'message' => 'ลบข้อมูลสำเร็จ'));
                exit;            
            }
        }
        


        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => '------'));
        exit;
       
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'ERROR เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}