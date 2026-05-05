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
        $sql_all = "SELECT v.ven_date, v.user_id, v.u_role, v.vn_id, 
                           COALESCE(vn.name, v.ven_name) as ven_name, 
                           p.fname, p.name, p.sname, p.workgroup as p_workgroup, p.dep, 
                           vns.name as vns_group, vns.srt as vns_srt, vns.Group_id as vns_group_id,
                           COALESCE(vu.order, p.st, 999) as `order`
                    FROM ven v
                    INNER JOIN profile p ON v.user_id = p.id
                    LEFT JOIN ven_name vn ON v.vn_id = vn.id
                    LEFT JOIN ven_user vu ON v.user_id = vu.user_id AND v.vn_id = vu.vn_id
                    LEFT JOIN ven_name_sub vns ON (vns.id = vu.vns_id OR (vns.name = v.u_role COLLATE utf8_general_ci AND vns.ven_name_id = v.vn_id))
                    WHERE v.ven_month = :ven_month AND (v.status = 1 OR v.status = 2)
                    ORDER BY vns.srt ASC, vu.order ASC, p.name ASC, v.ven_date ASC";
        $query_all = $conn->prepare($sql_all);
        $query_all->execute([':ven_month' => $ven_month]);
        $all_data = $query_all->fetchAll(PDO::FETCH_OBJ);

        // 3. Group by vnu_group (กลุ่มหน้าที่) and user
        $groups = array();
        foreach ($all_data as $row) {
            // ใช้ vns_group (กลุ่มหน้าที่) ก่อน ถ้าไม่มีค่อยใช้กลุ่มงานในโปรไฟล์
            $wg = $row->vns_group ? $row->vns_group : ($row->p_workgroup ? $row->p_workgroup : 'ไม่ระบุกลุ่ม');
            
            if (!isset($groups[$wg])) {
                $groups[$wg] = array(
                    'vns_srt' => $row->vns_srt,
                    'vns_group_id' => $row->vns_group_id, // New field
                    'users' => array()
                );
            }
            if (!isset($groups[$wg]['users'][$row->user_id])) {
                $groups[$wg]['users'][$row->user_id] = array(
                    'name'   => $row->fname . $row->name . ' ' . $row->sname,
                    'u_role' => $row->u_role,
                    'order'  => $row->order,
                    'dates'  => array()
                );
            }
            // Store date, ven_name, and vn_id
            $groups[$wg]['users'][$row->user_id]['dates'][] = array(
                'date' => $row->ven_date,
                'ven_name' => $row->ven_name,
                'vn_id' => $row->vn_id
            );
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
