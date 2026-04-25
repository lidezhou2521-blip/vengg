<?php 

require_once('../../server/authen.php');
if(isset($_POST['profile'])){
    try {
        $params = array(
            'firstname' => cleanData($_POST['firstname']),
            'lastname' => cleanData($_POST['lastname']),
            'updated_at' => date("Y-m-d h:i:s"),
            'u_id' => intval(cleanData($_SESSION['AD_ID'])),
        );
        $sql = "UPDATE users SET 
                        firstname = :firstname,
                        lastname = :lastname, 
                        updated_at = :updated_at
                        WHERE u_id = :u_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        if($stmt->rowCount()){
            $_SESSION['AD_FIRSTNAME'] = $params['firstname'];
            $_SESSION['AD_LASTNAME'] = $params['lastname'];

            echo "<script> alert('แก้ไขสำเร็จ')</script>";
            header("Refresh:0; url=../../pages/account/profile.php");
        } else {
            echo "<script> alert('ไม่สามารถแก้ไขข้อมูลได้')</script>";
            header("Refresh:0; url=../../pages/account/profile.php");
        }

    } catch (Throwable $e) {
        echo "<script> alert('การประมวลผลข้อมูลล้มเหลว')</script>";
        header("Refresh:0; url=../../pages/account/profile.php");
    }
} else {
    header('Location: ../../pages/account/profile.php');
}
?>