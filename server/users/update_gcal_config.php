<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents("php://input"));
$file = '../gcal_config.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $state = isset($data->state) && $data->state ? true : false;
    $api_url = isset($data->api_url) ? trim($data->api_url) : 'http://127.0.0.1/service/google/calendar/calendar.php';
    
    $config = array(
        'gcal_enabled' => $state,
        'api_url' => $api_url
    );
    
    if(file_put_contents($file, json_encode($config, JSON_PRETTY_PRINT))){
        echo json_encode(array('status' => true, 'message' => 'บันทึกการตั้งค่าสำเร็จ'));
    } else {
        echo json_encode(array('status' => false, 'message' => 'ไม่สามารถเขียนไฟล์การตั้งค่าได้'));
    }
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $state = false;
    $api_url = 'http://127.0.0.1/service/google/calendar/calendar.php';
    
    if(file_exists($file)){
        $config = json_decode(file_get_contents($file), true);
        if(isset($config['gcal_enabled'])){
            $state = $config['gcal_enabled'];
        }
        if(isset($config['api_url'])){
            $api_url = $config['api_url'];
        }
    }
    echo json_encode(array('status' => true, 'state' => $state, 'api_url' => $api_url));
    exit;
}
