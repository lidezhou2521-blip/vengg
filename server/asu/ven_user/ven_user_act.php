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
        if($act == 'delete_group'){
            $vn_id = $data->vn_id;
            $vns_id = $data->vns_id;
            $sql = "DELETE FROM ven_user WHERE vn_id = :vn_id AND vns_id = :vns_id";
            $q = $conn->prepare($sql);
            $q->bindParam(':vn_id', $vn_id, PDO::PARAM_INT);
            $q->bindParam(':vns_id', $vns_id, PDO::PARAM_INT);
            $q->execute();
            $count = $q->rowCount();
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => "ลบผู้อยู่เวร $count คนเรียบร้อย"));
            exit;
        }
        if($act == 'insert_all'){
            $ven_user = $data->ven_user;
            $vn_id    = $ven_user->vn_id;
            $vns_id   = $ven_user->vns_id;
            $create_at = Date("Y-m-d H:i:s");

            // Get all active users
            $sql = "SELECT p.user_id, p.fname, p.name, p.sname, p.st 
                    FROM profile AS p 
                    INNER JOIN user AS u ON u.id = p.user_id 
                    WHERE u.status = 10 
                    ORDER BY p.st ASC";
            $query = $conn->prepare($sql);
            $query->execute();
            $users = $query->fetchAll(PDO::FETCH_OBJ);

            $count = 0;
            foreach($users as $idx => $u){
                // Check if already exists
                $chk = $conn->prepare("SELECT vu_id FROM ven_user WHERE user_id = :uid AND vn_id = :vn_id AND vns_id = :vns_id");
                $chk->bindParam(':uid', $u->user_id, PDO::PARAM_INT);
                $chk->bindParam(':vn_id', $vn_id, PDO::PARAM_INT);
                $chk->bindParam(':vns_id', $vns_id, PDO::PARAM_INT);
                $chk->execute();
                if($chk->rowCount() > 0) continue;

                $order = $u->st > 0 ? $u->st : ($idx + 1);
                $sql = "INSERT INTO ven_user(user_id, `order`, vn_id, vns_id, comment, create_at) 
                        VALUES(:user_id, :order, :vn_id, :vns_id, '', :create_at)";
                $q = $conn->prepare($sql);
                $q->bindParam(':user_id', $u->user_id, PDO::PARAM_INT);
                $q->bindParam(':order', $order, PDO::PARAM_INT);
                $q->bindParam(':vn_id', $vn_id, PDO::PARAM_INT);
                $q->bindParam(':vns_id', $vns_id, PDO::PARAM_INT);
                $q->bindParam(':create_at', $create_at, PDO::PARAM_STR);
                $q->execute();
                $count++;
            }
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => "เพิ่มผู้อยู่เวร $count คนเรียบร้อย"));
            exit;
        }

        if($act == 'reorder'){
            $updates = $data->updates;
            foreach($updates as $item){
                $sql = "UPDATE ven_user SET `order` = :order WHERE vu_id = :vu_id";
                $q = $conn->prepare($sql);
                $q->bindParam(':order', $item->order, PDO::PARAM_INT);
                $q->bindParam(':vu_id', $item->vu_id, PDO::PARAM_INT);
                $q->execute();
            }
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สลับลำดับเรียบร้อย'));
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



