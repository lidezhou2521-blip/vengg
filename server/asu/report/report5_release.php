<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $vcid = $data->vcid;
    $date_start = isset($data->date_start) ? $data->date_start : '';
    $date_end = isset($data->date_end) ? $data->date_end : '';
    $excluded_duties = isset($data->excluded_duties) ? $data->excluded_duties : array();
    $datas = array();

    try{
        $sql = "SELECT * FROM ven_com WHERE id = :vcid";
        $query = $conn->prepare($sql);
        $query->execute([':vcid' => $vcid]);
        $vc = $query->fetch(PDO::FETCH_OBJ);

        if(!$vc) {
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูลคำสั่ง'));
            exit;
        }

        $ven_com = [
            "id" => $vc->id,
            "ven_com_num" => $vc->ven_com_num,
            "ven_com_date" => $vc->ven_com_date,
            "ven_month" => $vc->ven_month,
            "ven_month_th" => DateThai_MY($vc->ven_month),
            "vn_id" => $vc->vn_id,
            "status" => $vc->status,
            "date_start" => $date_start,
            "date_end" => $date_end
        ];

        $sql_date = "";
        if ($date_start != '' && $date_end != '') {
            $sql_date = " AND v.ven_date >= :date_start AND v.ven_date <= :date_end ";
        }

        // Fetch Release duties only
        $sql = "SELECT v.*, p.fname, p.name, p.sname, p.workgroup, p.dep, vn.name as vn_name
                FROM ven as v
                INNER JOIN `profile` AS p ON p.id = v.user_id
                LEFT JOIN ven_name AS vn ON v.vn_id = vn.id
                WHERE v.ven_month = :ven_month 
                    AND (v.status=1 OR v.status=2) 
                    AND (vn.name LIKE '%เวรเปิดทำการพิจารณาคำร้องขอปล่อยชั่วคราว%')
                    $sql_date
                ORDER BY v.ven_date ASC, v.ven_time ASC";
        $query = $conn->prepare($sql);
        $params = [':ven_month' => $vc->ven_month];
        if ($date_start != '' && $date_end != '') {
            $params[':date_start'] = $date_start;
            $params[':date_end'] = $date_end;
        }
        $query->execute($params);
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        $vd = array();
        foreach($result as $rs){
            if(!in_array($rs->ven_date, $vd)){
                array_push($vd, $rs->ven_date);
            }
        }

        foreach($vd as $date){
            $u_namej = array();
            $u_staff = array();
            $vt = '';
            $note = '';

            foreach($result as $rs){
                if($rs->ven_date == $date){
                    $is_excluded = false;
                    foreach ($excluded_duties as $ex) {
                        if ($ex->user_id == $rs->user_id
                            && $ex->day == (int)date('j', strtotime($rs->ven_date))
                            && $ex->ven_name == $rs->ven_name) {
                            $is_excluded = true;
                            break;
                        }
                    }
                    if ($is_excluded) continue;

                    $name = $rs->fname . $rs->name . ' ' . $rs->sname;
                    $vt = substr($rs->ven_time, 0, 5);
                    
                    if($note == '') {
                        $note = $rs->vn_name;
                    } else if (!str_contains($note, $rs->vn_name)) {
                        $note .= " และ " . $rs->vn_name;
                    }
                        
                    if(str_contains($rs->u_role, 'ผู้พิพากษา')){
                        array_push($u_namej, $name);
                    } else {
                        $duty = $rs->u_role;
                        array_push($u_staff, array(
                            'name' => $name,
                            'duty' => $duty
                        ));
                    }
                }
            }

            array_push($datas, array(
                'ven_date' => $date,
                'ven_time' => $vt,
                'u_namej' => $u_namej,
                'u_staff' => $u_staff,
                'note' => $note
            ));
        }
        
        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'สำเร็จ', 'respJSON' => $datas, 'vc' => $ven_com));
        exit;

    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}
