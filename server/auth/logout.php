<?php 
session_start(); // เริ่มต้นการใช้งาน session
session_destroy(); // ลบตัวแปร session ทั้งหมด
header('Location: ../../pages/dashboard'); // ส่งผู้ใช้ไปยังหน้าแดชบอร์ดหลังจากออกจากระบบ
exit;
?>
