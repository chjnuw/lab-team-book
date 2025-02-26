
<?php
$servername = "localhost";
$username = "u299560388_651224";
$password = "IG8720Hk";
$dbname = "u299560388_651224"; // ชื่อฐานข้อมูลของคุณ

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
