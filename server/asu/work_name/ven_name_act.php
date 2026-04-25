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

    $datas = array();
    $errors = array();
    $ven_name    = $data->ven_name; 
    $act         = $data->act;

    try{
        if($act == 'insert'){
            // $name   = $ven_name->name;
            // $srt    = (int)$ven_name->srt;
            // $DN     = $ven_name->DN;

            isset($ven_name->name)  ?  $name = $ven_name->name      : array_push($errors,'ชื่อ'); 
            isset($ven_name->srt)   ?  $srt = (int)$ven_name->srt   : array_push($errors,'ลำดับ'); 
            isset($ven_name->DN)    ?  $DN  = $ven_name->DN         : array_push($errors,'กลางวัน/กลางคืน'); 
            
            if(count($errors)>0){
                http_response_code(200);
                echo json_encode(array('status' => false, 'message' => $errors));
                exit;
            }

            $sql = "INSERT INTO ven_name(name, DN, srt) VALUE(:name, :DN, :srt);";        
            $query = $conn->prepare($sql);
            $query->bindParam(':name',$name, PDO::PARAM_STR);
            $query->bindParam(':DN',$DN, PDO::PARAM_STR);
            $query->bindParam(':srt',$srt, PDO::PARAM_INT);
            $query->execute();

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ok', 'responseJSON' => $datas));
            exit;                
        }    
        if($act == 'update'){
            $id     = $ven_name->id;
            $name   = $ven_name->name;
            $DN     = $ven_name->DN;
            $srt    = (int)$ven_name->srt;

            $sql = "UPDATE ven_name SET name =:name, DN=:DN, srt=:srt WHERE id = :id";   

            $query = $conn->prepare($sql);
            $query->bindParam(':name',$name, PDO::PARAM_STR);
            $query->bindParam(':DN',$DN, PDO::PARAM_STR);
            $query->bindParam(':srt',$srt, PDO::PARAM_INT);
            $query->bindParam(':id',$id, PDO::PARAM_INT);
            $query->execute();         

            $sql = "UPDATE ven SET ven_com_name =:ven_com_name, ven_name =:name, DN=:DN WHERE vn_id = :id";   

            $query = $conn->prepare($sql);
            $query->bindParam(':ven_com_name',$name, PDO::PARAM_STR);
            $query->bindParam(':name',$name, PDO::PARAM_STR);
            $query->bindParam(':DN',$DN, PDO::PARAM_STR);
            $query->bindParam(':id',$id, PDO::PARAM_INT);
            $query->execute();         

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ok', 'responseJSON' => $datas));
            exit;                
        }  
        if($act == 'delete'){
            $vn_id     = $ven_name->id;

            $sql = "DELETE FROM ven_name WHERE id = :vn_id";
            $query = $conn->prepare($sql);
            $query->bindParam(':vn_id', $vn_id, PDO::PARAM_INT);
            $query->execute();

            $sql = "DELETE FROM ven_name_sub WHERE ven_name_id = :vn_id";
            $query = $conn->prepare($sql);
            $query->bindParam(':vn_id', $vn_id, PDO::PARAM_INT);
            $query->execute();

            $sql = "DELETE FROM ven_user WHERE vn_id = :vn_id";
            $query = $conn->prepare($sql);
            $query->bindParam(':vn_id', $vn_id, PDO::PARAM_INT);
            $query->execute();


            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'DEL ok'));
            exit;                
        }   
        
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}



