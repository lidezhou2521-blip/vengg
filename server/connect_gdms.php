<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "edocument";

/** เชื่อมต่อฐานข้อมูลด้วย PHP PDO */
try {
    $conn_gdms = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn_gdms->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn_gdms->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    http_response_code(200);
    $response = array('status'=>false,'message' => 'การเชื่อมต่อฐานข้อมูล GDMS ล้มเหลว:'  . $e->getMessage());
    echo json_encode($response);
    die();
}


