<?php
include "C:/xampp/htdocs/vengg/server/connect.php";

try {
    $conn->beginTransaction();

    // 1. Add 50 users
    $userIds = [];
    for ($i = 1; $i <= 50; $i++) {
        $id = 2000000000 + $i; // Generate a unique ID
        $user_id = (string)$id;
        $fname = "ทดสอบ";
        $name = "คนที่ $i";
        $sname = "นามสกุล";
        
        $sql = "INSERT INTO profile (id, user_id, fname, name, sname, status, created_at) 
                VALUES (:id, :user_id, :fname, :name, :sname, 10, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':user_id' => $user_id,
            ':fname' => $fname,
            ':name' => $name,
            ':sname' => $sname
        ]);
        $userIds[] = $id;
    }

    echo "Inserted 50 users.\n";

    // 2. Prepare duty participants (30 people per point)
    $sql_vns = "SELECT id, ven_name_id FROM ven_name_sub";
    $vns_list = $conn->query($sql_vns)->fetchAll(PDO::FETCH_ASSOC);

    foreach ($vns_list as $vns) {
        $vns_id = $vns['id'];
        $vn_id = $vns['ven_name_id'];
        
        // Take 30 random users
        $shuffled = $userIds;
        shuffle($shuffled);
        $selected = array_slice($shuffled, 0, 30);
        
        foreach ($selected as $index => $uid) {
            $sql_vu = "INSERT INTO ven_user (user_id, `order`, vn_id, vns_id, create_at) 
                       VALUES (:user_id, :order, :vn_id, :vns_id, NOW())";
            $stmt = $conn->prepare($sql_vu);
            $stmt->execute([
                ':user_id' => $uid,
                ':order' => $index + 1,
                ':vn_id' => $vn_id,
                ':vns_id' => $vns_id
            ]);
        }
        echo "Assigned 30 users to vns_id $vns_id.\n";
    }

    $conn->commit();
    echo "Done!";
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "Error: " . $e->getMessage();
}
?>
