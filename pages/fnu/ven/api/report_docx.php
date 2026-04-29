<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");
date_default_timezone_set("Asia/Bangkok");



include 'vendor/autoload.php';
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

include_once "dbconfig.php";
include_once "function.php";

/** ----------------------------  ---------------------------*/

$DN_D_PRICE_DAY = 0;
$DN_N_PRICE_DAY = 0;

$_count = 0;
$price_total_all = 0;
$time='';
$error='';
$datas = array();

$price_d_all = 0;
$price_n_all = 0;


$data = json_decode(file_get_contents("php://input"));

if (isset($data->month) && !empty($data->month) && preg_match('/^\d{4}-\d{2}$/', $data->month)) {
    // รูปแบบถูกต้องและมีค่าไม่ว่าง
    $month = $data->month;
    $DATE_MONTH = date($data->month);
    // เรียกใช้โค้ดต่อไปที่นี่
} else {
    // รูปแบบไม่ถูกต้องหรือมีค่าว่าง
    http_response_code(400);
    echo json_encode(array('status' => false, 'message' => 'รูปแบบเดือนไม่ถูกต้องหรือไม่ได้ระบุเดือน'));
    exit;
}

$excluded_duties = isset($data->excluded_duties) ? $data->excluded_duties : array();


$HOLIDAY=[];

try{    
    
    $sql = "SELECT 
                vn.`name` AS vn_name,
                vc.*
            FROM ven_com AS vc
            INNER JOIN ven_name AS vn ON vc.vn_id = vn.id
            WHERE ven_month=:date_month";
    $query = $conn->prepare($sql);
    $query->bindParam(':date_month', $DATE_MONTH, PDO::PARAM_STR);
    $query->execute();
    $ven_com_nums = $query->fetchAll(PDO::FETCH_OBJ);

    //ที่เพิ่มเติม
    $ven_com_num = $ven_com_nums[0]->ven_com_num;
    $ven_com_date = DateThai_full($ven_com_nums[0]->ven_com_date);
        
    /** vens */
    $sql = "SELECT * FROM `ven` WHERE ven_month = :date_month AND (status = 1 OR status = 2)";
    $query = $conn->prepare($sql);
    $query->bindParam(':date_month', $DATE_MONTH, PDO::PARAM_STR);
    $query->execute();
    $vens = $query->fetchAll(PDO::FETCH_OBJ);


    /** user */    
    $sql = "SELECT * FROM profile WHERE status = 10 ORDER BY st ASC";
    $query = $conn->prepare($sql);
    $query->execute();
    $users = $query->fetchAll(PDO::FETCH_OBJ);

    if (count($users) > 0) { 
        foreach ($users as $user){           
            $price      = 0;
            $work_day   = array();
            $price_one  = 0;
            $weekdays   = 0;
            $holiday    = 0;
            $price_all  = 0;

            foreach($vens as $ven){
                
                if($user->user_id == $ven->user_id){
                    // ตรวจสอบว่าถูกกากบาทไหม
                    $is_excluded = false;
                    foreach ($excluded_duties as $ex) {
                        if ($ex->user_id == $ven->user_id
                            && $ex->day == (int)date('j', strtotime($ven->ven_date))
                            && $ex->ven_name == $ven->ven_name) {
                            $is_excluded = true;
                            break;
                        }
                    }
                    if ($is_excluded) continue; // ข้ามวันที่กากบาท

                    $price_one = $ven->price;
                    $price += $ven->price;
                    
                    // if(ck_holiday($ven->ven_date,$HLD )){
                    //     $holiday ++;
                    // }else{
                    //     $weekdays ++;
                    // }
                    if($ven->DN == 'กลางวัน'){
                        $time = ' เวลา 08.30 - 16.30 น.';
                        $price_d_all += $ven->price;
                    }
                    if($ven->DN == 'กลางคืน'){
                        $time = ' เวลา 16.30 - 08.30 น.';
                        $price_n_all += $ven->price;
                    }
                    array_push($work_day,$ven->ven_date);
                }

            }
           
            if($price > 0){                
                $price_total_all += $price_one * ($weekdays + $holiday);
                array_push($datas,array(
                    'user_id'=>$user->user_id,
                    'name'  => $user->fname.$user->name.' '.$user->sname,
                    'bank_account'  => $user->bank_account,
                    'bank_comment'  => $user->bank_comment,
                    'phone'  => $user->phone,
                    'work_day'=>$work_day,
                    'price_one'=>$price_one,
                    'weekdays' => $weekdays,
                    'holiday' => $holiday,
                    'price_all' => $price,

                ));
            }

        }
    }


    $doc_date   = date("Y-m-d");
    $doc_date   = DateThai_full($doc_date);
    $doc_date_c  = thainumDigit($doc_date);
    $month      = thainumDigit(DateThai_ym($DATE_MONTH));
    $count      = thainumDigit(count($datas)); 
    $price_n_1  = Num_f($price_n_all);
    $price_n_thai = thainumDigit($price_n_all);
    $price_n_text = Convert($price_n_all);
    $price_d        = Num_f($price_d_all);
    $price_d_thai   = thainumDigit($price_d_all);
    $price_d_text   = Convert($price_d_all);
    $price_dn_all      = $price_d_all + $price_n_all;
    $price_all_thai = Num_f($price_dn_all);
    $price_all_text = Convert($price_dn_all);
    // $datas = $datas;


    /**สร้างเอกสาร docx */
    $templateProcessor = new TemplateProcessor('template_in.docx');//เลือกไฟล์ template ที่เราสร้างไว้
    $templateProcessor->setValue('doc_date', $doc_date_c);//อัดตัวแปร รายตัว
    $templateProcessor->setValue('month', $month);//อัดตัวแปร รายตัว
    $templateProcessor->setValue('price_d', $price_d);
    $templateProcessor->setValue('price_n', $price_n_1);
    $templateProcessor->setValue('count', $count);
    $templateProcessor->setValue('price_all', $price_all_thai);
    $templateProcessor->setValue('price_all_text', $price_all_text);
    
    //ที่เพิ่มเติม
    $templateProcessor->setValue('ven_com_nums', $ven_com_num);
    $templateProcessor->setValue('ven_com_date', $ven_com_date);

    for($x = 0; $x <= count($datas); $x++){
        $no = 'n' . $x;
        $name = 'name_n' . $x;
        $t3_n = 't3_n' . $x ;
        $price_n = 'price_n' . $x;
        
        if(isset($datas[$x]['name'])){
            $no_data = ($x+1) . '.';
            $username = $datas[$x]['name'];
            $t3_n_data = 'จำนวนเงิน';
            
            $price_total_thai = Num_f($datas[$x]['price_all']).'.-บาท';
            // $price_total_thai = $datas[$x]['price_all'].'.-บาท';
        }else{
            $no_data = '';
            $username = '';
            $t3_n_data = '';
            $price_total_thai = '';
        }

        $templateProcessor->setValue($no, $no_data);
        $templateProcessor->setValue($name, $username);
        $templateProcessor->setValue($t3_n, $t3_n_data);
        $templateProcessor->setValue($price_n,  $price_total_thai);
    }
    
    $templateProcessor->saveAs('ven.docx');//สั่งให้บันทึกข้อมูลลงไฟล์ใหม่

    http_response_code(200);
    echo json_encode(array(
        'status' => true, 
        'message' => 'ok', 
        'month'=>DateThai_ym($DATE_MONTH),
        'datas' => $datas,
        "ven_com_num" => $ven_com_num,
        "ven_com_date" => $ven_com_date
    ));
 

}catch(PDOException $e){
    // echo "Faild to connect to database" . $e->getMessage();
    http_response_code(400);
    echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
}
