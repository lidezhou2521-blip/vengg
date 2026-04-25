<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../connect_gdms.php";
include "../function.php";

// $data = json_decode(file_get_contents("php://input"));


// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datas = array();

    try{
        $sql = "SELECT u.*
                FROM users as u";
        $query = $conn_gdms->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                        
            foreach($result as $rs){
                $res_profile = '';
                $sql = "SELECT p.*
                        FROM profile as p
                        WHERE id='$rs->id'";
                $query = $conn->prepare($sql);
                $query->execute();
                if($query->rowCount() == 0){
                    $id = $rs->id;
                    $sql = "INSERT INTO profile(id, user_id, name, dep, st, status , created_at, updated_at) 
                            VALUE(:id, :user_id, :name, :dep, :st, 10, :created_at, :updated_at);";      
                    $date_time = Date("Y-m-d h:i:s");

                    $query = $conn->prepare($sql);
                    $query->bindParam(':id',$id, PDO::PARAM_INT);
                    $query->bindParam(':user_id',$id, PDO::PARAM_INT);
                    $query->bindParam(':name',$rs->name, PDO::PARAM_STR);
                    $query->bindParam(':dep',$rs->position, PDO::PARAM_STR);
                    $query->bindParam(':st',$st, PDO::PARAM_STR);
                    $query->bindParam(':created_at',$date_time, PDO::PARAM_STR);
                    $query->bindParam(':updated_at',$date_time, PDO::PARAM_STR);       
                    $query->execute();
                }


            }
        
        }

        $sql = "SELECT p.*
                FROM profile as p
                ORDER BY p.st ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){  
            foreach ($result as $rs) {
                $img_link = '../../assets/images/profiles/nopic.png';
                if ($rs->img && file_exists('../../uploads/users/' . $rs->img)) {
                    $img_link = '../../uploads/users/' . $rs->img;
                }
                $data = [
                    'uid' => $rs->id,
                    'name' => $rs->fname . $rs->name . ' ' . $rs->sname,
                    'dep' => $rs->dep,
                    'img' => $img_link,
                    'status' => $rs->status,
                    'st' => $rs->st
                ];
                $datas[] = $data;
            }

            $response = array(
                'status' => true,
                'message' => 'Success',
                'respJSON' => $datas
            );

            
        }else {
            $response = array(
                'status' => false,
                'message' => 'No data found'
            );
        }

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
        
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Error: ' . $e->getMessage()));
        exit;
    }
}