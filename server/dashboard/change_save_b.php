<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

// Get JSON data from the request
$data = json_decode(file_get_contents("php://input"));


// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    

    $datas = array();    

    try{  
        
        $ch_v1  = $data->ch_v1;
        $user_id2 = $data->user_id2;
        $u_name2  = $data->u_name2;
        $idv1   = time();
        $idv2   = null;
        $ref    =  generateRandomString();
        $status             = 2 ;
        $create_at          = Date("Y-m-d H:i:s");
        
        $sql    = "SELECT v.* , p.fname, p.name, p.sname
                    FROM ven AS v
                    INNER JOIN profile as p ON v.user_id = p.user_id  
                    WHERE v.id = :id AND v.status=1";
        $query  = $conn->prepare($sql);
        $query->bindParam(':id',$ch_v1->id, PDO::PARAM_INT);
        $query->execute();
        $rsv1 = $query->fetch(PDO::FETCH_OBJ);
        if($query->rowCount() == 0){
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'กรุณาตรวจสอบประวัติการเปลี่ยน'));
            exit;
        }

        $u_name = $rsv1->fname.$rsv1->name.' '.$rsv1->sname;

        /** เช็ควันเวลาที่อยู่เวรไม่ได้ */  
        $ven_date = $rsv1->ven_date;
        $ven_date_u1 = date("Y-m-d", strtotime('+1 day', strtotime($ven_date)));
        $ven_date_d1 = date("Y-m-d", strtotime('-1 day', strtotime($ven_date)));

        $sql_VU = "SELECT v.* , p.fname, p.name, p.sname, p.workgroup
                    FROM ven AS v
                    INNER JOIN profile as p ON v.user_id = p.id 
                    WHERE v.user_id = $user_id2 
                        AND v.ven_date >= '$ven_date_d1' 
                        AND v.ven_date <= '$ven_date_u1' 
                        AND (v.status=1 OR v.status=2)
                        AND price  > 0";
        $query_VU = $conn->prepare($sql_VU);
        $query_VU->execute();
        if($query_VU->rowCount()){
            $res_VU = $query_VU->fetchAll(PDO::FETCH_OBJ);
            if ($res_VU[0]->workgroup != 'ผู้พิพากษา') {
                foreach($res_VU as $ru){
                // if($ru->ven_date == $ven_date){
                //     http_response_code(200);
                //     echo json_encode(array('status' => false, 'message' => $u_name2."\n".'วันที่ '.DateThai($ven_date).' มีเวรอยู่แล้ว.'));
                //     exit;
                // }
                if($ch_v1->DN == 'กลางวัน' && $ru->ven_date == $ven_date_d1 && $ru->DN == 'กลางคืน'){
                    http_response_code(200);
                    echo json_encode(array('status' => false, 'message' => $u_name2."\n".'วันที่ '.DateThai($ven_date_d1).' มีเวรกลางคืน'));
                    exit;
                }
                if($ch_v1->DN == 'กลางคืน'  && $ru->ven_date == $ven_date_u1 && $ru->DN == 'กลางวัน'){
                    http_response_code(200);
                    echo json_encode(array('status' => false, 'message' => $u_name2."\n".'วันที่ '.DateThai($ven_date_u1).' มีเวรกลางวัน'));
                    exit;
                }
                
            }

        }
        
         
        $conn->beginTransaction();

        // /**  สร้างเวรใบ1 */
        $sql = "INSERT INTO ven(id, ven_date, ven_time, DN, ven_month, ven_com_id, ven_com_idb, user_id, vn_id, vns_id, u_role, ven_name, ven_com_name, ven_com_num_all, ref1, ref2, price, color, gcal_id, `status`, update_at, create_at) 
                VALUE(:id, :ven_date, :ven_time, :DN, :ven_month, :ven_com_id, :ven_com_idb, :user_id, :vn_id, :vns_id, :u_role, :ven_name, :ven_com_name, :ven_com_num_all, :ref1, :ref2, :price, :color, :gcal_id, :status, :update_at, :create_at);";        
        $query = $conn->prepare($sql);
        $query->bindParam(':id',$idv1, PDO::PARAM_INT);
        $query->bindParam(':ven_date',$rsv1->ven_date, PDO::PARAM_STR);
        $query->bindParam(':ven_time',$rsv1->ven_time, PDO::PARAM_STR);
        $query->bindParam(':DN',$rsv1->DN, PDO::PARAM_STR);
        $query->bindParam(':ven_month',$rsv1->ven_month, PDO::PARAM_STR);
        $query->bindParam(':ven_com_id',$rsv1->ven_com_id, PDO::PARAM_STR);
        $query->bindParam(':ven_com_idb',$rsv1->ven_com_idb, PDO::PARAM_STR);
        $query->bindParam(':user_id',$user_id2, PDO::PARAM_INT);
        $query->bindParam(':vn_id',$rsv1->vn_id, PDO::PARAM_INT);
        $query->bindParam(':vns_id',$rsv1->vns_id, PDO::PARAM_INT);
        $query->bindParam(':u_role',$rsv1->u_role, PDO::PARAM_STR);
        $query->bindParam(':ven_name',$rsv1->ven_name, PDO::PARAM_STR);
        $query->bindParam(':ven_com_name',$rsv1->ven_com_name, PDO::PARAM_STR);
        $query->bindParam(':ven_com_num_all',$rsv1->ven_com_num_all, PDO::PARAM_STR);
        $query->bindParam(':ref1',$ref , PDO::PARAM_STR);
        $query->bindParam(':ref2',$rsv1->ref1 , PDO::PARAM_STR);
        $query->bindParam(':price',$rsv1->price , PDO::PARAM_STR);
        $query->bindParam(':color',$rsv1->color , PDO::PARAM_STR);
        $query->bindParam(':gcal_id',$rsv1->gcal_id , PDO::PARAM_STR);
        $query->bindParam(':status',$status , PDO::PARAM_INT);
        $query->bindParam(':update_at',$create_at , PDO::PARAM_STR);
        $query->bindParam(':create_at',$create_at , PDO::PARAM_STR);
        $query->execute();

        // /**  สร้างเวรใบที่2 */
        // $sql = "INSERT INTO ven(id, ven_date, ven_time, DN, ven_month, ven_com_id, ven_com_idb, user_id, u_name, u_role, ven_name, ven_com_name, ven_com_num_all, ref1, ref2, price, `status`, update_at, create_at) 
        //             VALUE(:id, :ven_date, :ven_time, :DN, :ven_month, :ven_com_id, :ven_com_idb, :user_id, :u_name, :u_role, :ven_name, :ven_com_name, :ven_com_num_all, :ref1, :ref2, :price, :status, :update_at, :create_at);";        
        // $query = $conn->prepare($sql);
        // $query->bindParam(':id',$idv2, PDO::PARAM_INT);
        // $query->bindParam(':ven_time',$rsv1->ven_time, PDO::PARAM_STR);
        // $query->bindParam(':ven_date',$rsv2->ven_date, PDO::PARAM_STR);
        // $query->bindParam(':DN',$rsv2->DN, PDO::PARAM_STR);
        // $query->bindParam(':ven_month',$rsv2->ven_month, PDO::PARAM_STR);
        // $query->bindParam(':ven_com_id',$rsv2->ven_com_id, PDO::PARAM_STR);
        // $query->bindParam(':ven_com_idb',$rsv2->ven_com_idb, PDO::PARAM_STR);
        // $query->bindParam(':user_id',$rsv1->user_id, PDO::PARAM_INT);
        // $query->bindParam(':u_name',$rsv1->u_name, PDO::PARAM_STR);
        // $query->bindParam(':u_role',$rsv1->u_role, PDO::PARAM_STR);
        // $query->bindParam(':ven_name',$rsv2->ven_name, PDO::PARAM_STR);
        // $query->bindParam(':ven_com_name',$rsv2->ven_com_name, PDO::PARAM_STR);
        // $query->bindParam(':ven_com_num_all',$rsv2->ven_com_num_all, PDO::PARAM_STR);
        // $query->bindParam(':ref1',$ref , PDO::PARAM_STR);
        // $query->bindParam(':ref2',$rsv2->ref1 , PDO::PARAM_STR);
        // $query->bindParam(':price',$rsv2->price , PDO::PARAM_STR);
        // $query->bindParam(':status',$status , PDO::PARAM_INT);
        // $query->bindParam(':update_at',$create_at , PDO::PARAM_STR);
        // $query->bindParam(':create_at',$create_at , PDO::PARAM_STR);
        // $query->execute();

        // /**สร้างใบเปลี่ยนเวร */

        $sql = "INSERT INTO ven_change(id, ven_date1, ven_date2, ven_month, ven_com_id, ven_com_num_all, DN, u_role, ven_id1, ven_id2, ven_id1_old, ven_id2_old,  user_id1, user_id2, ref1, `status`, create_at) 
                VALUE(:id, :ven_date1, :ven_date2, :ven_month, :ven_com_id,:ven_com_num_all, :DN, :u_role, :ven_id1, :ven_id2, :ven_id1_old, :ven_id2_old, :user_id1, :user_id2, :ref1, :status, :create_at);";        
        $chid = 'CH'.$idv1;
        $query = $conn->prepare($sql);
        $query->bindParam(':id',$chid, PDO::PARAM_INT);
        $query->bindParam(':ven_date1',$rsv1->ven_date, PDO::PARAM_STR);
        $query->bindParam(':ven_date2',$rsv1->ven_date, PDO::PARAM_STR);
        $query->bindParam(':ven_month',$rsv1->ven_month, PDO::PARAM_STR);
        $query->bindParam(':ven_com_id',$rsv1->ven_com_id, PDO::PARAM_STR);
        $query->bindParam(':ven_com_num_all',$rsv1->ven_com_num_all, PDO::PARAM_STR);
        $query->bindParam(':DN',$rsv1->DN, PDO::PARAM_STR);
        $query->bindParam(':u_role',$rsv1->u_role, PDO::PARAM_STR);
        $query->bindParam(':ven_id1',$idv1, PDO::PARAM_INT);
        $query->bindParam(':ven_id2',$idv2, PDO::PARAM_INT);
        $query->bindParam(':ven_id1_old',$rsv1->id, PDO::PARAM_INT);
        $query->bindParam(':ven_id2_old',$idv2, PDO::PARAM_INT);
        $query->bindParam(':user_id1',$rsv1->user_id, PDO::PARAM_INT);
        $query->bindParam(':user_id2',$user_id2, PDO::PARAM_INT);
        $query->bindParam(':ref1',$ref, PDO::PARAM_STR);
        $query->bindParam(':status',$status , PDO::PARAM_INT);
        $query->bindParam(':create_at',$create_at , PDO::PARAM_STR);        
        $query->execute();

        $status = 4;
        $sql = "UPDATE ven SET update_at=:update_at, ven.status =:status  WHERE id = :id";   
        $query = $conn->prepare($sql);
        $query->bindParam(':update_at',$create_at , PDO::PARAM_STR);
        $query->bindParam(':status',$status, PDO::PARAM_INT);
        $query->bindParam(':id',$ch_v1->id, PDO::PARAM_INT);
        $query->execute();

        // $sql = "UPDATE ven SET update_at=:update_at, ven.status =:status  WHERE id = :id";   
        // $query = $conn->prepare($sql);
        // $query->bindParam(':update_at',$create_at , PDO::PARAM_STR);
        // $query->bindParam(':status',$status, PDO::PARAM_INT);
        // $query->bindParam(':id',$ch_v2->id, PDO::PARAM_INT);
        // $query->execute();
        
        $conn->commit();

        /** google calendar */
        if(__GOOGLE_CALENDAR__){
            $sql_V = "SELECT * , p.fname, p.name, p.sname
                        FROM ven AS v
                        INNER JOIN profile as p ON v.user_id = p.user_id 
                        WHERE v.gcal_id = '$rsv1->gcal_id' AND (v.status=1 OR v.status=2)
                        ORDER BY v.ven_time ASC";
            $query_V = $conn->prepare($sql_V);
            $query_V->execute();
            if($query_V->rowCount()){
                $res_V = $query_V->fetchAll(PDO::FETCH_OBJ);
                $name = $res_V[0]->ven_com_name."\n";
                $sms = '';
                foreach($res_V as $v){
                    $sms .= $v->fname.$v->name.' '. $v->sname."\n";
                }

                gcal_update($rsv1->gcal_id, $name, $sms, 5);
                }
            }
        }

        /** ส่ง line to ven_admin */
        $sql = "SELECT token FROM line WHERE name = 'ven_admin' AND status=1";
        $query_line = $conn->prepare($sql);
        $query_line->execute();
        $res = $query_line->fetch(PDO::FETCH_OBJ);
        if($query_line->rowCount()){
            $sToken = $res->token;
            $sMessage = 'มีการเปลี่ยนเวร '.$chid."\n";
            $sMessage .= $u_name.'>>'.$u_name2."\n";
            $sMessage .= $rsv1->ven_date."\n";
            $sMessage .= '('.$create_at.')';
            $res_line = sendLine($sToken,$sMessage);
        }

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'ok', 'responseJSON' => ''));
        exit;  
        
    }catch(PDOException $e){
        $conn->rollback();
        http_response_code(500);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}



