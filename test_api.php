<?php
$data = json_encode([
    'month' => '2026-05',
    'excluded_duties' => [
        [
            'user_id' => 1680162006, // นางสาวรุสณีย์ มะสาแม
            'ven_name' => 'เวรเปิดทำการพิจารณาคำร้องขอปล่อยชั่วคราว',
            'day' => 30 // ven_date: 2026-05-30
        ]
    ]
]);
$ch = curl_init('http://localhost/vengg/pages/fnu/ven/api/index_get_data_all.php');
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
echo json_encode(json_decode($result), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
