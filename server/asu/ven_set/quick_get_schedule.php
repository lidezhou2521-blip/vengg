<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ven_month = $data->ven_month;
    $vc_id     = isset($data->vc_id) ? $data->vc_id : '';
    $vn_id     = isset($data->vn_id) ? $data->vn_id : '';
    $vns_id    = isset($data->vns_id) ? $data->vns_id : '';

    try {
        // Get all days in the month
        $year  = substr($ven_month, 0, 4);
        $month = substr($ven_month, 5, 2);
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);

        $schedule = array();
        for ($d = 1; $d <= $days_in_month; $d++) {
            $date = $year . '-' . $month . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
            $schedule[$date] = array(
                'ven_date' => $date,
                'day_name' => '',
                'assignments' => array()
            );
        }

        // Get existing duties for this month/command/position
        if ($vc_id && $vns_id) {
            $sql = "SELECT v.id, v.ven_date, v.user_id, v.u_role, v.DN, v.color, v.comment, v.status,
                        p.fname, p.name, p.sname
                    FROM ven AS v
                    INNER JOIN `profile` AS p ON p.user_id = v.user_id
                    WHERE v.ven_month = :ven_month
                        AND v.ven_com_idb = :vc_id
                        AND v.vns_id = :vns_id
                        AND (v.status = 1 OR v.status = 2)
                    ORDER BY v.ven_date ASC, v.ven_time ASC";
            $query = $conn->prepare($sql);
            $query->bindParam(':ven_month', $ven_month);
            $query->bindParam(':vc_id', $vc_id);
            $query->bindParam(':vns_id', $vns_id);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_OBJ);

            foreach ($result as $rs) {
                if (isset($schedule[$rs->ven_date])) {
                    array_push($schedule[$rs->ven_date]['assignments'], array(
                        'id' => $rs->id,
                        'user_id' => $rs->user_id,
                        'name' => $rs->fname . $rs->name . ' ' . $rs->sname,
                        'u_role' => $rs->u_role,
                        'DN' => $rs->DN,
                        'color' => $rs->color,
                        'comment' => $rs->comment,
                        'status' => $rs->status
                    ));
                }
            }
        }

        // Convert to indexed array
        $schedule_arr = array_values($schedule);

        http_response_code(200);
        echo json_encode(array(
            'status' => true,
            'message' => 'สำเร็จ',
            'schedule' => $schedule_arr,
            'days_in_month' => $days_in_month
        ));
        exit;

    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Error: ' . $e->getMessage()));
        exit;
    }
}
