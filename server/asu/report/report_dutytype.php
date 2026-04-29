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
    $excluded_duties = isset($data->excluded_duties) ? $data->excluded_duties : array();
    
    try{
        // Calculate days in month
        $days_in_month = (int)date('t', strtotime($ven_month . '-01'));
        $month_num = date('m', strtotime($ven_month . '-01'));
        $year_be = date('Y', strtotime($ven_month . '-01')) + 543;

        // Get all duties for the month, ordered by person then duty type
        $sql = "SELECT v.id, v.user_id, v.ven_date, v.ven_name, v.u_role, v.price, v.DN,
                       v.ven_com_id, v.ven_com_idb, v.ven_com_num_all,
                       p.fname, p.name, p.sname, vns.srt as vns_srt, vn.srt as vn_srt
                FROM ven v
                INNER JOIN profile p ON v.user_id = p.id
                LEFT JOIN ven_name_sub vns ON v.vns_id = vns.id
                LEFT JOIN ven_name vn ON vns.ven_name_id = vn.id
                WHERE v.ven_month = :ven_month AND (v.status = 1 OR v.status = 2)
                ORDER BY p.st ASC, p.name ASC, vn.srt ASC, vns.srt ASC, v.ven_date ASC";
        $query = $conn->prepare($sql);
        $query->execute([':ven_month' => $ven_month]);
        $all_data = $query->fetchAll(PDO::FETCH_OBJ);

        // Group: person → duty type
        $persons_map = array();
        foreach ($all_data as $row) {
            $uid = $row->user_id;
            if (!isset($persons_map[$uid])) {
                $persons_map[$uid] = array(
                    'name'   => $row->fname . $row->name . ' ' . $row->sname,
                    'duties' => array()
                );
            }
            $price    = floatval($row->price);
            $key      = $row->ven_name;

            if (!isset($persons_map[$uid]['duties'][$key])) {
                $persons_map[$uid]['duties'][$key] = array(
                    'ven_name'      => $row->ven_name,
                    'no_claim'      => false,
                    'price_per'     => $price,
                    'days'          => array(),
                    'day_ids'       => array(),
                    'excluded_days' => array(),
                    'excl_ids'      => array(),
                    'total'         => 0,
                    'vn_srt'        => $row->vn_srt  !== null ? (int)$row->vn_srt  : 999,
                    'vns_srt'       => $row->vns_srt !== null ? (int)$row->vns_srt : 999
                );
            } else {
                if ($persons_map[$uid]['duties'][$key]['price_per'] == 0 && $price > 0) {
                    $persons_map[$uid]['duties'][$key]['price_per'] = $price;
                }
            }
            $day = (int)date('j', strtotime($row->ven_date));

            // เงื่อนไขไม่เบิก: ven_com_id ว่างหรือ array ว่าง, ven_com_idb ว่าง, ven_com_num_all ว่าง
            $com_id_raw = trim((string)$row->ven_com_id);
            $com_id_arr = json_decode($com_id_raw, true);
            $com_id_empty = ($com_id_raw === '' || $com_id_raw === 'null' || $com_id_raw === '[]'
                            || (is_array($com_id_arr) && count($com_id_arr) === 0)
                            || $com_id_arr === null);
            $com_idb = trim((string)$row->ven_com_idb);
            $com_num = trim((string)$row->ven_com_num_all);
            $is_no_claim_db = (
                ( $com_id_empty &&
                  ($com_idb === '' || $com_idb === 'null') &&
                  ($com_num === '' || $com_num === 'null') )
                || $price <= 0
            );

            // ตรวจสอบว่าถูกกากบาทไหม
            $is_excluded = false;
            foreach ($excluded_duties as $ex) {
                if ($ex->user_id == $row->user_id
                    && $ex->day == $day
                    && $ex->ven_name == $row->ven_name) {
                    $is_excluded = true;
                    break;
                }
            }

            if ($is_no_claim_db) {
                // ใส่ใน excluded_days
                if (!in_array($day, $persons_map[$uid]['duties'][$key]['excluded_days'])) {
                    $persons_map[$uid]['duties'][$key]['excluded_days'][] = $day;
                    $persons_map[$uid]['duties'][$key]['excl_ids'][$day]  = (int)$row->id;
                }
            } else {
                // ใส่ใน days ปกติ
                if (!in_array($day, $persons_map[$uid]['duties'][$key]['days'])) {
                    $persons_map[$uid]['duties'][$key]['days'][]        = $day;
                    $persons_map[$uid]['duties'][$key]['day_ids'][$day] = (int)$row->id;
                    $persons_map[$uid]['duties'][$key]['total']        += $price;
                }
            }
        }

        // Convert to indexed arrays and calculate grand total
        $persons = array();
        $grand_total = 0;
        foreach ($persons_map as $uid => $person) {
            // Sort duties by vn_srt then vns_srt
            $duties = array_values($person['duties']);
            usort($duties, function($a, $b){ 
                if($a['vn_srt'] == $b['vn_srt']) {
                    return $a['vns_srt'] - $b['vns_srt'];
                }
                return $a['vn_srt'] - $b['vn_srt']; 
            });
            foreach ($duties as &$d) {
                sort($d['days']);
                $grand_total += $d['total'];
                unset($d['vn_srt']);
                unset($d['vns_srt']);
            }
            $persons[] = array(
                'uid' => $uid,
                'name' => $person['name'],
                'duties' => $duties
            );
        }

        http_response_code(200);
        echo json_encode(array(
            'status' => true,
            'message' => 'Ok.',
            'ven_month' => $ven_month,
            'ven_month_th' => DateThai_MY($ven_month),
            'ven_month_num' => $month_num . '/' . $year_be,
            'days_in_month' => $days_in_month,
            'persons' => $persons,
            'grand_total' => $grand_total
        ));
        exit;

    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Error: ' . $e->getMessage()));
        exit;
    }
}
