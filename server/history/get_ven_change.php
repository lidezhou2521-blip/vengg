<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // $user_id = $data->user_id;
    $user_id = $_SESSION['AD_ID']; 
    
    $datas = array();

    try{
        
        $sql = "SELECT vc.id , ven_month, ven_date1, ven_date2, ven_com_num_all, DN, u_role, user_id1, user_id2, vc.status
                FROM ven_change as vc  
                WHERE (vc.user_id2 = :user_id2 OR vc.user_id1 = :user_id1) AND (vc.status=1 OR vc.status=2)
				ORDER BY vc.create_at DESC				
                LIMIT 20";
        $query = $conn->prepare($sql);
        $query->bindParam(':user_id2',$user_id, PDO::PARAM_INT);
        $query->bindParam(':user_id1',$user_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                        //count($result)  for odbc
            foreach($result as $rs){
                $user1_name = '';
                $user2_name = '';
                // $user1_img = $_SERVER['REQUEST_SCHEME'].'://'. $_SERVER['HTTP_HOST'];
                // $user2_img = $_SERVER['REQUEST_SCHEME'].'://'. $_SERVER['HTTP_HOST'];

                /**ดึงข้อมูลprofile คนที่ 1 */
                $sql = "SELECT id, user_id, fname, profile.name, sname, img
                        FROM profile   
                        WHERE user_id = $rs->user_id1";
                $query = $conn->prepare($sql);
                $query->execute();
                $user1 = $query->fetch(PDO::FETCH_OBJ);
                $user1_name = $user1->fname.$user1->name.' '.$user1->sname;
                $user1_img = ($user1->img != null && $user1->img != '' && file_exists('../../uploads/users/' . $user1->img)) 
                                ? '../../uploads/users/'. $user1->img 
                                : '../../assets/images/profiles/nopic.png'; 
                   
                /**ดึงข้อมูลprofile คนที่ 2 */              
                $sql = "SELECT id, user_id, fname, profile.name, sname, img
                        FROM profile   
                        WHERE user_id = $rs->user_id2";
                $query = $conn->prepare($sql);
                $query->execute();
                $user2 = $query->fetch(PDO::FETCH_OBJ);
                $user2_name = $user2->fname.$user2->name.' '.$user2->sname;
                $user2_img = ($user2->img != null && $user2->img != '' && file_exists('../../uploads/users/' . $user2->img)) 
                                ? '../../uploads/users/'. $user2->img 
                                : '../../assets/images/profiles/nopic.png'; 
       
                array_push($datas,array(
                    'id'        => $rs->id,
                    'ven_month' => $rs->ven_month,
                    'ven_month_th' => DateThai_MY($rs->ven_month),
                    'ven_date1' => $rs->ven_date1,
                    'ven_date2' => $rs->ven_date2,
                    'ven_date1_th' => DateThai_full($rs->ven_date1),
                    'ven_date2_th' => DateThai_full($rs->ven_date2),
                    'ven_com_num_all' => $rs->ven_com_num_all,
                    'DN'        => $rs->DN,
                    'u_role'    => $rs->u_role,
                    'user1'     => $user1_name,
                    'user2'     => $user2_name,
                    'img1'      => $user1_img,
                    'img2'      => $user2_img,
                    'status'    => $rs->status,
                ));
            }
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สำเร็จ', 'respJSON' => $datas));
            exit;
        }else{

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ไม่พบข้อมูล ', 'respJSON' => $datas));
            exit;
        }
        
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}


