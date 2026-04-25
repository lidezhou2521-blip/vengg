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
        
        $sql_wn = "SELECT 
                        ven_name.id AS vn_id, 
                        ven_name.name AS vn_name,
                        ven_name.DN,
                        ven_name.srt AS vn_srt,
                        ven_name_sub.id as vns_id,
                        ven_name_sub.name as vns_name,
                        ven_name_sub.price,
                        ven_name_sub.color,
                        ven_name_sub.srt AS vns_srt
                    FROM ven_name 
                    INNER JOIN ven_name_sub
                    ON ven_name.id = ven_name_sub.ven_name_id 
                    ORDER BY ven_name.srt ASC, ven_name_sub.srt ASC";
        $query_wn = $conn->prepare($sql_wn);
        $query_wn->execute();
        $res_wn = $query_wn->fetchAll(PDO::FETCH_OBJ);
        

        if($query_wn->rowCount() > 0){  

            foreach($res_wn as $rs_wn){

                $sql_u = "SELECT vu.*, pr.fname, pr.`name`, pr.sname
                            FROM ven_user as vu
                            INNER JOIN `profile` as pr
                            ON vu.user_id = pr.id
                            WHERE vu.vn_id = :vn_id AND vu.vns_id = :vns_id
                            ORDER BY vu.`order` ASC;";
                $query_u = $conn->prepare($sql_u);
                $query_u->bindParam(':vn_id', $rs_wn->vn_id, PDO::PARAM_INT);
                $query_u->bindParam(':vns_id', $rs_wn->vns_id, PDO::PARAM_INT);
                $query_u->execute();


                $users = array();    
                
                foreach($query_u->fetchAll(PDO::FETCH_OBJ) as $u){
                    array_push($users,array(
                        "vu_id" => $u->vu_id,
                        "user_id" => $u->user_id,
                        "order" => $u->order,
                        "vn_id" => $u->vn_id,
                        "vns_id" => $u->vns_id,
                        "name" => $u->fname.$u->name.' '.$u->sname,
                    ));
                }

                http_response_code(200);
                array_push($datas, array(
                    "vn_id" => $rs_wn->vn_id,
                    "vns_id" => $rs_wn->vns_id,
                    "vn_name" => $rs_wn->vn_name,
                    "vns_name" => $rs_wn->vns_name,
                    "DN" => $rs_wn->DN,
                    "color" => $rs_wn->color,
                    "users" => $users,

                ));                
                
            }
            echo json_encode(array(
                'status' => true, 
                'message' => 'สำเร็จ', 
                'resps' => $res_wn,
                'respJSON' => $datas
            ));
            exit;

        }

     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล'));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}