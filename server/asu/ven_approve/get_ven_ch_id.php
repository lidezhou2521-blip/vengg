<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";


$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $data->id;
    $datas = array();

    try{
        
        $sql = "SELECT vc.id , ven_month, ven_date1, ven_date2, ven_com_num_all, DN, u_role, user_id1, user_id2, vc.status
                FROM ven_change as vc  
                WHERE vc.id=:id";
        $query = $conn->prepare($sql);
        $query->bindParam(':id',$id, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                        //count($result)  for odbc
            foreach($result as $rs){
                // $rs->DN == 'กลางวัน' ? $d = '☀️' : $d = '🌙';
                $sql = "SELECT id, user_id, fname, profile.name, sname, img
                        FROM profile   
                        WHERE user_id = :user_id";
                $query = $conn->prepare($sql);
                $query->bindParam(':user_id',$rs->user_id1, PDO::PARAM_INT);
                $query->execute();
                $user1 = $query->fetch(PDO::FETCH_OBJ);

                $sql = "SELECT id, user_id, fname, profile.name, sname, img
                        FROM profile   
                        WHERE user_id = :user_id";
                $query = $conn->prepare($sql);
                $query->bindParam(':user_id',$rs->user_id2, PDO::PARAM_INT);
                $query->execute();
                $user2 = $query->fetch(PDO::FETCH_OBJ);

                // $user1->img ? $img1 = $user1->img : $img1 = 'none.png';
                $user1_img = ($user1->img != null && $user1->img != '' && file_exists('../../../uploads/users/' . $user1->img)) 
                                ? '../../uploads/users/'. $user1->img 
                                : '../../assets/images/profiles/nopic.png'; 
                $user2_img = ($user2->img != null && $user2->img != '' && file_exists('../../../uploads/users/' . $user2->img)) 
                                ? '../../uploads/users/'. $user2->img 
                                : '../../assets/images/profiles/nopic.png'; 
                

                array_push($datas,array(
                    'id'    => $rs->id,
                    'ven_month' => $rs->ven_month,
                    'ven_date1' => $rs->ven_date1,
                    'ven_date2' => $rs->ven_date2,
                    'ven_com_num_all' => $rs->ven_com_num_all,
                    'DN' => $rs->DN,
                    'u_role'    => $rs->u_role,
                    'user1'     => $user1->fname.$user1->name.' '.$user1->sname,
                    'img1'      => $user1_img,
                    'user2'     => $user2->fname.$user2->name.' '.$user2->sname,
                    'img2'      => $user2_img,
                    'status'    => $rs->status,
                ));
            }
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สำเร็จ', 'respJSON' => $datas,'ss'=>$result));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล ','respJSON' => $datas ));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}