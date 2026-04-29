<?php
include_once "dbconfig.php";
$sql = "SELECT * FROM ven LIMIT 1";
$query = $conn->prepare($sql);
$query->execute();
$row = $query->fetch(PDO::FETCH_ASSOC);
echo json_encode(array_keys($row));
