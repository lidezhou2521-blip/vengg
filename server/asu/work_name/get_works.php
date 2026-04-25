<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";


// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datas      = array();
    
    try{
        // $sql = "SELECT * FROM ven_name ORDER BY srt ASC";
        $sql = "SELECT 
                    ven_name.name as vn_name, 
                    ven_name.DN as DN, 
                    ven_name_sub.name as vns_name, 
                    ven_name_sub.color as color, 
                    ven_name_sub.price as price, 
                    ven_name_sub.id as vns_id,
                    ven_name.id as vn_id,
                    ven_name.srt as vn_srt,
                    ven_name_sub.srt as vns_srt
                FROM ven_name 
                LEFT JOIN ven_name_sub
                ON ven_name.id = ven_name_sub.ven_name_id 
                ORDER BY ven_name.srt ASC, ven_name_sub.srt ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $resps = $query->fetchAll(PDO::FETCH_OBJ);


        if($query->rowCount() > 0){ 
            $ven_names  = array();                   
            $ven_name = '';   
            foreach($resps as $rs){
                if(!($ven_name == $rs->vn_name)){
                    array_push($ven_names, array(
                        'vn_id'     => $rs->vn_id,
                        'vn_name'   => $rs->vn_name,
                        'DN'        => $rs->DN,
                        'vn_srt'    => $rs->vn_srt,
                    ));
                    $ven_name = $rs->vn_name;
                }
            }
            
            
            foreach($ven_names as $vns){
                $vn_id      = $vns["vn_id"];
                $vn_name    = $vns["vn_name"];
                $DN         = $vns["DN"];
                $vn_srt     = $vns["vn_srt"];
                $ven_name_subs  = array();
                foreach($resps as $rs){
                    if($rs->vn_id == $vn_id && $rs->vns_id != null){
                        array_push($ven_name_subs,array(
                            "vn_name"   => $rs->vn_name,
                            "vns_DN"    => $rs->DN,
                            "vns_name"  => $rs->vns_name,
                            "color"     => $rs->color,
                            "price"     => $rs->price,
                            "vns_id"    => $rs->vns_id,
                            "vn_id"     => $rs->vn_id,
                            "vn_srt"    => $rs->vn_srt,
                            "vns_srt"   => $rs->vns_srt
                        ));
                    }
                }

                array_push($datas,array(
                    'vn_id'     => $vn_id,
                    'vn_name'   => $vn_name,
                    'DN'        => $DN,
                    'vn_srt'    => $vn_srt,
                    'ven_name_subs'   => $ven_name_subs
                ));



            }
            
            http_response_code(200);
            echo json_encode(array(
                'status' => true, 
                'message' => 'สำเร็จ', 
                // 'ven_name' => $ven_names,
                // 'resp' => $resps,
                'respJSON' => $datas,
            ));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล','respJSON' => $datas));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}