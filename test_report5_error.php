<?php
$url = 'http://localhost/vengg/server/asu/report/report5.php';
$data = array('vcid' => 1776932612); 
$options = array(
    'http' => array(
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
    ),
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
echo $result;
