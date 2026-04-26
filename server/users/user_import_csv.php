<?php
/**
 * Script to import users from person_split.csv
 */

require_once __DIR__ . "/../connect.php";

$csv_file = 'C:/Users/COJ/Downloads/person_split.csv';

if (!file_exists($csv_file)) {
    die("Error: File not found at $csv_file");
}

$handle = fopen($csv_file, "r");
if ($handle === FALSE) {
    die("Error: Could not open file.");
}

// Skip header
fgetcsv($handle);

$count = 0;
$errors = [];

while (($data = fgetcsv($handle)) !== FALSE) {
    // If line is empty or doesn't have enough columns, skip
    if (count($data) < 8 || empty(trim($data[1]))) {
        continue;
    }

    // Convert encoding from CP874 to UTF-8
    foreach ($data as $key => $value) {
        $data[$key] = iconv('CP874', 'UTF-8//IGNORE', trim($value));
    }

    $fname     = $data[0];
    $name      = $data[1];
    $sname     = $data[2];
    $dep       = $data[3];
    $workgroup = $data[4];
    $phone     = $data[5];
    $username  = $data[6];
    $password  = $data[7];

    // Handle scientific notation or numeric strings
    if (is_numeric($username)) {
        $username = (string)$username;
        // If it looks like 6.55e8, we might need a more robust way to read it.
        // But fgetcsv should read it as is if it's not converted by Excel.
    }

    try {
        // Check if username exists
        $sql = "SELECT id FROM user WHERE username = :username";
        $query = $conn->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();
        
        if ($query->rowCount() > 0) {
            $errors[] = "Skip existing user: $username ($name)";
            continue;
        }

        $conn->beginTransaction();

        $date_time = date("Y-m-d H:i:s");
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // 1. Insert into user
        $sql = "INSERT INTO user(username, password_hash, role, status, created_at, updated_at) 
                VALUES (:username, :password_hash, 1, 10, :created_at, :updated_at)";
        $query = $conn->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
        $query->bindParam(':created_at', $date_time, PDO::PARAM_STR);
        $query->bindParam(':updated_at', $date_time, PDO::PARAM_STR);
        $query->execute();

        $user_id = $conn->lastInsertId();

        // 2. Insert into profile
        // st = sequence number, using $count+1 or something? 
        // For now, I'll use 0 or let it be.
        $st = 0; 
        
        $sql = "INSERT INTO profile(id, user_id, fname, name, sname, dep, workgroup, phone, st, status, created_at, updated_at) 
                VALUES (:id, :user_id, :fname, :name, :sname, :dep, :workgroup, :phone, :st, 10, :created_at, :updated_at)";
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $user_id, PDO::PARAM_INT);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->bindParam(':fname', $fname, PDO::PARAM_STR);
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':sname', $sname, PDO::PARAM_STR);
        $query->bindParam(':dep', $dep, PDO::PARAM_STR);
        $query->bindParam(':workgroup', $workgroup, PDO::PARAM_STR);
        $query->bindParam(':phone', $phone, PDO::PARAM_STR);
        $query->bindParam(':st', $st, PDO::PARAM_INT);
        $query->bindParam(':created_at', $date_time, PDO::PARAM_STR);
        $query->bindParam(':updated_at', $date_time, PDO::PARAM_STR);
        $query->execute();

        $conn->commit();
        $count++;

    } catch (Exception $e) {
        $conn->rollback();
        $errors[] = "Error for $username ($name): " . $e->getMessage();
    }
}

fclose($handle);

echo json_encode([
    'status' => true,
    'imported_count' => $count,
    'errors' => $errors
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
