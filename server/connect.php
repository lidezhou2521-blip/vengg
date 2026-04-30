<?php
session_start();
error_reporting(E_ALL);
// error_reporting(0);

// โหลดตั้งค่า Google Calendar จากไฟล์ JSON (เพื่อให้จัดการง่าย ไม่ต้องแก้โค้ด)
$gcal_config_path = __DIR__ . '/gcal_config.json';
$gcal_enabled = false;
$gcal_api_url = "http://127.0.0.1/service/google/calendar/calendar.php";
if (file_exists($gcal_config_path)) {
    $gcal_config = json_decode(file_get_contents($gcal_config_path), true);
    if (isset($gcal_config['gcal_enabled'])) {
        $gcal_enabled = $gcal_config['gcal_enabled'];
    }
    if (isset($gcal_config['api_url']) && !empty($gcal_config['api_url'])) {
        $gcal_api_url = $gcal_config['api_url'];
    }
}
define("__GOOGLE_CALENDAR__", $gcal_enabled);
define("__GOOGLE_CALENDAR_URL__", $gcal_api_url);
define("__LOGIN_BY__", "");                     // vengg : gdms
define("__VERSION__", "V 1.0");               // version

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