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
    $vcid = $data->vcid;

    $datas = array();

    try{

        $sql = "SELECT * FROM ven_com WHERE id = $vcid";
        $query = $conn->prepare($sql);
        $query->execute();
        $vc = $query->fetch(PDO::FETCH_OBJ);

        $ven_com = [
            "id" => $vc->id,
            "ven_com_num" => $vc->ven_com_num,
            "ven_com_date" => $vc->ven_com_date,
            "ven_month" => $vc->ven_month,
            "ven_month_th" => DateThai_MY($vc->ven_month),
            "vn_id" => $vc->vn_id,
            "status" => $vc->status,
        ];


        $sql = "SELECT v.* , p.fname, p.name, p.sname
                FROM ven as v
                INNER JOIN `profile` AS p ON p.id = v.user_id
                WHERE v.ven_month = '$vc->ven_month' 
                    AND (v.status=1 OR v.status=2) 
                ORDER BY v.ven_date ASC, v.ven_time ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        $x  = 0;
        $n  = $query->rowCount();

        $vd = array();

        if($query->rowCount() > 0){                        //count($result)  for odbc
            $vd_o = '';
            foreach($result as $rs){
                $aVCI = json_decode($rs->ven_com_id);
                foreach($aVCI as $r){
                    if($r == $vcid){
                        if($vd_o != $rs->ven_date){
                            array_push($vd,$rs->ven_date);
                            $vd_o = $rs->ven_date;
                        }
                    }
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
                    $name = $rs->fname.$rs->name.' '.$rs->sname;
                    if($rs->ven_date == $r){
                        // if(count($rs->ven_com_id) > 0){

                            foreach(json_decode($rs->ven_com_id) as $v){
                                if($vcid == $v){
        
                                    $vt_s = substr($rs->ven_time, 0, -3);
                                    if($OLD_VT != $vt_s){
                                        array_push($vt,$vt_s);
                                        $OLD_VT = $vt_s;
                                    }
            
                                    if($OLD_UNAME != $name){
                                        $st_ul      = strlen($rs->u_role);
                                        $st_urlo    = $rs->u_role;
                                        if($st_ul > 30){
                                            $st_urlo = substr($st_urlo, 0, 30);
                                        }
                                        if($st_urlo == 'ผู้พิพากษา'){
                                            array_push($u_namej,$name );
                                        }elseif($rs->u_role == 'จนท'){
                                            array_push($u_name,$name);
                                            array_push($cmt,$rs->u_role);
                                        }
                                        $OLD_UNAME = $name;
                                    }
                                }
    
                            }
                        // }

                    }
                }

                array_push($datas,array(
                    'ven_date'  => $r,
                    'ven_time'  => $vt,
                    'u_namej'  => $u_namej,
                    'u_name'    => $u_name,
                    'cmt'       => $cmt
                ));
            }
            
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => ' สำเร็จ ', 'respJSON' => $datas , 'vc'=>$ven_com));
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