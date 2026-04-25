<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

require_once "../../connect.php";
require_once "../../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $q = $data->q;
    $datas = array();

    try {
        $sql = "SELECT l.id, l.name, l.status
                FROM line as l
                WHERE l.name LIKE :q
                ORDER BY l.name ASC";
        $query = $conn->prepare($sql);
        $query->bindValue(':q', '%' . $q . '%', PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
            foreach ($result as $rs) {
                array_push($datas, array(
                    'id' => $rs->id,
                    'name' => $rs->name,
                    'status' => $rs->status,
                ));
            }
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สำเร็จ', 'respJSON' => $datas));
        } else {
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล'));
        }
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()));
    }
}
?>
