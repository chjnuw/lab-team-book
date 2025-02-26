<?php
session_start();
include('db.php');

// ตรวจสอบว่ามีการล็อกอินหรือไม่ และ role ของผู้ใช้เป็น admin หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // ถ้าไม่ใช่ admin หรือยังไม่ได้ล็อกอิน ให้เปลี่ยนเส้นทางไปยังหน้าอื่น
    header('Location: index.php'); // เปลี่ยนเป็นหน้าที่ต้องการให้เปลี่ยนเส้นทาง
    exit(); // หยุดการประมวลผลหลังจากเปลี่ยนเส้นทาง
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Playfair Display', serif;
            background-image: url('img/teacup-7526022_1280.webp'); /* แทนที่ด้วยพาธรูปภาพพื้นหลังของคุณ */
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
            color: white;
        }

        .admin-container {
            max-width: 900px;
            margin: 100px auto;
            padding: 20px;
            text-align: center;
        }

        h2 {
            margin-bottom: 10px;
            font-size: 36px;
        }

        .panel-subtitle {
            color: #00aaff;
            margin-bottom: 40px;
            font-size: 18px;
        }

        /* การจัดการปุ่มให้อยู่ในแถวเดียวกัน */
        .button-group {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }

        .button-group div {
            width: 150px;
            height: 150px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .button-group div:hover {
            background-color: rgba(0, 0, 0, 0.9);
            transform: scale(1.1);
        }

        .button-group div i {
            font-size: 40px;
            margin-bottom: 10px;
        }

        /* ปุ่มออกจากระบบ */
        .logout {
            margin-top: 20px;
            padding: 15px 30px;
            background-color: #d4a373;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .logout:hover {
            background-color: #b6895e;
        }
    </style>
</head>
<body>

<div class="admin-container">
    <h2>Admin</h2>
    <div class="panel-subtitle">Panel</div>

    <!-- ปุ่มหลักในแถวเดียวกัน -->
    <div class="button-group">
        <div onclick="window.location.href='book_list.php'">
            <i class="fas fa-cubes"></i>
            List Books
        </div>
        <div onclick="window.location.href='add_book.php'">
            <i class="fas fa-laptop"></i>
            Add Book
        </div>
        <div onclick="window.location.href='manage_users.php'">
            <i class="fas fa-users"></i>
            User
        </div>
        <div onclick="window.location.href='index.php'">
            <i class="fas fa-envelope"></i>
            Home
        </div>
        <div onclick="window.location.href='register.php'">
            <i class="fas fa-user-plus"></i>
            Register
        </div>
    </div>

    <!-- ปุ่มออกจากระบบ -->
    <button class="logout" onclick="window.location.href='logout.php'">Logout</button>
</div>

</body>
</html>
