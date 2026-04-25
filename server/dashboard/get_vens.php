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
        $sql = "SELECT vns.name as u_role, vns.price, vns.color, vn.name, vn.DN
                FROM ven_name_sub AS vns
                INNER JOIN ven_name AS vn ON vn.id = vns.ven_name_id";
        $query = $conn->prepare($sql);
        $query->execute();
        $res = $query->fetchAll(PDO::FETCH_OBJ);
        

        $sql = "SELECT v.id, v.ven_date, v.ven_time, v.user_id, v.u_role, v.DN, v.price, v.color, v.ven_com_name, v.status, v.comment, p.fname, p.`name`, p.sname
                FROM ven AS v
                INNER JOIN `profile` AS p ON v.user_id = p.id
                WHERE v.status = 1 OR v.status = 2
                ORDER BY v.ven_date DESC, v.ven_time ASC
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
                        'DN' => $rs->DN
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




