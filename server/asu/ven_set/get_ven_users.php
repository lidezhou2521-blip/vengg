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

    try{

        $vn_id      = $data->vn_id;
        $vns_id     = $data->vns_id;
        
        $sql = "SELECT vu.*, p.fname, p.name, p.sname 
                FROM ven_user AS vu
                INNER JOIN `profile` AS p ON vu.user_id = p.user_id
                WHERE vu.vn_id = :vn_id AND vu.vns_id =:vns_id 
                ORDER BY vu.order ASC";

        $query = $conn->prepare($sql);
        $query->bindParam(':vn_id',$vn_id, PDO::PARAM_INT);
        $query->bindParam(':vns_id',$vns_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        foreach($result AS $rs){
            array_push($datas,array(
                'uid'   => $rs->user_id,
                'u_name'    => $rs->fname.$rs->name.' '.$rs->sname,
                'vn_id'     => $rs->vn_id,
                'vns_id'    => $rs->vns_id,
                'order'     => $rs->order,
            ));
        }

        if($query->rowCount() > 0){
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สำเร็จ', 'respJSON' => $datas));
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