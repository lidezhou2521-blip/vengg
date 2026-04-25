<?php 

require_once('../../server/authen.php');
if (isset($_POST['upload-image'])) {
    /** ตั้งชื่อรูป ค่าเริ่มต้น */
    $u_image = $_POST['image'];
    /** ตรวจสอบว่ามีการอัปโหลดไฟล์ภาพมาหรือไม่ */
    if (isset($_FILES['file']['tmp_name'])) {
        /** เข้าถึงนามสกุลไฟล์ของรูปภาพ */
        $extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        /** นามสกุลรูปภาพที่อนุญาตให้ใช้งานได้ */
        $supported = array('jpg', 'jpeg', 'png', 'gif');
        /** เช็คนามสกุลรูปภาพว่าตรงตามที่กำหนดไว้หรือไม่ */
        if( in_array($extension, $supported) && isMimeValid($_FILES['file']['tmp_name']) ){
            /** สร้างชื่อรูปภาพขึ้นมาใหม่ */
            $u_image = md5(microtime()).'.'.$extension;
            /** สร้างเส้นทางเพื่อเก็บไฟล์รูปภาพ */
            $pathUpload = '../../assets/images/uploads/' . $u_image;
            /** ทำการย้ายรูปภาพเข้าสู่โฟลเดอร์ */
            if(!move_uploaded_file($_FILES['file']['tmp_name'], $pathUpload)){
                /** เรียกใช้งาน Method error สำหรับ Response ข้อมูลกลับไป */ 
                echo '<script>ไม่สามารถอัพโหลดรูปภาพได้</script>';
            }
        }
    }

    try {
        $sql = "UPDATE users SET 
                        image = ?,
                        updated_at = ?
                        WHERE u_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$u_image, date("Y-m-d"), $_SESSION['AD_ID']]);
        
        if($stmt->rowCount()){
            $_SESSION['AD_IMAGE'] = $u_image;
            echo "<script> alert('แก้ไขรูปภาพสำเร็จ')</script>";
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
