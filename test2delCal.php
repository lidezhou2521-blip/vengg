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

	
}    
