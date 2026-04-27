<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
 
    $ssid = isset($_SESSION['AD_ID']) ? $_SESSION['AD_ID'] : '';

    $datas = array();

    
    try{
        // Duty types: 1 entry per duty name (vn), not per sub-role
        $sql = "SELECT vn.id as vn_id, vn.name, vn.DN,
                       (SELECT vns2.color FROM ven_name_sub AS vns2
                        WHERE vns2.ven_name_id = vn.id ORDER BY vns2.srt ASC LIMIT 1) AS color
                FROM ven_name AS vn
                ORDER BY vn.srt ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $res = $query->fetchAll(PDO::FETCH_OBJ);
        

        $sql = "SELECT v.id, v.ven_date, v.ven_time, v.user_id as profile_id, v.u_role, v.DN, v.price, v.color, v.ven_com_name, v.ven_name, v.status, v.comment, v.vn_id, v.vns_id, p.fname, p.`name`, p.sname, p.user_id,
                        COALESCE(vns.srt, 999) AS vns_srt
                FROM ven AS v
                INNER JOIN `profile` AS p ON v.user_id = p.id
                LEFT JOIN ven_name_sub AS vns ON vns.id = v.vns_id
                WHERE v.status = 1 OR v.status = 2
                ORDER BY v.ven_date DESC, v.ven_time ASC, vns.srt ASC
                LIMIT 5000";
        $query = $conn->prepare($sql);
        $query->execute();
        
        if($query->rowCount() > 0){                       
            $result = $query->fetchAll(PDO::FETCH_OBJ);
            foreach($result as $rs){
                $rs->DN == 'กลางวัน' ? $d = '☀️' : $d = '🌙';
                $bgcolor = $rs->color;
                $textC = 'white';
                
                array_push($datas,array(
                    'id'    => $rs->id,
                    'title' => $d.' '.$rs->fname.$rs->name.' '.$rs->sname,
                    'start' => $rs->ven_date.' '.$rs->ven_time,
                    'allDay' => true,
                    'backgroundColor' => $bgcolor,
                    'textColor' => $textC,
                    'comment' => $rs->comment ? $rs->comment : '',
                    'extendedProps' => array(
                        'u_name' => $rs->fname.$rs->name.' '.$rs->sname,
                        'u_role' => $rs->u_role,
                        'ven_com_name' => $rs->ven_com_name,
                        'ven_name' => $rs->ven_name,
                        'DN' => $rs->DN,
                        'user_id' => $rs->user_id,
                        'vn_id'  => (int)$rs->vn_id,
                        'vns_id' => (int)$rs->vns_id,
                        'vu_order' => (int)$rs->vns_srt
                    )
                ));
            }
            
            http_response_code(200);
            echo json_encode(array(
                'status' => true, 
                'message' => 'สำเร็จ', 
                'respJSON' => $datas, 
                'ssid' => $ssid,
                'res' => $res
            ));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล ', 'respJSON' => $datas, 'ssid' => $ssid));
        exit;
    
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}

function getColor($res,$d,$price,$v_name){    
    $color = '';
    foreach($res as $rs){
        if($rs->u_role == $d && $rs->price == $price && $rs->name == $v_name ){
            $color = $rs->color;
            break;
        }
    }
    return $color; 
}




