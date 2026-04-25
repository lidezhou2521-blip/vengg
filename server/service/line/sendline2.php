<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$date_now 		= date("Y-m-d");	
	$date_tomorrow 	= date("Y-m-d",strtotime("+1 day")); 
	$sToken 		= "";
	$sMessage 		= "";

	$sql 	= "SELECT * FROM line WHERE name = 'ven' AND status=1";
	$query 	= $conn->prepare($sql);
	$query->execute();
	$res 	= $query->fetch(PDO::FETCH_OBJ);
	
	if($query->rowCount()){
		$sToken = $res->token;
		$sMessage .= 'ตารางเวร ';	
		$sql = "SELECT v.*
				FROM ven as v
				WHERE v.ven_date >= '$date_now' AND v.ven_date <= '$date_tomorrow' AND (v.status=1 OR v.status=2)
				ORDER BY v.ven_date, v.ven_time ASC";
		$query = $conn->prepare($sql);
		$query->execute();
		$result = $query->fetchAll(PDO::FETCH_OBJ);

		$date_rs = '';
		foreach($result as $rs){
			if($date_rs != $rs->ven_date){
				$sMessage 	.= "\n".DateThai($rs->ven_date)."\n";
				$date_rs 	=  $rs->ven_date;
			}
			if(date("H:i:s") < $rs->ven_time && $date_now == $rs->ven_date){
				$rs->DN == 'กลางวัน' ? $sMessage .= "☀️ ": $sMessage .= "🌙 " ; 
				$sMessage .= $rs->u_name;

				if($rs->workgroup != 'ผู้พิพากษา'){
					// $sMessage .= "\n";
					// $sMessage .= '(โทร : ' . $rs->phone .')';
				}  
				$sMessage .= "\n";
			}else{
				$rs->DN == 'กลางวัน' ? $sMessage .= "☀️ ": $sMessage .= "🌙 " ; 
				$sMessage .= $rs->u_name;

				if($rs->workgroup != 'ผู้พิพากษา'){
					// $sMessage .= "\n";
					// $sMessage .= '(โทร : ' . $rs->phone .')';
				}  
				$sMessage .= "\n";
			}
		}
		
		http_response_code(200);
		echo sendLine($sToken,$sMessage);

	}else{
		$sql = "SELECT * FROM line WHERE name = 'admin'";
		$query = $conn->prepare($sql);
		$query->execute();
		$res = $query->fetch(PDO::FETCH_OBJ);
		$sToken = $res->token;

		$sMessage = 'ไม่สามารถแจ้งผ่านกลุ่ม ven ได้';
		http_response_code(200);
		echo sendLine($sToken,$sMessage);
	}
	    
}    

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	/**
	 * 	post
	 * 
	 * 	token or username
	 * 	message
	 * 
	 */
	
	$date_now = date("Y-m-d H:i:s");
	$sMessage = '';

	if(isset($data->token)){
		$sToken 	= $data->token;
	}else{
		if(isset($data->username)){
			$sql = "SELECT * FROM line WHERE name = '$data->username'";
			$query = $conn->prepare($sql);
			$query->execute();
			$res = $query->fetch(PDO::FETCH_OBJ);
			$sToken = $res->token;
		}else{
			http_response_code(200);
			echo json_encode(array('status' => true, 'message' => 'ไม่พบข้อมูล Token'));
			exit;
		}
	}

	$sMessage .= $data->message;
	$sMessage .= "\n";
	$sMessage .= $date_now;

	
	http_response_code(200);
	echo sendLine($sToken,$sMessage);
	    
}    
?>