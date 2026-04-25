<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");
date_default_timezone_set("Asia/Bangkok");

include 'vendor/autoload.php';

include_once "dbconfig.php";
include_once "function.php";

$DN_D_PRICE_DAY = 0;
$DN_N_PRICE_DAY = 0;

$_count = 0;
$price_dn1_all = 0;
$price_dn2_all = 0;
$error='';

$data = json_decode(file_get_contents("php://input"));

// $DATE_MONTH = date("2022-11");
// $DATE_MONTH = date($data->month);
if (isset($data->month) && !empty($data->month) && preg_match('/^\d{4}-\d{2}$/', $data->month)) {
    // รูปแบบถูกต้องและมีค่าไม่ว่าง
    $DATE_MONTH = date($data->month);
} else {
    // รูปแบบไม่ถูกต้องหรือมีค่าว่าง
    http_response_code(200);
    echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูลหรือรูปแบบไม่ถูกต้อง'));
    exit;
}

$users = array();
$vens = array();
$ven_users = array();
$ven_coms = array();

$datas = array();
// $DATE_MONTH = '2022-11';

$price_all = 0;
// http_response_code(200);
//         echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล', 'responseJSON' => $data->month));
// exit;
try{    

    $sql = "SELECT user_id,fname,name,sname, phone, bank_account, bank_comment FROM profile WHERE status=10 ORDER BY st";
    $query = $conn->prepare($sql);
    $query->execute();
    $users = $query->fetchAll(PDO::FETCH_OBJ);
    
    $sql = "SELECT * FROM ven WHERE ven_month='$DATE_MONTH' AND (status=1 OR status=2) ORDER BY user_id";
    $query = $conn->prepare($sql);
    $query->execute();
    $vens = $query->fetchAll(PDO::FETCH_OBJ);
    
    $sql = "SELECT * FROM ven_com WHERE ven_month='$DATE_MONTH' ORDER BY ven_com_num ASC";
    $query = $conn->prepare($sql);
    $query->execute();
    $ven_coms = $query->fetchAll(PDO::FETCH_OBJ);
   
    // $datas = $users;

    foreach($users as $user){
        $ven_users = array();
        $D_c = 0;
        $N_c = 0;
        $D_price = 0;
        $N_price = 0;
        foreach($vens as $ven){
            if($ven->user_id == $user->user_id){
                if($ven->DN == 'กลางวัน'){
                    $D_price += $ven->price;
                    $D_c ++;
                }
                if($ven->DN == 'กลางคืน'){
                    $N_price += $ven->price;
                    $N_c ++ ;
                }
                $price_all += $ven->price;
                array_push($ven_users,array(
                    "ven_date" => $ven->ven_date,
                    "DN" => $ven->DN,
                    "price" => $ven->price,
                ));        
            }
        }
        
        if(count($ven_users) > 0){
            array_push($datas,array(
                "uid" => $user->user_id,
                "name" => $user->fname.$user->name.' '.$user->sname,
                "vens" => $ven_users,
                "D_c" => $D_c,
                "N_c" => $N_c,
                "D_price" => $D_price,
                "N_price" => $N_price,    
                "bank_account" => $user->bank_account,   
                "phone" => $user->phone,   
                "bank_comment" => $user->bank_comment   
            ));
        }
    }



    http_response_code(200);
    echo json_encode(array(
        'status' => true, 
        'message' => 'Ok.', 
        'datas' => $datas,
        "price_all" => $price_all,
        "ven_coms"=>$ven_coms,
        'month' => DateThai_ym($DATE_MONTH),
    ));

}catch(PDOException $e){
    echo "Faild to connect to database" . $e->getMessage();
    http_response_code(400);
    echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
}