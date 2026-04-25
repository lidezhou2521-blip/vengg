<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");
date_default_timezone_set("Asia/Bangkok");

include 'vendor/autoload.php';

include_once "dbconfig.php";
include_once "./function.php";

$_count = 0;
$price_dn1_all = 0;
$price_dn2_all = 0;
$error='';

$data = json_decode(file_get_contents("php://input"));


$VEN_COM_ID = $data->ven_com_id;
$datas = array();


try{    
    
    
    $sql = "SELECT ven_date, price FROM `ven` WHERE ven_com_idb ='$VEN_COM_ID' AND (status=1 OR status=2) GROUP BY ven_date;";
    $query = $dbcon->prepare($sql);
    $query->execute();
    $day = $query->fetchAll(PDO::FETCH_OBJ);
    $day_num = count($day);


    $sql = "SELECT * FROM profile WHERE status = 10 ORDER BY st ASC";
    $query = $dbcon->prepare($sql);
    $query->execute();
    $users = $query->fetchAll(PDO::FETCH_OBJ);

    if (count($users) > 0) {

    }
        http_response_code(200);
        echo json_encode(array('status' => true, 'massege' => 'ไม่พบข้อมูล', 'responseJSON' => $datas));
    
}catch(PDOException $e){
    echo "Faild to connect to database" . $e->getMessage();
    http_response_code(400);
    echo json_encode(array('status' => false, 'massege' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
}

