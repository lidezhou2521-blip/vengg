<?php 

require_once('../../server/authen.php');
header('Content-Type: application/json');

if(isset($_POST['id']) && $_SESSION['AD_ROLE'] === 'superadmin'){

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE u_id = ?");
    $stmt->execute([cleanData($_POST['value']), cleanData($_POST['id'])]);
    
    if($stmt->rowCount()){
        http_response_code(200);
        $response = array('message' => 'success');
    }else{
        http_response_code(400);
        $response = array('message' => 'error');
    }
    
    echo json_encode($response);
} else {
    header('Location: ../../pages/account/');
}
?>