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

    $datas  = array();    
    $act    = $data->act;

    try{
        if($act == 'insert'){
            $ven_user   = $data->ven_user; 
            $order      = $ven_user->order;
            $user_id    = $ven_user->user_id;
            $vn_id      = $ven_user->vn_id;
            $vns_id     = $ven_user->vns_id;

            $sql    = "SELECT fname, name, sname FROM profile WHERE user_id =:user_id";
            $query  = $conn->prepare($sql);
            $query->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);

            $comment    = "";
            $create_at  = Date("Y-m-d h:i:s");

            $sql = "INSERT INTO ven_user(user_id, `order`, vn_id, vns_id, comment, create_at) 
                    VALUE(:user_id, :order, :vn_id, :vns_id, :comment, :create_at);";        
            $query = $conn->prepare($sql);
            $query->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $query->bindParam(':order',$order, PDO::PARAM_INT);
            $query->bindParam(':vn_id',$vn_id, PDO::PARAM_INT);
            $query->bindParam(':vns_id',$vns_id, PDO::PARAM_INT);
            $query->bindParam(':comment',$comment, PDO::PARAM_STR);
            $query->bindParam(':create_at',$create_at, PDO::PARAM_STR);
            $query->execute();

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ok', 'responseJSON' => $data));
            exit;                
        }    
        if($act == 'update'){
            $ven_user   = $data->ven_user; 
            $vu_id         = $ven_user->vu_id;
            $user_id    = $ven_user->user_id;
            $order      = $ven_user->order;
            $vn_id      = $ven_user->vn_id;
            $vns_id     = $ven_user->vns_id;

            $sql = "UPDATE ven_user 
                    SET 
                        user_id =:user_id, 
                        `order` =:order 
                    WHERE vu_id = :vu_id";   

            $query = $conn->prepare($sql);
            $query->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $query->bindParam(':order',$order, PDO::PARAM_INT);
            $query->bindParam(':vu_id',$vu_id, PDO::PARAM_INT);
            $query->execute();    
            if($query->execute()){
                http_response_code(200);
                echo json_encode(array('status' => true, 'message' => 'update ok'));
                exit; 
            }
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'not update'));
            exit; 
        }  
        if($act == 'delete'){
            $vu_id  = $data->id;
            $sql = "DELETE FROM ven_user WHERE vu_id = $vu_id";
            if($conn->exec($sql)){
                http_response_code(200);
                echo json_encode(array('status' => true, 'message' => 'DEL ok'));
                exit;               
                
            };
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'not work'));
            exit;               
        }  
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'no content'));
        exit;  
        
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}



