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
    $act = $data->act;

    try{
        if($act == 'insert'){
            $vc      = $data->vc; 

            $id = time();
            $ven_com_num    = $vc->ven_com_num;
            $ven_com_date   = $vc->ven_com_date;
            $ven_month      = $vc->ven_month;
            $vn_id          = $vc->vn_id;
            $ref            = generateRandomString();
            $status         = 1 ;

            $sql = "INSERT INTO ven_com(id, ven_com_num, ven_com_date, ven_month, vn_id, ref, `status`) 
                    VALUE(:id, :ven_com_num, :ven_com_date, :ven_month, :vn_id, :ref, :status);";        
            $query = $conn->prepare($sql);
            $query->bindParam(':id',$id, PDO::PARAM_INT);
            $query->bindParam(':ven_com_num',$ven_com_num, PDO::PARAM_STR);
            $query->bindParam(':ven_com_date',$ven_com_date, PDO::PARAM_STR);
            $query->bindParam(':ven_month',$ven_month, PDO::PARAM_STR);
            $query->bindParam(':vn_id',$vn_id, PDO::PARAM_INT);
            $query->bindParam(':ref',$ref , PDO::PARAM_STR);
            $query->bindParam(':status',$status , PDO::PARAM_INT);
            $query->execute();

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ok', 'responseJSON' => $vc));
            exit;                
        }    
        if($act == 'update'){
            $vc   = $data->vc;
            
            $id             = $vc->id;
            $ven_com_num    = $vc->ven_com_num;
            $ven_com_date   = $vc->ven_com_date;
            $ven_month      = $vc->ven_month;
            $vn_id          = $vc->vn_id;

            $create_at  = Date("Y-m-d h:i:s");

            $sql = "UPDATE ven_com 
                    SET 
                        ven_com_num=:ven_com_num, 
                        ven_com_date=:ven_com_date, 
                        ven_month=:ven_month, 
                        vn_id=:vn_id 
                    WHERE id = :id";   

            $query = $conn->prepare($sql);
            $query->bindParam(':ven_com_num',$ven_com_num, PDO::PARAM_STR);
            $query->bindParam(':ven_com_date',$ven_com_date, PDO::PARAM_STR);
            $query->bindParam(':ven_month',$ven_month, PDO::PARAM_STR);
            $query->bindParam(':vn_id',$vn_id, PDO::PARAM_INT);
            $query->bindParam(':id',$id, PDO::PARAM_INT);
            $query->execute();

            $sql = "UPDATE ven
                    SET 
                        ven_com_num_all=:ven_com_num_all,
                        ven_month=:ven_month
                    WHERE ven_com_idb = :ven_com_idb";   

            $query = $conn->prepare($sql);
            $query->bindParam(':ven_com_num_all',$ven_com_num, PDO::PARAM_STR);
            $query->bindParam(':ven_month',$ven_month, PDO::PARAM_STR);
            $query->bindParam(':ven_com_idb',$id, PDO::PARAM_INT);
            $query->execute();
           
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ok', 'responseJSON' => $datas));
            exit;                
        }  
        if($act == 'delete'){
            $id     = $data->id;

            $sql = "SELECT * FROM ven WHERE ven_com_idb=:id AND (status=1 OR status=2)";
            $query = $conn->prepare($sql);
            $query->bindParam(':id',$id, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_OBJ);

            if($query->rowCount() > 0){
                http_response_code(200);
                echo json_encode(array('status' => false, 'message' => 'ไม่สามารถลบได้ คำสั่งนี้มีการจัดเวรแล้ว'));
                exit;   
            }else{
                $sql = "DELETE FROM ven_com WHERE id = $id";
                $conn->exec($sql);
                http_response_code(200);
                echo json_encode(array('status' => true, 'message' => 'DEL ok'));
                exit;                

            }

        }  
        if($act == 'status'){
            $id     = $data->id;
            $st     = $data->st;
            $sql = "UPDATE ven_com SET `status`= $st WHERE id = $id";
            $conn->exec($sql);

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ok'));
            exit;                
        }  
        
        
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}




