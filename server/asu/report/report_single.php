<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $ven_month = $data->ven_month;
    $search_name = $data->search_name; // e.g. 'หมายจับ-ค้น'
    
    try{
        // 1. Get duties for this month matching the name
        $sql = "SELECT v.ven_date, p.fname, p.name, p.sname, vn.name as vn_name
                FROM ven v
                INNER JOIN profile p ON v.user_id = p.id
                INNER JOIN ven_name vn ON v.vn_id = vn.id
                WHERE v.ven_month = :ven_month 
                AND vn.name LIKE :search_name
                AND (v.status = 1 OR v.status = 2)
                ORDER BY v.ven_date ASC, v.ven_time ASC";
        $query = $conn->prepare($sql);
        $query->execute([
            ':ven_month' => $ven_month,
            ':search_name' => "%$search_name%"
        ]);
        $all_duties = $query->fetchAll(PDO::FETCH_OBJ);

        // 2. Days in month
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, substr($ven_month, 5, 2), substr($ven_month, 0, 4));
        $rows = array();
        for ($day = 1; $day <= $days_in_month; $day++) {
            $date = $ven_month . '-' . sprintf("%02d", $day);
            $names = array();
            foreach ($all_duties as $d) {
                if ($d->ven_date == $date) {
                    $names[] = $d->fname . $d->name . ' ' . $d->sname;
                }
            }
            if(count($names) > 0){
                $rows[] = array(
                    'date' => $date,
                    'names' => implode(", ", $names)
                );
            }
        }

        http_response_code(200);
        echo json_encode(array(
            'status' => true,
            'ven_month_th' => DateThai_MY($ven_month),
            'search_name' => $search_name,
            'respJSON' => $rows
        ));
        exit;

    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Error: ' . $e->getMessage()));
        exit;
    }
}
