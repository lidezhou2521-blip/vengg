<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");
date_default_timezone_set("Asia/Bangkok");

include 'vendor/autoload.php';
// use PhpOffice\PhpWord\IOFactory;
// use PhpOffice\PhpWord\TemplateProcessor;

include_once "./dbconfig.php";
include_once "./function.php";

$error='';

$data = json_decode(file_get_contents("php://input"));

$DATE_MONTH = date($data->month);
$excluded_duties = isset($data->excluded_duties) ? $data->excluded_duties : array();

$users = array();
$vens = array();
$ven_users = array();
$ven_coms = array();


$datas = array();
$data_j = array();
$data_u = array();

$price_all = 0;
// http_response_code(200);
//         echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล', 'responseJSON' => $data->month));
// exit;
try{    

    $sql = "SELECT user_id,fname,name,sname,workgroup, phone,bank_account,bank_comment FROM profile WHERE status=10 ORDER BY st ASC";
    $query = $conn->prepare($sql);
    $query->execute();
    $users = $query->fetchAll(PDO::FETCH_OBJ);
    
    $sql = "SELECT * FROM ven WHERE ven_month='$DATE_MONTH' AND (status=1 OR status=2) ORDER BY user_id";
    $query = $conn->prepare($sql);
    $query->execute();
    $vens = $query->fetchAll(PDO::FETCH_OBJ);
    
    $sql = "SELECT 
                vn.`name` AS vn_name,
                vc.*
            FROM ven_com AS vc
            INNER JOIN ven_name AS vn ON vc.vn_id = vn.id
            WHERE ven_month='$DATE_MONTH' 
            ORDER BY ven_com_num ASC";
    $query = $conn->prepare($sql);
    $query->execute();
    $ven_coms = $query->fetchAll(PDO::FETCH_OBJ);

    $u_all_price = 0;

    foreach($users as $user){
        $ven_users = array();
        $D_c = 0;
        $N_c = 0;
        $D_price = 0;
        $N_price = 0;
        
        foreach($vens as $ven){
            if($ven->user_id == $user->user_id && $ven->price > 0){
                $is_excluded = false;
                foreach ($excluded_duties as $ex) {
                    if ($ex->user_id == $ven->user_id && $ex->day == (int)date('j', strtotime($ven->ven_date)) && $ex->ven_name == $ven->ven_name) {
                        $is_excluded = true;
                        break;
                    }
                }

                if (!$is_excluded) {
                    $u_all_price += $ven->price;
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
                        "ven_com_idb" => $ven->ven_com_idb,
                        "price" => $ven->price,
                    ));
                }
            }
        }
        
        if(count($ven_users) > 0){   
            $vcs_arr = array();   

            $price_sum = 0;
            foreach($ven_coms as $vcs){
                $vsc_id = $vcs->id;
                $vsc_name = $vcs->vn_name;
                $vsc_price = 0;
                $v_count = 0;

                foreach($ven_users as $vus){
                    if($vus['ven_com_idb'] == $vcs->id){
                        $vsc_price += $vus['price'];
                        ++$v_count;             
                        $price_sum = $price_sum + $vus['price'];      
                    } 
                } 
                $price = $vsc_price == 0 ? '-': Num_f($vsc_price);
                array_push($vcs_arr,array(
                    "id"        => $vcs->id,
                    "ven_name"  => $vsc_name,
                    "price"     => $vsc_price,
                    "price_th"  => $price,
                    "v_count"   => $v_count,
                ));
                
            }
            array_push($datas,array(
                "uid" => $user->user_id,
                "vcs_arr" => $vcs_arr,
                "name" => $user->fname.$user->name.' '.$user->sname,
                "workgroup" => $user->workgroup,
                "vens" => $ven_users,
                "D_c" => $D_c,
                "N_c" => $N_c,
                "D_price" => $D_price,
                "N_price" => $N_price,                   
                "price_sum"=>$price_sum,
                "phone" => $user->phone,
                "bank_account" => $user->bank_account,
                "bank_comment" => $user->bank_comment
            ));
        }
    }
    
    $ven_coms_arr =array(); 
    foreach($ven_coms as $vc){
        array_push($ven_coms_arr,array(
            "id"=>$vc->id,
            "ven_com_num"=>$vc->ven_com_num,
            "ven_com_date"=>$vc->ven_com_date
        ));
    }

    
    foreach($datas as $us){

        if($us['workgroup'] == 'ผู้พิพากษา'){
            
            array_push($data_j,array(
                "name"=>$us['name'],
                "bank_comment"=>$us['bank_comment'],
                "bank_account"=>$us['bank_account'],
                "price_sum"=>$us['price_sum'],
                "price_sum_th"=>Num_f($us['price_sum']),
                "vcs_arr"=>$us['vcs_arr'],
            ));
                        
        }else{
            array_push($data_u,array(
                "name"=>$us['name'],
                "bank_comment"=>$us['bank_comment'],
                "bank_account"=>$us['bank_account'],
                "price_sum"=>$us['price_sum'],
                "price_sum_th"=>Num_f($us['price_sum']),
                "vcs_arr"=>$us['vcs_arr'],
            ));
        }        
    }

    $data_j_sum = array(); 
    $data_u_sum = array();
    $data_all_sum = array();
    $price_all_sum = 0;
    foreach($ven_coms as $vc){
        $price_j_sum = 0;
        $price_u_sum = 0;
        $price = 0;
        $price_all_c = 0;
        foreach($data_j as $vcs){
            foreach($vcs['vcs_arr'] as $vcs_r){
                if($vcs_r['id'] == $vc->id){
                    $price += $vcs_r['price'];
                    $price_all_c +=  $vcs_r['price'];
                    $price_all_sum +=  $vcs_r['price'];
                }
            }
            $price_j_sum +=  $vcs['price_sum'];
        }
        array_push($data_j_sum,array(
            "id"=>$vc->id,
            "ven_com_num"=>$vc->ven_com_num,
            "ven_com_date"=>$vc->ven_com_date,
            "ven_name"=>$vc->vn_name,
            "price"=>Num_f($price),
        ));
        
        
        $price=0;
        foreach($data_u as $vcs){
            foreach($vcs['vcs_arr'] as $vcs_r){
                if($vcs_r['id'] == $vc->id){
                    $price += $vcs_r['price'];
                    $price_all_c +=  $vcs_r['price'];
                    $price_all_sum +=  $vcs_r['price'];
                }
            }
            $price_u_sum +=  $vcs['price_sum'];
        }
        array_push($data_u_sum,array(
            "id"=>$vc->id,
            "ven_com_num"=>$vc->ven_com_num,
            "ven_com_date"=>$vc->ven_com_date,
            "ven_name"=>$vc->vn_name,
            "price"=>Num_f($price),
        ));

        array_push($data_all_sum,array(
            "id"=>$vc->id,
            "ven_com_num"=>$vc->ven_com_num,
            "ven_com_date"=>$vc->ven_com_date,
            "ven_name"=>$vc->vn_name,
            "price"=>Num_f($price_all_c),
        ));
    }

    array_push($data_j_sum,array(
        "price"=>Num_f($price_j_sum)
    ));
    array_push($data_u_sum,array(
        "price"=>Num_f($price_u_sum)
    ));
    array_push($data_all_sum,array(
        "price"=>Num_f($price_all_sum),
    ));



    http_response_code(200);
    echo json_encode(array(
        'status' => true, 
        'message' => 'Ok.', 
        'datas' => $datas,
        'data_j' => $data_j,
        'data_u' => $data_u,
        'data_j_sum' => $data_j_sum,
        'data_u_sum' => $data_u_sum,
        'data_all_sum' => $data_all_sum,
        "price_all" => $price_all,
        "ven_coms"=>$ven_coms,
        'month' => DateThai_ym($DATE_MONTH),
    ));

    

}catch(Exception $e){
    // echo "Faild to connect to database" . $e->getMessage();
    http_response_code(400);
    echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
}

