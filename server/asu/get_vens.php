<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $datas = array();

    try{
        $sql = "SELECT v.id, v.ven_date, v.ven_time, p.name, p.sname FROM ven as v 
        INNER JOIN `profile` as p ON v.user_id = p.user_id
        WHERE v.status = 1 OR v.status = 2 AND p.`status` = 10
        ORDER BY v.ven_date DESC, v.ven_time ASC
        LIMIT 800";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                        //count($result)  for odbc
            foreach($result as $rs){
                
                array_push($datas,array(
                    'id'    => $rs->id,
                    'title' => $rs->name. ' '. $rs->sname,
                    'start' => $rs->ven_date.' '.$rs->ven_time
                ));
            }
            http_response_code(200);
            echo json_encode(array('status' => true, 'massege' => 'success', 'respJSON' => $datas));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'massege' => 'No data found ', 'respJSON' => $datas));
        exit;

    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'massege' => 'Error: ' . $e->getMessage()));
        exit;
    }
}


