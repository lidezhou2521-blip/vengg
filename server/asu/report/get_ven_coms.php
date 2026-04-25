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

    try{
       

        $res_g = array();
        $sql = "SELECT ven_com.*, ven_name.name AS vn_name, ven_name.DN 
                FROM ven_com
                LEFT JOIN ven_name ON ven_com.vn_id = ven_name.id 
                ORDER BY ven_month DESC, CAST(ven_com_num AS DECIMAL) 
                LIMIT 100
                ";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                        //count($result)  for odbc  
            $month = '';          
                        
            foreach($result as $rs){

                if($rs->ven_month != $month){
                    array_push($res_g,array(
                        'ven_month'=> $rs->ven_month,
                        'ven_month_th'=> DateThai_MY($rs->ven_month)
                    ));
                    $month = $rs->ven_month;
                }
                
                if($rs->ven_month == $month){
                    array_push($datas,array(
                        'id'  => $rs->id,
                        'ven_month'    => $rs->ven_month,
                        'ven_com_num'  => $rs->ven_com_num,
                        'ven_com_date' => $rs->ven_com_date,
                        'ven_com_date_th' => DateThai_full($rs->ven_com_date),
                        'ven_name'  => $rs->vn_name,
                        'status'    => $rs->status
                    ));  
                }              
            }
            
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สำเร็จ', 'respJSON' => $datas, 'respJSON_G' => $res_g ));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'ไม่พบข้อมูล '));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}