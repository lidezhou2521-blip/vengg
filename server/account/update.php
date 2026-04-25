<?php 

require_once('../../server/authen.php');
if(isset($_POST['submit']) && $_SESSION['AD_ROLE'] === 'superadmin'){
    try {
        $params = array(
            'firstname' => cleanData($_POST['firstname']),
            'lastname' => cleanData($_POST['lastname']),
            'role' => cleanData($_POST['role']),
            'status' => cleanData($_POST['status'] ? 'true': false),
            'updated_at' => date("Y-m-d h:i:s"),
            'u_id' => intval(cleanData($_POST['u_id'])),
        );
        $sql = "UPDATE users SET 
                        firstname = :firstname,
                        lastname = :lastname, 
                        role = :role,
                        status = :status,
                        updated_at = :updated_at
                        WHERE u_id = :u_id AND u_id NOT IN ( 1 )";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        if($stmt->rowCount()){
            echo "<script> alert('แก้ไขสำเร็จ')</script>";
            header("Refresh:0; url=../../pages/account/");
        } else {
            echo "<script> alert('ไม่สามารถแก้ไขข้อมูลได้')</script>";
            header("Refresh:0; url=../../pages/account/");
        }
    } catch (Throwable $e) {
        echo "<script> alert('การประมวลผลข้อมูลล้มเหลว')</script>";
        header("Refresh:0; url=../../pages/account/");
    }
} else {
    header('Location: ../../pages/account/');
}
?>