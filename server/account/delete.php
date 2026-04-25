<?php 

require_once('../../server/authen.php');
if(isset($_GET['u_id']) && $_SESSION['AD_ROLE'] === 'superadmin'){
    try {
        $sql = "DELETE FROM users WHERE u_id = ? AND u_id NOT IN ( 1 )";
        $stmt= $conn->prepare($sql);
        $stmt->execute([cleanData($_GET['u_id'])]);

        if($stmt->rowCount()){
            echo "<script> alert('ลบข้อมูลสำเร็จ')</script>";
            header("Refresh:0; url=../../pages/account/");
        } else {
            echo "<script> alert('ไม่สามารถลบข้อมูลได้')</script>";
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