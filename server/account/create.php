<?php 

require_once('../../server/authen.php'); 
if(isset($_POST['submit']) && $_SESSION['AD_ROLE'] === 'superadmin'){
    /** ตั้งชื่อรูป ค่าเริ่มต้น */
    $u_image = 'avatar.png';
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
                header("Refresh:0; url=../../pages/account/");
            }
        }
    }

    try {
        $params = array(
            'firstname' => cleanData($_POST['firstname']),
            'lastname' => cleanData($_POST['lastname']),
            'username' => cleanData($_POST['username']),
            'password' => password_hash(cleanData($_POST['password']), PASSWORD_DEFAULT) ,
            'image' => cleanData($u_image),
            'role' => cleanData($_POST['role']),
            'status' => 'true',
            'created_at' => date("Y-m-d h:i:s"),
            'updated_at' => date("Y-m-d h:i:s"),
        );
        $sql = "INSERT INTO users (firstname, lastname, username, password, image, role, status, created_at, updated_at)
                VALUES (:firstname, :lastname, :username, :password, :image, :role, :status, :created_at, :updated_at)";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        if($conn->lastInsertId()){
            echo "<script> alert('เพิ่มข้อมูลสำเร็จ')</script>";
            header("Refresh:0; url=../../pages/account/");
        } else {
            echo "<script> alert('ไม่สามารถเพิ่มข้อมูลได้')</script>";
            header("Refresh:0; url=../../pages/account/");
        }

    } catch (Throwable $e) {
        echo "<script> alert('การประมวลผลข้อมูลล้มเหลว')</script>";
        header("Refresh:0; url=../../pages/account/");
    }
}

?>