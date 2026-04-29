<?php
include('server/connect.php');
$ven_month = '2026-05';

// หาชื่อจงกลก่อน
$sql = "SELECT DISTINCT v.user_id, p.fname, p.name, p.sname
        FROM ven v
        INNER JOIN profile p ON v.user_id = p.id
        WHERE v.ven_month = :ven_month
        AND (p.name LIKE '%จงกล%' OR p.fname LIKE '%จงกล%' OR p.sname LIKE '%จงกล%')";
$q = $conn->prepare($sql);
$q->execute([':ven_month' => $ven_month]);
$people = $q->fetchAll(PDO::FETCH_OBJ);

echo "--- ค้นหาจงกล ---\n";
foreach ($people as $p) {
    echo "uid={$p->user_id} | {$p->fname}{$p->name} {$p->sname}\n";
}

echo "\n--- เวรวันที่ 31 เดือน $ven_month ทั้งหมด ---\n";
$sql2 = "SELECT v.user_id, v.ven_date, v.ven_name, v.u_role, v.price, v.status, p.fname, p.name, p.sname
         FROM ven v
         INNER JOIN profile p ON v.user_id = p.id
         WHERE v.ven_month = :ven_month
         AND v.ven_date = '2026-05-31'
         ORDER BY p.st, p.name";
$q2 = $conn->prepare($sql2);
$q2->execute([':ven_month' => $ven_month]);
$rows = $q2->fetchAll(PDO::FETCH_OBJ);
echo "พบ: " . count($rows) . " รายการ\n";
foreach ($rows as $r) {
    echo "{$r->fname}{$r->name} {$r->sname} | {$r->ven_date} | {$r->ven_name} | price:{$r->price} | status:{$r->status}\n";
}

echo "\n--- เวรทั้งหมดของเดือน รายการที่ status ไม่ใช่ 1 หรือ 2 ---\n";
$sql3 = "SELECT v.user_id, v.ven_date, v.ven_name, v.status, p.fname, p.name, p.sname
         FROM ven v
         INNER JOIN profile p ON v.user_id = p.id
         WHERE v.ven_month = :ven_month
         AND v.status NOT IN (1,2)
         ORDER BY v.ven_date, p.name";
$q3 = $conn->prepare($sql3);
$q3->execute([':ven_month' => $ven_month]);
$rows3 = $q3->fetchAll(PDO::FETCH_OBJ);
echo "พบสถานะอื่น: " . count($rows3) . " รายการ\n";
foreach ($rows3 as $r) {
    echo "{$r->fname}{$r->name} {$r->sname} | {$r->ven_date} | {$r->ven_name} | status:{$r->status}\n";
}
