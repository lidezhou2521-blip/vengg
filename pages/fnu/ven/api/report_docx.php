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
    
    // ตรวจสอบว่ามี extension zip หรือไม่ (จำเป็นสำหรับ PHPWord)
    if (!extension_loaded('zip')) {
        throw new Exception('เซิร์ฟเวอร์ไม่ได้เปิดใช้งาน PHP Zip Extension กรุณาเปิดใช้งานใน php.ini');
    }

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

    if (count($ven_com_nums) == 0) {
        throw new Exception('ไม่พบข้อมูลคำสั่งเวรในเดือนที่ระบุ');
    }

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
                    /** ตรวจสอบเงื่อนไขไม่เบิก (No Claim) */
                    $com_id_raw = trim((string)$ven->ven_com_id);
                    $com_id_arr = json_decode($com_id_raw, true);
                    $com_id_empty = ($com_id_raw === '' || $com_id_raw === 'null' || $com_id_raw === '[]'
                        || (is_array($com_id_arr) && count($com_id_arr) === 0)
                        || $com_id_arr === null);
                    $com_idb = trim((string)$ven->ven_com_idb);
                    $com_num = trim((string)$ven->ven_com_num_all);
                    
                    $is_no_claim = (
                        ($com_id_empty && ($com_idb === '' || $com_idb === 'null') && ($com_num === '' || $com_num === 'null'))
                        || $ven->price <= 0
                    );

                    if ($is_no_claim) continue; // ข้ามถ้าเป็นเวรไม่เบิกจ่าย

                    /** ตรวจสอบว่าถูกกากบาท (Excluded) จากหน้าเว็บไหม */
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
                    
                    /** แยกกลุ่มเงินตามชื่อเวร */
                    $v_name = (string)$ven->ven_name;
                    
                    // กลุ่มที่ 1: เวรแขวงฯ / เวรปล่อยฯ (เปิดทำการ) -> ลงช่อง price_d
                    if (strpos($v_name, 'แขวง') !== false || strpos($v_name, 'เปิดทำการ') !== false || strpos($v_name, 'ปล่อย') !== false) {
                        $price_d_all += $ven->price;
                    } 
                    // กลุ่มที่ 2: เวรอื่นๆ (หมายจับ, ค้น, ตรวจสอบการจับ) -> ลงช่อง price_n
                    else {
                        $price_n_all += $ven->price;
                    }
                    array_push($work_day,$ven->ven_date);
                }

            }
           
            if($price > 0){                
                $price_total_all += $price; // แก้ไขให้ถูกต้อง
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


    /**สร้างเอกสาร docx */
    if (!file_exists('template_in.docx')) {
        throw new Exception('ไม่พบไฟล์เทมเพลต template_in.docx');
    }

    $templateProcessor = new TemplateProcessor('template_in.docx');
    $templateProcessor->setValue('doc_date', $doc_date_c);
    $templateProcessor->setValue('month', $month);
    $templateProcessor->setValue('price_d', $price_d);
    $templateProcessor->setValue('price_n', $price_n_1);
    $templateProcessor->setValue('count', $count);
    $templateProcessor->setValue('price_all', $price_all_thai);
    $templateProcessor->setValue('price_all_text', $price_all_text);
    
    $templateProcessor->setValue('ven_com_nums', $ven_com_num);
    $templateProcessor->setValue('ven_com_date', $ven_com_date);

    // สั่ง Clone แถวตามจำนวนข้อมูลที่มีใน $datas
    if (count($datas) > 0) {
        $templateProcessor->cloneRow('name', count($datas));

        foreach ($datas as $index => $row) {
            $row_num = $index + 1;
            
            // ใส่ลำดับ (1., 2., 3...)
            $templateProcessor->setValue('n#' . $row_num, $row_num . '.');
            
            // ใส่ชื่อ
            $templateProcessor->setValue('name#' . $row_num, $row['name']);
            
            // รวมวันที่ (เช่น 1, 5, 10)
            $days = array();
            foreach ($row['work_day'] as $wd) {
                $days[] = (int)date('j', strtotime($wd)); // ดึงเฉพาะเลขวันที่
            }
            sort($days); // เรียงลำดับวันที่จากน้อยไปมาก
            $days_text = implode(', ', $days);
            $templateProcessor->setValue('work_date#' . $row_num, $days_text);
            
            // ใส่จำนวนเงินรวมของคนนั้น
            $templateProcessor->setValue('price_total#' . $row_num, Num_f($row['price_all']) . '.-บาท');
            
            // เก็บตัวแปรเดิมไว้เผื่อเทมเพลตยังใช้ชื่อเดิม (optional)
            $templateProcessor->setValue('t3_n#' . $row_num, 'จำนวนเงิน');
        }
    }
    
    $templateProcessor->saveAs('ven.docx');

    echo json_encode(array(
        'status' => true, 
        'message' => 'ok', 
        'month'=>DateThai_ym($DATE_MONTH),
        'datas' => $datas,
        "ven_com_num" => $ven_com_num,
        "ven_com_date" => $ven_com_date
    ));

} catch(Exception $e) {
    http_response_code(200); // Always return 200 to let axios read the JSON error message
    echo json_encode(array('status' => false, 'message' => $e->getMessage()));
}
