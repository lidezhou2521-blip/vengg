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
    $id = $data->id;
    $datas = array();

    try{
        $conn->beginTransaction();

        $sql = "UPDATE ven_change as vc
        LEFT JOIN ven AS v1 ON v1.id = vc.ven_id1
        LEFT JOIN ven AS v2 ON v2.id = vc.ven_id2
        LEFT JOIN ven AS v1o ON v1o.id = vc.ven_id1_old
        LEFT JOIN ven AS v2o ON v2o.id = vc.ven_id2_old								
        SET 
            vc.`status` = 1,
            v1.`status` = 1,
            v2.`status` = 1,
            v1o.`status` = 4,
            v2o.`status` = 4
        WHERE vc.id = :id";
        $query2 = $conn->prepare($sql);
        $query2->bindParam(':id',$id, PDO::PARAM_STR);
        $query2->execute();

        $conn->commit();

        if($query2->rowCount()){                        //count($result)  for odbc
            $sql = "SELECT v1.gcal_id AS v1_gcal_id, v2.gcal_id AS v2_gcal_id
                    FROM ven_change as vc
                    LEFT JOIN ven AS v1 ON v1.id = vc.ven_id1
                    LEFT JOIN ven AS v2 ON v2.id = vc.ven_id2	
                    WHERE vc.id = :id";
            $query = $conn->prepare($sql);
            $query->bindParam(':id',$id, PDO::PARAM_STR);
            $query->execute();
            $res  = $query->fetch(PDO::FETCH_OBJ);
            if($query->rowCount()){ 
                
                if(__GOOGLE_CALENDAR__){
                    $sql_V = "SELECT v.*, p.fname, p.name, p.sname 
                                FROM ven AS v 
                                INNER JOIN `profile` AS p ON v.user_id = p.user_id 
                                WHERE v.gcal_id = '$res->v1_gcal_id' AND (v.status=1 OR v.status=2)
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
                        gcal_update($res->v1_gcal_id, $name, $sms, 1);
                    }
                    if($res->v2_gcal_id){

                        $sql_V = "SELECT v.*, p.fname, p.name, p.sname 
                                    FROM ven AS v 
                                    INNER JOIN `profile` AS p ON v.user_id = p.user_id 
                                    WHERE v.gcal_id = '$res->v2_gcal_id' AND (v.status=1 OR v.status=2)
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
                            gcal_update($res->v2_gcal_id, $name, $sms, 1);
                        }
                    }
                           
                }
            }

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สำเร็จ'));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่มีการปรับปรุง'));
        exit;
    
    }catch(PDOException $e){
        $conn->rollback();
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}