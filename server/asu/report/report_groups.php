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
        // 1. Get all unique dates that have duties this month
        $sql_dates = "SELECT DISTINCT ven_date 
                      FROM ven 
                      WHERE ven_month = :ven_month AND (status = 1 OR status = 2)
                      ORDER BY ven_date ASC";
        $query_dates = $conn->prepare($sql_dates);
        $query_dates->execute([':ven_month' => $ven_month]);
        $dates = $query_dates->fetchAll(PDO::FETCH_ASSOC);

        // 2. Get all duties and user profile info
        $sql_all = "SELECT v.ven_date, v.user_id, v.u_role, p.fname, p.name, p.sname, p.workgroup, p.dep
                    FROM ven v
                    INNER JOIN profile p ON v.user_id = p.id
                    WHERE v.ven_month = :ven_month AND (v.status = 1 OR v.status = 2)
                    ORDER BY p.workgroup ASC, p.name ASC, v.ven_date ASC";
        $query_all = $conn->prepare($sql_all);
        $query_all->execute([':ven_month' => $ven_month]);
        $all_data = $query_all->fetchAll(PDO::FETCH_OBJ);

        // 3. Group by workgroup and user
        $groups = array();
        foreach ($all_data as $row) {
            $wg = $row->workgroup ? $row->workgroup : 'ไม่ระบุกลุ่มงาน';
            if (!isset($groups[$wg])) {
                $groups[$wg] = array();
            }
            if (!isset($groups[$wg][$row->user_id])) {
                $groups[$wg][$row->user_id] = array(
                    'name'   => $row->fname . $row->name . ' ' . $row->sname,
                    'u_role' => $row->u_role,
                    'dates'  => array()
                );
            }
            if (!in_array($row->ven_date, $groups[$wg][$row->user_id]['dates'])) {
                $groups[$wg][$row->user_id]['dates'][] = $row->ven_date;
            }
        }

        http_response_code(200);
        echo json_encode(array(
            'status' => true,
            'ven_month_th' => DateThai_MY($ven_month),
            'dates' => $dates,
            'groups' => $groups
        ));
        exit;

    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Error: ' . $e->getMessage()));
        exit;
    }
}
