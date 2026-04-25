<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";



$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vc_id = $data->vcid;

    $datas = array();
    $heads = [
        "ven_month_th" => '',
        "vc_name" => '',
        "vc_num" => '',
        "vc_date" => ''
    ];

    try {


        $sql = "SELECT 
                    vn.name AS vn_name, 
                    vc.ven_com_num AS vc_num,
                    vc.ven_com_date AS vc_date,
                    v.*, 
                    p.fname, p.name,p.sname, p.dep, p.workgroup
                FROM ven AS v
                INNER JOIN `profile` AS p ON p.user_id = v.user_id
                INNER JOIN ven_name AS vn ON v.vn_id = vn.id
                RIGHT JOIN ven_com AS vc ON v.ven_com_idb = vc.id
                WHERE v.ven_com_idb = $vc_id AND (v.status=1 OR v.status=2)
                ORDER BY v.ven_date ASC, v.ven_time ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);


        if ($query->rowCount() > 0) {
            $heads = [
                "vc_name" => $result[0]->vn_name,
                "vc_num" => $result[0]->vc_num,
                "vc_date" => DateThai_full($result[0]->vc_date),
                "ven_month_th" => DateThai_full($result[0]->ven_month)
            ];

            $datas = []; // สร้างตัวแปรเพื่อเก็บข้อมูลที่จัดกลุ่มแล้ว

            $groupedData = []; // สร้างตัวแปรเพื่อเก็บข้อมูลที่จัดกลุ่มแยกตาม ven_date

            // จัดกลุ่มข้อมูลตาม ven_date
            foreach ($result as $rs) {
                $groupedData[$rs->ven_date][] = $rs;
            }

            // สร้างข้อมูลใหม่ที่จัดกลุ่มแล้ว
            foreach ($groupedData as $vd => $group) {
                $vts = [];
                $u_namejs = [];
                $u_names = [];
                $cmts = [];

                foreach ($group as $rs) {
                    if ($rs->u_role == 'ผู้พิพากษา') {
                        $u_namejs[] = $rs->fname . $rs->name . " " . $rs->sname;
                        $vts[] = $rs->ven_time;
                    } else {

                        $u_names[] = $rs->fname . $rs->name . " " . $rs->sname;
                        $cmts[] = $rs->u_role;
                    }
                }

                $datas[] = [
                    'ven_date' => $vd,
                    'ven_date_th' => DateThai_full($vd),
                    'ven_time' => $vts,
                    'u_namej' => $u_namejs,
                    'u_name' => $u_names,
                    'cmt' => $cmts,
                ];
            }

            usort($datas, function ($a, $b) {
                return strtotime($a['ven_date']) <=> strtotime($b['ven_date']);
            });



            http_response_code(200);
            echo json_encode(array(
                'status' => true,
                'message' => ' สำเร็จ ',
                'heads' => $heads,
                'respJSON' => $datas
            ));
            exit;
        }

        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล'));
        exit;
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}
