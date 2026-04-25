<?php    
    session_start(); // ประกาศ การใช้งาน session
    session_destroy(); // ลบตัวแปร session ทั้งหมด
    header('Location: ./dashboard');
?>