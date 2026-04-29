<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents("php://input"));
$file = '../../line_config.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $channel_access_token = isset($data->channel_access_token) ? trim($data->channel_access_token) : '';
    
    $config = array('channel_access_token' => $channel_access_token);
    
    if(file_put_contents($file, json_encode($config, JSON_PRETTY_PRINT))){
        echo json_encode(array('status' => true, 'message' => 'บันทึกการตั้งค่า LINE สำเร็จ'));
    } else {
        echo json_encode(array('status' => false, 'message' => 'ไม่สามารถเขียนไฟล์การตั้งค่าได้'));
    }
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $channel_access_token = '';
    
    if(file_exists($file)){
        $config = json_decode(file_get_contents($file), true);
        if(isset($config['channel_access_token'])){
            $channel_access_token = $config['channel_access_token'];
        }
    }
    echo json_encode(array('status' => true, 'channel_access_token' => $channel_access_token));
    exit;
}
