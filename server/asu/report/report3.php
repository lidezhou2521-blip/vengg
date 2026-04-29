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
    $ven_month = $data->ven_month;
    $excluded_duties = isset($data->excluded_duties) ? $data->excluded_duties : array();
    
    $datas = array();

    try{
        $sql_com = "SELECT vc.* , vn.`name` AS ven_name
                    FROM ven_com AS vc
                    INNER JOIN ven_name AS vn ON vc.vn_id = vn.id 
                    WHERE vc.ven_month = '$ven_month'
                    ORDER BY vc.ven_com_num ASC";
        $query_vc = $conn->prepare($sql_com);
        $query_vc->execute();
        $res_ven_com = $query_vc->fetchAll(PDO::FETCH_OBJ);

        $ven_com = '';        
        foreach($res_ven_com as $rvc){
            if(str_contains($rvc->ven_name, 'ฟื้นฟู') || str_contains($rvc->ven_name, 'ตรวจสอบการจับ')){
                $ven_com .= $rvc->ven_com_num; 
                $ven_com .= '(กลางวัน)'; 
                $ven_com .= ', '; 
            }
            if(str_contains($rvc->ven_name, 'หมายจับ-ค้น')){
                $ven_com .= $rvc->ven_com_num; 
                $ven_com .= '(กลางคืน)'; 
                $ven_com .= ', ';
            }
            if(str_contains($rvc->ven_name, 'วันหยุดราชการ')){
                $ven_com .= $rvc->ven_com_num; 
                $ven_com .= '(วันหยุด)'; 
                $ven_com .= ', ';
            }
            $ven_com .= ' '; 
        }
        $ven_com .= 'ลงวันที่ '; 
        $ven_com .= DateThai_full($res_ven_com[0]->ven_com_date); 

        $sql = "SELECT v.* , p.fname, p.name, p.sname 
                FROM ven AS v
                INNER JOIN `profile` AS p ON p.id = v.user_id
                WHERE v.ven_month = '$ven_month' AND (v.status = 1 OR v.status = 2 )
                ORDER BY v.ven_date ASC, v.ven_time ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $res_ven = $query->fetchAll(PDO::FETCH_OBJ);

        
        if($query->rowCount() > 0){                        //count($result)  for odbc
            $ven_date_arr = array();
            $date = '';
            foreach($res_ven as $rs){
                if(!($date == $rs->ven_date)){
                    array_push($ven_date_arr,$rs->ven_date);
                }
                $date = $rs->ven_date;
            }
            
            $u_name_arr = array();
            foreach($ven_date_arr as $date){
                foreach($res_ven as $rv){
                    if($date == $rv->ven_date){
                        // ตรวจสอบว่าถูกกากบาทไหม
                        $is_excluded = false;
                        foreach ($excluded_duties as $ex) {
                            if ($ex->user_id == $rv->user_id
                                && $ex->day == (int)date('j', strtotime($rv->ven_date))
                                && $ex->ven_name == $rv->ven_name) {
                                $is_excluded = true;
                                break;
                            }
                        }
                        if ($is_excluded) continue; // ข้ามวันที่กากบาท

                        array_push($u_name_arr,array(
                            'ven_date'  => $date,
                            'u_name'    => $rv->fname.$rv->name.' '.$rv->sname,
                            'u_role'    => $rv->u_role,
                            'ven_name'  => $rv->ven_name,
                            'DN'        => $rv->DN,
                        ));
                    }
                }
                        
            }
                 
           
           foreach($ven_date_arr as $date){
                $u1 = '';
                $u2 = '';
                $u3 = '';
                $u4 = '';
                $u5 = '';
                $u6 = '';
                $u7 = '';
                $u45 = array();
                $hld = '';
                foreach($u_name_arr as $u){
                    if($date == $u['ven_date']){
                        if($u['u_role'] == 'ผู้พิพากษา' && (str_contains($u['ven_name'], 'ฟื้นฟู') || str_contains($u['ven_name'], 'ตรวจสอบการจับ') || str_contains($u['ven_name'], 'วันหยุดราชการ'))){
                            $u1 = $u['u_name'];
                        }
                        if($u['u_role'] == 'ผู้พิพากษา' && str_contains($u['ven_name'], 'หมายจับ-ค้น')){
                            $u2 = $u['u_name'];
                        }
                        if($u['u_role'] == 'ผอ./แทน' && (str_contains($u['ven_name'], 'ฟื้นฟู') || str_contains($u['ven_name'], 'ตรวจสอบการจับ') || str_contains($u['ven_name'], 'วันหยุดราชการ'))){
                            $u3 = $u['u_name'];
                        }
                        if($u['u_role'] == 'จนท' && (str_contains($u['ven_name'], 'ฟื้นฟู') || str_contains($u['ven_name'], 'ตรวจสอบการจับ') || str_contains($u['ven_name'], 'วันหยุดราชการ'))){
                            array_push($u45,$u['u_name']);
                        }
                        
                        if($u['u_role'] == 'จนท' && str_contains($u['ven_name'], 'หมายจับ-ค้น')){
                            $u6 = $u['u_name'];
                        }
                        if($u['u_role'] == 'ผู้ตรวจ' && str_contains($u['ven_name'], 'ผู้ตรวจ(กลางคืน)')){
                            $u7 = $u['u_name'];
                        }
                    }                    
                }
                if($u45){
                    $u4 = $u45[0];
                    $u5 = isset($u45[1]) ? $u45[1] : '';
                    $hld = 'bg-warning text-dark';
                }
                
                array_push($datas,array(
                    'ven_date'  => $date,
                    'u_name'    => [$u1, $u2, $u3, $u4, $u5, $u6, $u7],
                    'hld'       => $hld
                ));
                        
            }
            
            http_response_code(200);
            echo json_encode(array(
                'status' => true, 
                'message' => ' สำเร็จ ', 
                'u_name_arr' => $u_name_arr,
                'ven_com' => $ven_com, 
                'respJSON' => $datas , 
                'ven_date'  =>$ven_date_arr,
                'ven_month' => $ven_month,

            ));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล '));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}

function sh($arr, $date, $ven_name, $u_role){
    foreach($arr as $ar){
        if($ar->ven_name == $ven_name && $ar->u_role == $u_role){
            return $ar->u_name;
        }
        return '';
    }
}