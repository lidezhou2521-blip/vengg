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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ven_date   = $data->ven_date;
    // $DN         = $data->DN;
    $DN         = 'กลางคืน';
    
    $datas = array();

    $ven_com_num = '';
    $ven_com_date = '';
    $ven_date_d = '';
    $ven_date_m = '';
    $ven_date_y = '';
    $ven_date_time = '16:30';
    $ven_date_next_d = '';
    $ven_date_next_m = '';
    $ven_date_next_y = '';
    $ven_date_next_time = '08:30';
    $users[0] = array("name" => '',"dep" => '');
    $users[1] = array("name" => '',"dep" => '');
    

    // The request is using the POST method
    try{
        $sql = "SELECT v.id, v.ven_date, v.ven_com_name, v.DN, 
                        p.fname, p.name, p.sname, p.dep, p.workgroup, vc.ven_com_num, vc.ven_com_date 
                FROM ven as v 
                INNER JOIN profile as p ON v.user_id = p.user_id
                INNER JOIN ven_com as vc ON vc.id = v.ven_com_idb
                WHERE v.ven_date = '$ven_date' 
                        AND v.DN = '$DN' 
                        AND (v.`status` =1 OR v.`status` =2)
                        -- AND (v.`ven_com_name` = 'หมายค้น-หมายจับ')

                ORDER BY v.ven_time ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                       
            foreach($result as $rs){

                $ven_com_num = $rs->ven_com_num;
                $ven_com_date = $rs->ven_com_date;

                if($rs->workgroup == 'ผู้พิพากษา'){
                    $users[0] = array("name" => $rs->fname.$rs->name.' '.$rs->sname,"dep" => $rs->dep);
                }else{
                    $users[1] = array("name" =>$rs->fname.$rs->name.' '.$rs->sname,"dep" => $rs->dep);
                }
                // array_push($users,array(
                //     "name" => $rs->fname.$rs->name.' '.$rs->sname,
                //     "dep" => $rs->dep
                // ));
            }

        }
        $ven_date_next = date('Y-m-d', strtotime($ven_date .' +1 day'));

        $datas =array(
            "ven_com_num" => $ven_com_num,
            "ven_com_date" => DateThai_full($ven_com_date),
            "ven_date_d" => date_d($ven_date),
            "ven_date_m" => date_m($ven_date),
            "ven_date_y" => date_y($ven_date),
            "ven_date_time" => '16:30',
            "ven_date_next_d" => date_d($ven_date_next),
            "ven_date_next_m" => date_m($ven_date_next),
            "ven_date_next_y" => date_y($ven_date_next),
            "ven_date_next_time" => '08:30',
            "users" => $users
        );

        /**สร้างเอกสาร docx */
        $templateProcessor = new TemplateProcessor('../../uploads/template_docx/ven_jk_tm.docx');//เลือกไฟล์ template ที่เราสร้างไว้
        $templateProcessor->setValue('ven_com_num', $ven_com_num);
        $templateProcessor->setValue('ven_com_date', $datas['ven_com_date']);
        $templateProcessor->setValue('ven_date_d', $datas['ven_date_d']);
        $templateProcessor->setValue('ven_date_m', $datas['ven_date_m']);
        $templateProcessor->setValue('ven_date_y', $datas['ven_date_y']);
        $templateProcessor->setValue('ven_date_next_d', $datas['ven_date_next_d']);
        $templateProcessor->setValue('ven_date_next_m', $datas['ven_date_next_m']);
        $templateProcessor->setValue('ven_date_next_y', $datas['ven_date_next_y']);
        $templateProcessor->setValue('name1', $users[0]['name']);
        $templateProcessor->setValue('dep1', $users[0]['dep']);
        $templateProcessor->setValue('name2', $users[1]['name']);
        $templateProcessor->setValue('dep2', $users[1]['dep']);
        $templateProcessor->saveAs('../../uploads/ven_jk.docx');

       
        http_response_code(200);
        echo json_encode(array(
            'status' => true, 
            'message' => 'OK', 
            'resp' => $datas
        ));
        exit;
    
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}

function date_d($strDate)
{
    if($strDate == ''){
        return "-";
    }   
    $strDay= date("j",strtotime($strDate));
    
    return "$strDay";
}
function date_m($strDate)
{
    if($strDate == ''){
        return "-";
    }    
    $strMonth= date("n",strtotime($strDate));
    $strMonthCut = Array("","มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม",
                        "สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
    
    $strMonthThai=$strMonthCut[$strMonth];
    return "$strMonthThai";
}
function date_y($strDate)
{
    if($strDate == ''){
        return "-";
    }
    $strYear = date("Y",strtotime($strDate))+543;
    return "$strYear";
}

