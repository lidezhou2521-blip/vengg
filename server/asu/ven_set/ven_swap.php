<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_a = intval($data->id_a);
    $id_b = intval($data->id_b);

    if (!$id_a || !$id_b || $id_a === $id_b) {
        echo json_encode(['status' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
        exit;
    }

    try {
        // ดึงข้อมูลเวรทั้งสอง (ใช้ OR แทน IN เพื่อหลีกเลี่ยงปัญหา PDO binding)
        $sql = "SELECT id, user_id, u_role, DN, ven_com_idb, ven_name
                FROM ven
                WHERE id = :id_a OR id = :id_b";
        $query = $conn->prepare($sql);
        $query->bindParam(':id_a', $id_a, PDO::PARAM_INT);
        $query->bindParam(':id_b', $id_b, PDO::PARAM_INT);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_OBJ);

        if (count($rows) !== 2) {
            echo json_encode(['status' => false, 'message' => 'ไม่พบข้อมูลเวรที่ระบุ']);
            exit;
        }

        $ven_a = null;
        $ven_b = null;
        foreach ($rows as $r) {
            if ($r->id == $id_a) $ven_a = $r;
            if ($r->id == $id_b) $ven_b = $r;
        }

        // ตรวจสอบเงื่อนไข: ประเภทเวร (DN) ต้องตรงกัน
        if ($ven_a->DN !== $ven_b->DN) {
            echo json_encode([
                'status'  => false,
                'message' => 'ประเภทเวรไม่ตรงกัน (' . $ven_a->DN . ' ≠ ' . $ven_b->DN . ')\nกรุณาแลกเฉพาะเวรประเภทเดียวกัน'
            ]);
            exit;
        }

        // ตรวจสอบเงื่อนไข: คำสั่ง (ven_com_idb) ต้องตรงกัน
        $com_a = trim((string)$ven_a->ven_com_idb);
        $com_b = trim((string)$ven_b->ven_com_idb);
        if ($com_a !== $com_b) {
            echo json_encode([
                'status'  => false,
                'message' => 'คำสั่งเวรไม่ตรงกัน\nกรุณาแลกเฉพาะเวรที่อยู่ในคำสั่งเดียวกัน'
            ]);
            exit;
        }

        // ตรวจสอบเงื่อนไข: ผู้พิพากษาแลกได้เฉพาะกับผู้พิพากษา
        $sql_wg = "SELECT p.user_id, p.workgroup FROM profile p
                   WHERE p.user_id = :uid_a OR p.user_id = :uid_b";
        $q_wg = $conn->prepare($sql_wg);
        $q_wg->bindParam(':uid_a', $ven_a->user_id, PDO::PARAM_INT);
        $q_wg->bindParam(':uid_b', $ven_b->user_id, PDO::PARAM_INT);
        $q_wg->execute();
        $wg_rows = $q_wg->fetchAll(PDO::FETCH_OBJ);

        $wg_a = isset($wg_rows[0]) ? trim((string)$wg_rows[0]->workgroup) : '';
        $wg_b = isset($wg_rows[1]) ? trim((string)$wg_rows[1]->workgroup) : '';
        if (isset($wg_rows[0]) && isset($wg_rows[1])) {
            foreach ($wg_rows as $wg) {
                if ($wg->user_id == $ven_a->user_id) $wg_a = trim((string)$wg->workgroup);
                if ($wg->user_id == $ven_b->user_id) $wg_b = trim((string)$wg->workgroup);
            }
        }

        $a_is_judge = (mb_strpos($wg_a, 'ผู้พิพากษา') !== false);
        $b_is_judge = (mb_strpos($wg_b, 'ผู้พิพากษา') !== false);

        if ($a_is_judge !== $b_is_judge) {
            echo json_encode([
                'status'  => false,
                'message' => 'ผู้พิพากษาแลกได้เฉพาะกับผู้พิพากษาเท่านั้น\nตำแหน่งอื่นๆ แลกได้เฉพาะกันเอง'
            ]);
            exit;
        }

        $update_at = date("Y-m-d H:i:s");

        // Swap: สลับ user_id และ u_role ระหว่างสองเวร
        $sql_upd = "UPDATE ven SET user_id = :user_id, u_role = :u_role, update_at = :update_at WHERE id = :id";

        $q = $conn->prepare($sql_upd);
        $q->bindParam(':user_id',   $ven_b->user_id, PDO::PARAM_INT);
        $q->bindParam(':u_role',    $ven_b->u_role,  PDO::PARAM_STR);
        $q->bindParam(':update_at', $update_at,      PDO::PARAM_STR);
        $q->bindParam(':id',        $id_a,           PDO::PARAM_INT);
        $q->execute();

        $q2 = $conn->prepare($sql_upd);
        $q2->bindParam(':user_id',   $ven_a->user_id, PDO::PARAM_INT);
        $q2->bindParam(':u_role',    $ven_a->u_role,  PDO::PARAM_STR);
        $q2->bindParam(':update_at', $update_at,      PDO::PARAM_STR);
        $q2->bindParam(':id',        $id_b,           PDO::PARAM_INT);
        $q2->execute();

        echo json_encode(['status' => true, 'message' => 'แลกเวรสำเร็จ']);
        exit;
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
        exit;
    }
}
