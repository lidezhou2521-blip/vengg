<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";


// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datas = array();
    $judge = array();
    $not_judge = array();

    try{
        $sql = "SELECT p.*
                FROM profile as p 
                -- INNER JOIN `user` as u ON u.id = p.user_id
                WHERE p.status = 10
                ORDER BY p.name ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                       
            foreach($result as $rs){
                if($rs->workgroup == 'ผู้พิพากษา'){
                    array_push($judge,array(
                        'uid' => $rs->user_id,
                        'name'  => $rs->name.' '.$rs->sname,
                        'dep'   => $rs->dep,
                        'workgroup' => $rs->workgroup,
                        'status'   => $rs->status
                    ));
                }else{
                    array_push($not_judge,array(
                        'uid' => $rs->user_id,
                        'name'  => $rs->name.' '.$rs->sname,
                        'dep'   => $rs->dep,
                        'workgroup' => $rs->workgroup,
                        'status'   => $rs->status
                    ));
                }
                array_push($datas,array(
                    'uid' => $rs->user_id,
                    // 'username' => $rs->username,
                    'name'  => $rs->name.' '.$rs->sname,
                    'dep'   => $rs->dep,
                    'workgroup' => $rs->workgroup,
                    'status'   => $rs->status
                ));
            }
            http_response_code(200);
            echo json_encode(array(
                'status' => true, 
                'message' => 'สำเร็จ', 
                'respJSON' => $datas,
                'judge' => $judge,
                'not_judge' => $not_judge,
            ));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array(
            'status' => false, 
            'message' => 'ไม่พบข้อมูล ',
            'respJSON' => $datas,
            'judge' => $judge,
            'not_judge' => $not_judge
        ));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}