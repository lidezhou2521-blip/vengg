<?php
require 'connect.php';
$stmt = $conn->query('SHOW TABLES');
echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
