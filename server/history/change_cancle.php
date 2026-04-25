<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $data->id;
    

    $datas = array();

    // The request is using the POST method
    try{
        
        $sql = "SELECT vc.id, v1o.gcal_id as gcal_id1, v2o.gcal_id as gcal_id2
                FROM ven_change as vc  
                LEFT JOIN ven AS v1o ON v1o.id = vc.ven_id1_old
                LEFT JOIN ven AS v2o ON v2o.id = vc.ven_id2_old
                WHERE vc.id = :id ";

                $query = $conn->prepare($sql);
                $query->bindParam(':id',$id, PDO::PARAM_STR);
                $query->execute();
                $result = $query->fetch(PDO::FETCH_OBJ);


        if($query->rowCount() > 0){                        
            
            $conn->beginTransaction();
            $sql = "UPDATE ven_change as vc
                    LEFT JOIN ven AS v1 ON v1.id = vc.ven_id1
                    LEFT JOIN ven AS v2 ON v2.id = vc.ven_id2
                    LEFT JOIN ven AS v1o ON v1o.id = vc.ven_id1_old
                    LEFT JOIN ven AS v2o ON v2o.id = vc.ven_id2_old								
                    SET 
                        vc.`status` = 77,
                        v1.`status` = 77,
                        v2.`status` = 77,
                        v1o.`status` = 1,
                        v2o.`status` = 1
                    WHERE vc.id = :id";
            $query2 = $conn->prepare($sql);
            $query2->bindParam(':id',$id, PDO::PARAM_STR);
            $query2->execute();
            
            $conn->commit();

            if($query2->rowCount()){   

                /**google calendar */
                if(__GOOGLE_CALENDAR__){
                    
                    if($result->gcal_id1){
                        $sql_V = "SELECT v.ven_com_name, p.fname, p.`name`, p.sname 
                                    FROM ven AS v
                                    INNER JOIN `profile` AS p ON v.user_id = p.id 
                                    WHERE v.gcal_id = '$result->gcal_id1' AND (v.status=1 OR v.status=2)
                                    ORDER BY v.ven_time ASC";
                        $query_V = $conn->prepare($sql_V);
                        $query_V->execute();
                        if($query_V->rowCount()){
                            $res_V = $query_V->fetchAll(PDO::FETCH_OBJ);
                            $name = $res_V[0]->ven_com_name."\n";
                            $sms = '';
                            foreach($res_V as $v){
                                $sms .= $v->fname.$v->name.' '.$v->sname."\n";
                            }
                            gcal_update($result->gcal_id1, $name, $sms, 1);
                        }
                    }
                    if($result->gcal_id2){
                        $sql_V = "SELECT v.ven_com_name, p.fname, p.`name`, p.sname 
                                    FROM ven AS v
                                    INNER JOIN `profile` AS p ON v.user_id = p.id 
                                    WHERE v.gcal_id = '$result->gcal_id2' AND (v.status=1 OR v.status=2)
                                    ORDER BY v.ven_time ASC";
                        $query_V = $conn->prepare($sql_V);
                        $query_V->execute();
                        if($query_V->rowCount()){
                            $res_V = $query_V->fetchAll(PDO::FETCH_OBJ);
                            $name = $res_V[0]->ven_com_name."\n";
                            $sms = '';
                            foreach($res_V as $v){
                                $sms .= $v->fname.$v->name.' '.$v->sname."\n";
                            }
                            gcal_update($result->gcal_id2, $name, $sms, 1);
                        }

                    }
                }

                //ส่ง line ot ven_admin
                $sql = "SELECT token FROM line WHERE name = 'ven_admin' AND status=1";
                $query_line = $conn->prepare($sql);
                $query_line->execute();
                $res = $query_line->fetch(PDO::FETCH_OBJ);
                if($query_line->rowCount()){
                    $sToken = $res->token;
                    $sMessage = 'มีการยกเลิกเวร '.$id."\n";
                    $res_line = sendLine($sToken,$sMessage);
                }
                http_response_code(200);
                echo json_encode(array('status' => true, 'message' => 'สำเร็จ'));
                exit;
            }else{
                http_response_code(200);
                echo json_encode(array('status' => false, 'message' => 'ไม่พบใบเปลี่ยนเวรนี้'));
                exit;
            }  
            
        }else{
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'ไม่พบใบเปลี่ยนเวรนี้'));
            exit;
        }
    
    }catch(Exception $e){
        $conn->rollback();
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}


