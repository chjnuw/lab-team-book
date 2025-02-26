<?php
session_start(); // เริ่มต้น session
session_unset(); // ลบข้อมูลใน session
session_destroy(); // ทำลาย session

// เปลี่ยนเส้นทางไปที่หน้า login.php
header("Location: login.php");
exit;
?>
