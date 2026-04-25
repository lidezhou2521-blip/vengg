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
        $id     = $data->id;

        $sql = "SELECT * FROM ven_change 
                WHERE (ven_id1 = :id1 OR ven_id2 = :id2 OR ven_id1_old = :id3 OR ven_id2_old = :id4) 
                AND (status = 1 OR status = 2)";
        $query = $conn->prepare($sql);
        $query->bindParam(':id1', $id, PDO::PARAM_INT);
        $query->bindParam(':id2', $id, PDO::PARAM_INT);
        $query->bindParam(':id3', $id, PDO::PARAM_INT);
        $query->bindParam(':id4', $id, PDO::PARAM_INT);
        $query->execute();
        $res = $query->fetch(PDO::FETCH_OBJ);

        if($res){

            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'ไม่สามารถลบได้เนื่องจากมีรายชื่อในใบเปลี่ยนเวร'));
            exit;                

        }else{

            $sql = "SELECT * FROM ven WHERE id = :id";
            $query = $conn->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $rs = $query->fetch(PDO::FETCH_OBJ);

            if ($rs) {
                // รายละเอียดโค้ดที่ต้องการทำเมื่อมีข้อมูล
                $sql = "DELETE FROM ven WHERE id = :id";
                $query = $conn->prepare($sql);
                $query->bindParam(':id', $id, PDO::PARAM_INT);
                $query->execute();

                if(__GOOGLE_CALENDAR__){  
                       
                    $sql_v = "SELECT v.*, v.id, v.user_id, v.ven_com_idb, v.ven_date, v.ven_time, v.gcal_id, p.fname, p.name, p.sname, vn.`name` AS vn_name
                            FROM ven AS v
                            INNER JOIN `profile` AS p ON v.user_id = p.id
                            INNER JOIN ven_name AS vn ON v.vn_id = vn.id
                            WHERE v.gcal_id = :gcal_id
                            AND (v.status = 1 OR v.status = 2)
                            ORDER BY v.ven_time ASC";
                    $query_V = $conn->prepare($sql_v);
                    $query_V->bindParam(':gcal_id', $rs->gcal_id, PDO::PARAM_STR);
                    $query_V->execute();
                    $res_V = $query_V->fetchAll(PDO::FETCH_OBJ);
    
                    if (count($res_V)) {
                        /** เตรียมข้อมูลสำหรับส่ง */
                        $name = "(เวร)" . $res_V[0]->ven_com_name;
                        $desc = '';
    
                        foreach ($res_V as $rs) {
                            $desc .= $rs->fname . $rs->name . ' ' . $rs->sname . "\n";
                        }
    
                        gcal_update($rs->gcal_id, $name, $desc, 1);
                    } else {
                        gcal_remove($rs->gcal_id);
                    }
                                   
                }
                
                http_response_code(200);
                echo json_encode(array(
                    'status' => true, 
                    'message' => 'DEL ok'
                    ));
                exit;   

            }            
            
        }
        
        http_response_code(200);
        echo json_encode(array(
            'status' => false, 
            'message' => 'ไม่มีการปรับปรุง'
            ));
        exit;   

        
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}




