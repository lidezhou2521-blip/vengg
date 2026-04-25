<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $datas = array();

    try{
    
        $sql = "SELECT * FROM ven WHERE ven_month = '2022-11' AND (status=1 OR status=2)  ORDER BY ven_time ASC, ven_date ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        $x  = 0;
        $n  = $query->rowCount();

        $vd = array();

        if($query->rowCount() > 0){                        //count($result)  for odbc
            $vd_o = '';
            foreach($result as $rs){
                if($vd_o != $rs->ven_date){
                    array_push($vd,$rs->ven_date);
                    $vd_o = $rs->ven_date;
                }
            }

            foreach($vd as $r){                         /**    เวียนวัน  $r วันที่ 2022-11-01  */
                $vt         = array();
                $u_namej    = array();
                $u_name     = array();
                $u_role     = array();
                $cmt        = array();

                $OLD_VT = '';
                $OLD_UNAME = '';
                foreach($result as $rs){
                    if($rs->ven_date == $r){
                        $vt_s = substr($rs->ven_time, 0, -3);
                        if($OLD_VT != $vt_s){
                            array_push($vt,$vt_s);
                            $OLD_VT = $vt_s;
                        }

                        if($OLD_UNAME != $rs->u_name){
                            $st_ul      = strlen($rs->u_role);
                            $st_urlo    = $rs->u_role;
                            if($st_ul > 30){
                                $st_urlo = substr($st_ul, 0, 30);
                            }
                            if($st_urlo == 'ผู้พิพากษา'){
                                array_push($u_namej,$rs->u_name );
                            }else{
                                array_push($u_name,$rs->u_name);
                                array_push($cmt,$rs->u_role);
                            }
                            $OLD_UNAME = $rs->u_name;
                        }

                    }
                }

                array_push($datas,array(
                    'ven_date'  => $r,
                    'ven_time'  => $vt,
                    'u_namej'  => $u_namej,
                    'u_name'    => $u_name,
                    'cmt'       => $cmt,
                ));
            }
            
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => substr('ผู้พิพากษา3', 0, 30). ' สำเร็จ '.  strlen('ผู้พิพากษา3'), 'respJSON' => $datas));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล'));
        exit;
    
    }catch(PDOException $e){;
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}