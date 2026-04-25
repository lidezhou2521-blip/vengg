<?php
session_start();
error_reporting(E_ALL);
// error_reporting(0);

define("__GOOGLE_CALENDAR__", false);           // true : false
define("__LOGIN_BY__", "");                     // vengg : gdms
define("__VERSION__", "V 2.1.9");               // version

date_default_timezone_set("Asia/Bangkok");

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "vengg";

/** เชื่อมต่อฐานข้อมูลด้วย PHP PDO */
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    http_response_code(200);    
    echo json_encode(array('status'=>false, 'message' => 'การเชื่อมต่อฐานข้อมูล VENGG ล้มเหลว:'  . $e->getMessage()));
    die();
}

//error handler function
function customError($errno, $errstr) {
    http_response_code(200);
    echo json_encode(array('status'=>false,'message' => "Error: [$errno] $errstr"));
    exit();
}

//set error handler
// set_error_handler("customError");