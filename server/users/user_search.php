<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($data->q) && !empty($data->q)) {
        $q = $data->q;

        $datas = array();

        try {
            $sql = "SELECT p.*
                    FROM profile AS p 
                    LEFT JOIN user AS u ON p.user_id = u.id
                    WHERE p.name LIKE :q OR p.sname LIKE :q2
                    ORDER BY p.name ASC";
            $query = $conn->prepare($sql);
            $query->bindValue(':q', "%$q%", PDO::PARAM_STR);
            $query->bindValue(':q2', "%$q%", PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($result)) {
                foreach ($result as $rs) {
                    $img_link = '../../assets/images/profiles/nopic.png';

                    if ($rs['img'] != null && $rs['img'] != '' && file_exists('../../uploads/users/' . $rs['img'])) {
                        $img_link = '../../uploads/users/' . $rs['img'];
                    }

                    $data = array(
                        'uid' => $rs['user_id'],
                        'name' => $rs['fname'] . $rs['name'] . ' ' . $rs['sname'],
                        'dep' => $rs['dep'],
                        'img' => $img_link,
                        'status' => $rs['status'],
                        'st' => $rs['st']
                    );

                    $datas[] = $data;
                }

                $response = [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $datas
                ];
                http_response_code(200);
                echo json_encode($response);
                exit;
            }

            $response = [
                'status' => false,
                'message' => 'No data found',
                'data' => $datas
            ];
            http_response_code(200);
            echo json_encode($response);
            exit;
        } catch (PDOException $e) {
            $response = [
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
            http_response_code(500);
            echo json_encode($response);
            exit;
        }
    } else {
        $response = [
            'status' => false,
            'message' => 'No data provided'
        ];
        http_response_code(200);
        echo json_encode($response);
        exit;
    }
}
?>
