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
    // if($_SESSION['AD_ROLE'] != 9){
        //     http_response_code(200);
        //     echo json_encode(array('status' => false, 'message' => 'ไม่มีสิทธิ์'));
        //     exit;
        // }
        
    $sms_err    = array();
    $datas      = array();
    $datas_gcal = array();
    $_ven_dates = array();

    $_ven_com_id='';
    if(isset($data->ven_com_id)){
        $_ven_com_id = cleanData($data->ven_com_id);
    }else{
        array_push($sms_err,'ven_com_id : null');
    }    

    if($sms_err){
        http_response_code(200);
        echo json_encode(array('status' => true, 'errors' => $sms_err));
        exit;
    }

    
    try{
        
        $sql = "UPDATE ven SET status = 1 WHERE status = 2 AND ven_com_idb = :ven_com_idb";
        $query = $conn->prepare($sql);   
        $query->bindParam(':ven_com_idb', $_ven_com_id, PDO::PARAM_STR);   
        $query->execute();


        $sql = "SELECT v.*, p.fname, p.name, p.sname 
                FROM ven AS v
                INNER JOIN `profile` AS p ON v.user_id = p.id 
                WHERE v.ven_com_idb=:ven_com_idb
                AND (v.status=1 OR v.status=2)
                AND (v.gcal_id IS NULL OR v.gcal_id='')
                ORDER BY v.ven_date ASC, v.ven_time ASC";
        $query = $conn->prepare($sql); 
        $query->bindParam(':ven_com_idb', $_ven_com_id, PDO::PARAM_STR);    
        $query->execute();
        
        if($query->rowCount()){
            $res        = $query->fetchAll(PDO::FETCH_OBJ);            
            $_ven_date  = '';
            
            foreach($res as $rs){
                if(!($_ven_date == $rs->ven_date)){
                    array_push($_ven_dates,$rs->ven_date);                    
                }
                $_ven_date = $rs->ven_date;
            }

            if(count($_ven_dates) > 0){
                $sql_v = "SELECT v.*, v.id, v.user_id, v.ven_com_idb, v.ven_date, v.ven_time, v.gcal_id, p.fname, p.name, p.sname, vn.`name` AS vn_name 
                            FROM ven AS v
                            INNER JOIN `profile` AS p ON v.user_id = p.id 
                            INNER JOIN ven_name AS vn ON v.vn_id = vn.id
                            WHERE v.ven_com_idb = :ven_com_idb
                            AND (v.status = 1 OR v.status = 2)
                            ORDER BY v.ven_date ASC, v.ven_time ASC";
                $query_v = $conn->prepare($sql_v);
                $query_v->bindParam(':ven_com_idb', $_ven_com_id, PDO::PARAM_STR);
                $query_v->execute();
                $res_v = $query_v->fetchAll(PDO::FETCH_OBJ);


                
                foreach($_ven_dates as $_vd){
                    $_ven_name  = "";
                    $_ven_com_idb= "";
                    $_ven_time  = "";
                    $u_names    = array();
                    $_gcal_null = array();
                    
                    foreach($res_v as $rs){
                        $_ven_name      = $rs->vn_name;
                        $_ven_com_idb   = $rs->ven_com_idb;                        
                        $_ven_time      = $rs->ven_time;

                        if($_vd == $rs->ven_date){
                            array_push($u_names,array(
                                "vid"   => $rs->id,
                                "uid"   => $rs->user_id,
                                "u_name"=> $rs->fname.$rs->name.' '.$rs->sname,
                                "gcal_id"  => $rs->gcal_id,
                            )); 

                            if($rs->gcal_id == '' || $rs->gcal_id == null){
                                array_push($_gcal_null,array(
                                    "vid"   => $rs->id,
                                    "uid"   => $rs->user_id,
                                    "u_name"=> $rs->fname.$rs->name.' '.$rs->sname,
                                    "ven_date"=> $rs->ven_date,
                                    "gcal_id"  => $rs->gcal_id,
                                ));
    
                            }                      
                        }
                    }
    
                    array_push($datas,array(
                        "ven_date"      => $_vd,
                        "ven_time"      => $_ven_time,
                        "ven_com_name"  => $_ven_name,
                        "ven_com_idb"   => $_ven_com_idb,
                        "u_name"        => $u_names,
                    ));
                    $u_names = array();
                }
            }   

            /** ส่งข้อมูล ขึ้น gcal */
            if(__GOOGLE_CALENDAR__){
                
                foreach($datas as $d){
                    $name           = "(เวร)".$d["ven_com_name"];
                    $start          = $d["ven_date"].' '.$d["ven_time"];
                    $desc           = '';
                    
                    $ven_com_name   = $d["ven_com_name"];
                    $ven_com_idb    = $d["ven_com_idb"];
                    $ven_date       = $d["ven_date"];
                    $vid_a          = array();
                    $gcal_id_a      = array();

                    $date   = new DateTime($start);
                    $start  = $date->format(DateTime::ATOM);

                    foreach($d["u_name"] as $un){
                        $desc .= $un["u_name"]."\n";

                        if($un["gcal_id"] <> '' || $un["gcal_id"] <> null){
                            array_push($gcal_id_a, $un["gcal_id"]);
                        }

                        array_push($vid_a, $un["vid"]);
                    }

                    /** ถ้ามี gcal_id อยู่ให้ลบ */
                    if(count($gcal_id_a) > 0 ){
                        foreach($gcal_id_a as $gid){
                            gcal_remove($gid);
                        }
                    }

                    /** ส่งข้อมูลขึ้น gcal */
                    $res = json_decode(gcal_insert($name, $start, $desc));
                    if($res){
                        $gcal_id = $res->resp->id; 

                        $sql = "UPDATE ven 
                                SET gcal_id =:gcal_id 
                                WHERE ven_com_idb =:ven_com_idb 
                                AND ven_com_name =:ven_com_name
                                AND ven_date =:ven_date";    
    
                        $query = $conn->prepare($sql);
                        $query->bindParam(':gcal_id', $gcal_id, PDO::PARAM_STR);
                        $query->bindParam(':ven_com_idb', $ven_com_idb, PDO::PARAM_INT);
                        $query->bindParam(':ven_com_name', $ven_com_name, PDO::PARAM_STR);
                        $query->bindParam(':ven_date', $ven_date, PDO::PARAM_STR);
                        $query->execute(); 
                        
                        array_push($datas_gcal,array(
                            'ven_com_idb'   => $ven_com_idb,
                            'ven_date'      => $ven_date,
                            'name'          => $name,
                            'start'         => $start,
                            'desc'          => $desc,     
                            'gcal_id'       => $gcal_id, 
                        ));
                    }                   

                } 
            }

            http_response_code(200);
            echo json_encode(array(
                'status'    => true,
                'message'   => "Ok",
                '_gcal_null' => $_ven_dates,
                'resp'       => $datas_gcal
            ));
            exit;      
        }else{
            http_response_code(200);
            echo json_encode(array(
                'status'    => false,
                'message'   => "ไม่มีการปรับปรุงข้อมูล",
                '_gcal_null' => $_ven_dates,
                'datas_gcal' => $datas_gcal,
                'resp'       => $datas
            ));
            exit;
        }
           
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ERROR เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}
