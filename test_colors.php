<?php
include('pages/fnu/ven/api/dbconfig.php');
// ดู ven_name และ ven_name_sub มีสีอะไรบ้าง
$sql = "SELECT vn.id, vn.name, vn.srt FROM ven_name vn ORDER BY vn.srt";
$q = $conn->prepare($sql); $q->execute();
$rows = $q->fetchAll(PDO::FETCH_OBJ);
echo "=== ven_name ===\n";
foreach($rows as $r) echo "id:{$r->id} | {$r->name}\n";

// ดู ven_name_sub สีอะไร
$sql2 = "SELECT vns.id, vns.ven_name_id, vns.name, vns.color, vns.srt FROM ven_name_sub vns ORDER BY vns.ven_name_id, vns.srt";
$q2 = $conn->prepare($sql2); $q2->execute();
$rows2 = $q2->fetchAll(PDO::FETCH_OBJ);
echo "\n=== ven_name_sub (with colors) ===\n";
foreach($rows2 as $r) echo "vn_id:{$r->ven_name_id} | {$r->name} | color:{$r->color}\n";
