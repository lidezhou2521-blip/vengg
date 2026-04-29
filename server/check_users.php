<?php
require 'connect.php';
echo "USER TABLE:\n";
$stmt = $conn->query('SELECT * FROM user');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) . "\n\n";

echo "PROFILE TABLE:\n";
$stmt = $conn->query('SELECT * FROM profile');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) . "\n\n";
