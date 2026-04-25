<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
// header("Content-Type: application/json; charset=utf-8");

include "./server/connect.php";
include "./server/function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	// $seconds = 100;
	// echo date("H:i:s", mktime(16, 30, $seconds)); // แสดงผลเป็นเวลาที่มีวินาทีเป็น 1

	// function generateRandomNumber($min = 1000, $max = 9999) {
	// 	return rand($min, $max);
	// }
	
	// // ตัวอย่างการใช้งาน
	// $randomNumber = generateRandomNumber();
	// echo time() . $randomNumber;

	// $name = '2222222';
	// $start = '2022-12-29 22:29:00';

	// $res = json_decode(gcal_insert($name,$start));
	// echo $res->resp->id;

	// $gcal_id = 'a3b834d8afu96vkpblrs8mr72o';
	
	// echo gcal_update($gcal_id,$name,'',5);
	
	// echo gcal_remove($gcal_id);

	// $datas =array();

	// $sql2 = "SELECT * FROM ven WHERE ven_month='2023-03' 
	// 			AND (status=1 OR status=2) 
	// 			AND (gcal_id IS NULL OR gcal_id='')
	// 			ORDER BY ven_date ASC, ven_time ASC";
	// $query_g = $conn->prepare($sql2);  
	// $query_g->execute();
	
	// if($query_g->rowCount()){
	// 	$result = $query_g->fetchAll(PDO::FETCH_OBJ);
		
	// 	$n = 0;
	// 	$ven_date = '';
	// 	$ven_time = '';
	// 	$vn = 10;
	// 	foreach($result as $rs){
	// 		$rs->DN == 'กลางวัน' ? $ven_time = '08:30:' : $ven_time = '16:30:';
	// 		if($ven_date == $rs->ven_date){
	// 			$vn++;
	// 		}
	// 		$ven_date = $rs->ven_date;
	// 		$ven_time .= (string)$vn;
			
	// 		$name = $rs->u_name;
	// 		$start = $rs->ven_date.' '.$ven_time;

	// 		$date = new DateTime($start);
	// 		$start = $date->format(DateTime::ATOM);

	// 		echo $name.' '.$start . $vn. "\n";

	// 		if(__GOOGLE_CALENDAR__){
	// 			$res = json_decode(gcal_insert($name,$start));
	// 			if($res){
	// 				$gcal_id = $res->resp->id;               
	// 				$sql = "UPDATE ven SET gcal_id =:gcal_id WHERE id = :id";    
	
	// 				$query = $conn->prepare($sql);
	// 				$query->bindParam(':gcal_id',$gcal_id, PDO::PARAM_STR);
	// 				$query->bindParam(':id',$rs->id, PDO::PARAM_INT);
	// 				$query->execute(); 
					
	// 				array_push($datas,array(
	// 					'id'    => $rs->id,
	// 					'gcal_id' => $res->resp->id        
	// 				));
	// 			}
	// 		}
	// 	}
	// }



	// $sql2 = "SELECT * FROM ven 
	// 			WHERE ven_month='2023-03' 
	// 			AND (status=1 OR status=2) 
	// 			AND (gcal_id IS NOT NULL)
	// 			ORDER BY ven_date ASC, ven_time ASC";
	// $query_g = $conn->prepare($sql2);  
	// $query_g->execute();
	
	// if($query_g->rowCount()){
	// 	$result = $query_g->fetchAll(PDO::FETCH_OBJ);		
		
	// 	foreach($result as $rs){
	// 		$gcal_id = $rs->gcal_id;
	// 		gcal_remove($gcal_id);	
	// 	}
	// }

	// 	http_response_code(200);
	// 	echo json_encode(array('status' => true, 'massege' => 'สำเร็จ', 'respJSON' => $datas));
	// 	exit;
	// }else{
	// 	http_response_code(200);
	// 	echo json_encode(array('status' => false, 'massege' => 'null',));
	// 	exit;
	// }

}    

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// 	/**
// 	 * 	post
// 	 * 
// 	 * 	token or username
// 	 * 	message
// 	 * 
// 	 */
	
// 	$date_now = date("Y-m-d H:i:s");
// 	$sMessage = '';

// 	if(isset($data->token)){
// 		$sToken 	= $data->token;
// 	}else{
// 		if(isset($data->username)){
// 			$sql = "SELECT * FROM line WHERE name = '$data->username'";
// 			$query = $conn->prepare($sql);
// 			$query->execute();
// 			$res = $query->fetch(PDO::FETCH_OBJ);
// 			$sToken = $res->token;
// 		}else{
// 			http_response_code(200);
// 			echo json_encode(array('status' => true, 'message' => 'ไม่พบข้อมูล Token'));
// 			exit;
// 		}
// 	}

// 	$sMessage .= $data->message;
// 	$sMessage .= "\n";
// 	$sMessage .= $date_now;

	
// 	http_response_code(200);
// 	echo sendLine($sToken,$sMessage);
	    
// }    

// $a = "2023-03-01 08:30:61";

// echo date("Y-m-d H:i:s",strtotime($a)). "\n";

// echo number_format("1000000",2);

function isInternetAvailable() {
    $headers = @get_headers('https://www.google.com');
    
    // Check if there are headers
    if ($headers && is_array($headers)) {
        // Iterate through headers and look for the "200 OK" status
        foreach ($headers as $header) {
            if (strpos($header, '200 OK') !== false) {
                return true; // Internet is available
            }
        }
    }
    
    return false; // Internet is not available
}

// ใช้ฟังก์ชันเพื่อตรวจสอบสถานะของการเชื่อมต่ออินเทอร์เน็ต
if (isInternetAvailable()) {
    echo "Internet is available.";
} else {
    echo "Internet is not available.";
}
?>