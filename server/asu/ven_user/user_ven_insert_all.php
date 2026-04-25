<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $datas = array();    

    try{   
        $sb_uvn = $data->uvn;
        $sl = strlen($sb_uvn);
        
        if($sl > 30){
            $sb_uvn = substr($data->uvn, 0, -1);
        }
        if($sb_uvn == 'ผู้พิพากษา'){
            $sql = "SELECT user_id, fname, name, sname, st FROM profile WHERE workgroup = 'ผู้พิพากษา' AND status =10 ORDER BY st ASC";
        }else{
            $sql = "SELECT user_id, fname, name, sname, st FROM profile WHERE workgroup <> 'ผู้พิพากษา' AND status =10 ORDER BY st ASC";
        }

        $query = $conn->prepare($sql);
        // $query->bindParam(':user_id',$user_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        $create_at  = Date("Y-m-d h:i:s");

        foreach($result as $rs){
            $user_id    = $rs->user_id;
            $u_name     = $rs->fname.$rs->name.' '.$rs->sname;
            $order      = $rs->st;
            $ven_name   = $data->ven_name; 
            $uvn        = $data->uvn;
            $DN         = $data->DN;
            $v_time     = $data->v_time;
            $price      = $data->price;
            $color      = $data->color;

            $sql = "INSERT INTO ven_user(user_id, u_name, `order`, ven_name, uvn, DN, v_time, price, color, create_at) 
                    VALUE(:user_id, :u_name, :order, :ven_name, :uvn, :DN, :v_time, :price, :color, :create_at);";        
            $query = $conn->prepare($sql);
            $query->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $query->bindParam(':u_name',$u_name, PDO::PARAM_STR);
            $query->bindParam(':order',$order, PDO::PARAM_INT);
            $query->bindParam(':ven_name',$ven_name, PDO::PARAM_STR);
            $query->bindParam(':uvn',$uvn, PDO::PARAM_STR);
            $query->bindParam(':DN',$DN, PDO::PARAM_STR);
            $query->bindParam(':v_time',$v_time, PDO::PARAM_STR);
            $query->bindParam(':price',$price, PDO::PARAM_STR);
            $query->bindParam(':color',$color, PDO::PARAM_STR);
            $query->bindParam(':create_at',$create_at, PDO::PARAM_STR);
            $query->execute();
        }

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'ok', 'responseJSON' => $DN));
        exit;  
        
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}



