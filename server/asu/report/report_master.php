<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    if(!isset($data->ven_month)){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'not ven_month'));
        exit;
    }
    $ven_month = $data->ven_month;
    
    try{
        // 1. Get all duty names (columns) for this month
        $sql_names = "SELECT DISTINCT vn.id, vn.name 
                      FROM ven v
                      INNER JOIN ven_name vn ON v.vn_id = vn.id
                      WHERE v.ven_month = :ven_month AND (v.status = 1 OR v.status = 2)
                      ORDER BY vn.srt ASC, vn.id ASC";
        $query_names = $conn->prepare($sql_names);
        $query_names->execute([':ven_month' => $ven_month]);
        $columns = $query_names->fetchAll(PDO::FETCH_ASSOC);

        // 2. Get all duties for this month
        $sql_duties = "SELECT v.ven_date, v.vn_id, p.fname, p.name, p.sname
                       FROM ven v
                       INNER JOIN profile p ON v.user_id = p.id
                       WHERE v.ven_month = :ven_month AND (v.status = 1 OR v.status = 2)
                       ORDER BY v.ven_date ASC, v.ven_time ASC";
        $query_duties = $conn->prepare($sql_duties);
        $query_duties->execute([':ven_month' => $ven_month]);
        $all_duties = $query_duties->fetchAll(PDO::FETCH_OBJ);

        // 3. Generate list of days in month
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, substr($ven_month, 5, 2), substr($ven_month, 0, 4));
        $rows = array();
        for ($day = 1; $day <= $days_in_month; $day++) {
            $date = $ven_month . '-' . sprintf("%02d", $day);
            $day_data = array(
                'date' => $date,
                'duties' => array()
            );
            
            foreach ($columns as $col) {
                $names = array();
                foreach ($all_duties as $d) {
                    if ($d->ven_date == $date && $d->vn_id == $col['id']) {
                        $names[] = $d->fname . $d->name . ' ' . $d->sname;
                    }
                }
                $day_data['duties'][$col['id']] = implode("\n", $names);
            }
            $rows[] = $day_data;
        }

        http_response_code(200);
        echo json_encode(array(
            'status' => true,
            'ven_month' => $ven_month,
            'ven_month_th' => DateThai_MY($ven_month),
            'columns' => $columns,
            'respJSON' => $rows
        ));
        exit;

    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Error: ' . $e->getMessage()));
        exit;
    }
}
