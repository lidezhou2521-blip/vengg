<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

require_once('../connect.php');
require_once('../connect_gdms.php');
require_once('../function.php');


// if (isset($_POST['authen'])) {
$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $username = cleanData($_POST['username']);
    // $password = cleanData($_POST['password']);
    $username = cleanData($data->username);
    $password = cleanData($data->password);

    // $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND status = 'true' ");
    $stmt = $conn_gdms->prepare("SELECT u.* 
                            FROM users as u     
                            WHERE u.username = :username");
    $stmt->execute(array(":username" => $username));
    $row = $stmt->fetch(PDO::FETCH_OBJ);
    // $row = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!empty($row) && password_verify($password, $row->password)) {
        unset($row->password);
        $role = '';
        if ($username == 'admin') {
            $role = 9;
        }
        empty($row->img) ?  $u_image = 'avatar.png' : $u_image = $row->avatar;
        $_SESSION['AD_ID'] = $row->id;
        $_SESSION['AD_FIRSTNAME'] = $row->name;
        $_SESSION['AD_LASTNAME'] = '';
        $_SESSION['AD_USERNAME'] = $row->username;
        $_SESSION['AD_IMAGE'] = $u_image;
        $_SESSION['AD_ROLE'] = $role;
        $_SESSION['AD_STATUS'] = $row->status;
        $_SESSION['LOGIN_BY'] = 'gdms';

        // header('Location: ../../pages/index.php');  

        http_response_code(200);
        $response = array('status' => true, 'message' => 'success', 'ss_uid' => $_SESSION['AD_ID']);
        echo json_encode($response);
        exit;
    } else {
        http_response_code(200);
        $response = array('status' => false, 'message' => 'ไม่สามารถเข้าระบบได้');
        echo json_encode($response);
        exit;
    }
}
