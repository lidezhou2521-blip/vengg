<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    if(!isset($data->ven_month)){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'not ven_month'));
        exit;
    } 
    if(!isset($data->ven_com_num)){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'not ven_com_num'));
        exit;
    } 
    if(!isset($data->ven_com_date)){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'not ven_com_date'));
        exit;
    } 

    $ven_month      = $data->ven_month;
    $ven_com_num    = $data->ven_com_num;
    $ven_com_date   = $data->ven_com_date;

    $datas      = array();
    $user_ids   = array();
    $names      = array();

    try{        
        $sql_vhd = "SELECT v.* , p.fname, p.name, p.sname
                    FROM ven as v
                    INNER JOIN `profile` AS p ON p.id = v.user_id
                    WHERE v.ven_month ='$ven_month' 
                        AND (v.`status` = 1 OR  v.`status` = 2)
                        AND v.DN = 'กลางวัน'
                        AND v.u_role = 'ผอ./แทน'
                    GROUP BY v.user_id
                    ORDER BY v.ven_date ASC";
        $query_vhd = $conn->prepare($sql_vhd);
        $query_vhd->execute();
        $res_vhds = $query_vhd->fetchAll(PDO::FETCH_OBJ);

        foreach($res_vhds as $vhd){
            array_push($names,$vhd->fname.$vhd->name.' '.$vhd->sname);
        }
        
        $sql_vhd = "SELECT v.* , p.fname, p.name, p.sname 
                    FROM ven as v
                    INNER JOIN `profile` AS p ON p.id = v.user_id
                    WHERE v.ven_month ='$ven_month' 
                        AND (v.`status` = 1 OR  v.`status` = 2)
                        AND v.DN = 'กลางคืน'
                        AND v.u_role = 'จนท'
                    GROUP BY v.user_id
                    ORDER BY v.ven_date ASC";
        $query_vhd = $conn->prepare($sql_vhd);
        $query_vhd->execute();
        $res_vhds = $query_vhd->fetchAll(PDO::FETCH_OBJ);

        foreach($res_vhds as $vhd){
            array_push($names,$vhd->fname.$vhd->name.' '.$vhd->sname);
        }

        $name   = '';
        $vn     = '';
        $vhd    = '';
        $vhn    = '';

        $sql_vhd = "SELECT v.* , p.fname, p.name, p.sname  
                    FROM ven as v
                    INNER JOIN `profile` AS p ON p.id = v.user_id
                    WHERE v.ven_month ='$ven_month' 
                    AND (v.`status` = 1 OR  v.`status` = 2)
                    AND v.DN = 'กลางวัน'
                    AND v.u_role = 'ผอ./แทน'
                    ORDER BY v.ven_date ASC";
        $query_vhd = $conn->prepare($sql_vhd);
        $query_vhd->execute();
        $res_vhds = $query_vhd->fetchAll(PDO::FETCH_OBJ);

        $sql_vhd = "SELECT v.* , p.fname, p.name, p.sname  
                    FROM ven as v 
                    INNER JOIN `profile` AS p ON p.id = v.user_id
                    WHERE v.ven_month ='$ven_month' 
                    AND (v.`status` = 1 OR  v.`status` = 2)
                    AND v.DN = 'กลางคืน'
                    AND v.u_role = 'ผู้ตรวจ'
                    ORDER BY v.ven_date ASC";
        $query_vhd = $conn->prepare($sql_vhd);
        $query_vhd->execute();
        $res_vhns = $query_vhd->fetchAll(PDO::FETCH_OBJ);
        
        $sql_vhd = "SELECT v.* , p.fname, p.name, p.sname  
                    FROM ven as v 
                    INNER JOIN `profile` AS p ON p.id = v.user_id
                    WHERE v.ven_month ='$ven_month' 
                    AND (v.`status` = 1 OR  v.`status` = 2)
                    AND v.DN = 'กลางคืน'
                    AND v.u_role = 'จนท'
                    ORDER BY v.ven_date ASC";
        $query_vhd = $conn->prepare($sql_vhd);
        $query_vhd->execute();
        $res_vns = $query_vhd->fetchAll(PDO::FETCH_OBJ);


        foreach($names as $name){
            $vhd_arr    = array();
            $vhn_arr    = array();
            $vn_arr    = array();
            $u_name = '';
            foreach($res_vhds as $vhd){
                $u_name = $vhd->fname.$vhd->name.' '.$vhd->sname;
                if($name == $u_name){
                    array_push($vhd_arr,date('j', strtotime($vhd->ven_date)));
                }
            }
            $u_name = '';
            foreach($res_vhns as $vhd){
                $u_name = $vhd->fname.$vhd->name.' '.$vhd->sname;
                if($name == $u_name){
                    array_push($vhn_arr,date('j', strtotime($vhd->ven_date)));
                }
            }
            $u_name = '';
            foreach($res_vns as $vhd){
                $u_name = $vhd->fname.$vhd->name.' '.$vhd->sname;
                if($name == $u_name){
                    array_push($vn_arr,date('j', strtotime($vhd->ven_date)));
                }
            }
            $u_name = '';
            array_push($datas,array(
                'name'  => $name,
                'vn'    => $vn_arr,
                'vhd'   => $vhd_arr,
                'vhn'   => $vhn_arr
            ));
            
        }

        if(count($datas) > 0){
            http_response_code(200);
            echo json_encode(array(
                'status' => true, 
                'message' => ' สำเร็จ ', 
                'resp' => $datas,
                'month' => DateThai_MY($ven_month),
                'ven_com_num' => $ven_com_num,
                'ven_com_date' => DateThai_for_kh($ven_com_date)

            ));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล '));
        exit;
    
    }catch(Exception $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}

