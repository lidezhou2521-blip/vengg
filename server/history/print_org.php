<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include '../../vendor/autoload.php';
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $data->id;
    
    $datas = array();

    try{
        $sql = "SELECT vc.id , vc.ven_month, vc.ven_date1, vc.ven_date2, v.ven_com_idb, v.ven_com_name, 
                        vc.ven_com_num_all, vc.DN, vc.u_role, vc.user_id1, vc.ven_id1, vc.ven_id2, vc.user_id2, 
                        vc.ven_id1_old, vc.ven_id2_old, vc.comment, vc.status, vc.create_at
                FROM ven_change as vc  
                INNER JOIN ven as v ON v.id = vc.ven_id1
                WHERE vc.id = :id";
        $query = $conn->prepare($sql);
        $query->bindParam(':id',$id, PDO::PARAM_INT);
        $query->execute();
        $res = $query->fetch(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                        //count($result)  for odbc
            $doc_date           = DateThai_full($res->create_at);
            $ven_com_num_all    = $res->ven_com_num_all;
            $ven_com_date       = "";  
            $ven_com_idb        = "";   
            $ven_com_name       = $res->ven_com_name;   
            $ven_month          = $res->ven_month;   
            $name1              = "";   
            $name2              = "";   
            $name_dep1          = "";   
            $name_dep2          = ""; 
            $ven_date1          = "";   
            $ven_date2          = "";   
                     
            $sql = "SELECT vc.ven_com_date
                    FROM ven_com as vc
                    WHERE id = :id";
            $query = $conn->prepare($sql);
            $query->bindParam(':id',$res->ven_com_idb, PDO::PARAM_INT);
            $query->execute();
            $res_vc = $query->fetch(PDO::FETCH_OBJ);
            $ven_com_date       = DateThai_full($res_vc->ven_com_date); 

            $sql = "SELECT p.fname, p.name, p.sname, p.dep
                    FROM profile as p  
                    WHERE user_id = :user_id";
            $query = $conn->prepare($sql);
            $query->bindParam(':user_id',$res->user_id1, PDO::PARAM_INT);
            $query->execute();
            $res_p = $query->fetch(PDO::FETCH_OBJ);
            $name1      = $res_p->fname.$res_p->name.' '.$res_p->sname; 
            $name_dep1  = $res_p->dep; 
            
            $sql = "SELECT p.fname, p.name, p.sname, p.dep
                    FROM profile as p  
                    WHERE user_id = :user_id";
            $query = $conn->prepare($sql);
            $query->bindParam(':user_id',$res->user_id2, PDO::PARAM_INT);
            $query->execute();
            $res_p = $query->fetch(PDO::FETCH_OBJ);
            $name2       = $res_p->fname.$res_p->name.' '.$res_p->sname; 
            $name_dep2   = $res_p->dep; 

            $res->DN == 'กลางวัน' ? $time = '08.30 – 16.30' : $time = '16.30 – 08.30 ';  
            $ven_date1 = DateThai_full($res->ven_date1).' ตั้งแต่เวลา '.$time.' นาฬิกา';   
            $vd1       = DateThai_full($res->ven_date1).' เวลา '.$time.' นาฬิกา';   
            if($res->ven_id2 !=''){
                $ven_date2       = ' และข้าพเจ้าจะมาปฎิบัติหน้าที่แทน '. $name2 .' ในวันที่ '.DateThai_full($res->ven_date2).' ตั้งแต่เวลา '.$time.' นาฬิกา'; 
                $vd = DateThai_full($res->ven_date2);
            }else{
                $ven_date2       = ' ในวันที่ '.DateThai_full($res->ven_date2).' ตั้งแต่เวลา '.$time.' นาฬิกา'; 
                $vd = '-';
            }

            $sql = "SELECT id, create_at
                    FROM ven_change 
                    WHERE (ven_id1 = :ven_id1_old) OR (ven_id2 = :ven_id1_old) 
                    OR (ven_id1 = :ven_id2_old) OR (ven_id2 = :ven_id2_old)";

            $query = $conn->prepare($sql);
            $query->bindParam(':ven_id1_old', $res->ven_id1_old, PDO::PARAM_INT);
            $query->bindParam(':ven_id2_old', $res->ven_id2_old, PDO::PARAM_INT);
            $query->execute();

            $res_vc_old = $query->fetchAll(PDO::FETCH_OBJ);
            $vcod = [];
            $vcod_doc = '';
            if($query->rowCount()){
                foreach($res_vc_old as $rs){                    
                    $vcod_doc .= ' และบันทึกการเปลี่ยนเวรวันที่ '.DateThai_full($rs->create_at).' ['.$rs->id.']';
                    array_push($vcod,' และบันทึกการเปลี่ยนเวรวันที่ '.DateThai_full($rs->create_at).' ['.$rs->id.']');
                }
            }

            /**สร้างเอกสาร docx */
            $name_doc = '../../uploads/ven_tm.docx';
            if($res->DN =='กลางวัน'){ $name_doc = '../../uploads/venholiday.docx';}
            if($res->DN =='กลางคืน'){ $name_doc = '../../uploads/vennight.docx';}
            $templateProcessor = new TemplateProcessor($name_doc);//เลือกไฟล์ template ที่เราสร้างไว้
            $templateProcessor->setValue('doc_date', $doc_date);//อัดตัวแปร รายตัว
            $templateProcessor->setValue('ven_ch_id', $res->id);//อัดตัวแปร รายตัว
            $templateProcessor->setValue('ven_com_num_all', $ven_com_num_all);//อัดตัวแปร รายตัว
            $templateProcessor->setValue('ven_com_date', $ven_com_date);
            $templateProcessor->setValue('ven_com_name', $ven_com_name);
            $templateProcessor->setValue('comment', $vcod_doc);
            $templateProcessor->setValue('ven_month', $ven_month);
            $templateProcessor->setValue('name1', $name1);
            $templateProcessor->setValue('name2', $name2);
            $templateProcessor->setValue('name_dep1', $name_dep1);
            $templateProcessor->setValue('name_dep2', $name_dep2);
            $templateProcessor->setValue('ven_date1', $ven_date1);
            $templateProcessor->setValue('ven_date2', $ven_date2);            
            $templateProcessor->saveAs('../../uploads/ven.docx');//สั่งให้บันทึกข้อมูลลงไฟล์ใหม่
           
            http_response_code(200);
            echo json_encode(array(
                'status' => true, 
                'message' => 'OK', 
                'respJSON' => [
                    'doc_date'  => $doc_date,
                    'id'   => $res->id,
                    'ven_com_num_all'=> $ven_com_num_all,
                    'ven_com_date'   => $ven_com_date,
                    'ven_com_name'   => $ven_com_name,
                    'vcod'   => $vcod,
                    'comment'   => $res->comment,
                    'name1'   => $name1,
                    'name2'   => $name2,
                    'name_dep1'   => $name_dep1,
                    'name_dep2'   => $name_dep2,
                    'ven_date1'   => $vd1,
                    'ven_date2'   => $vd
                ]
            ));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'ไม่พบข้อมูล '));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}


