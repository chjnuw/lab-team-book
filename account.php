<?php
session_start();
include('db.php'); // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้
$sql_user = "SELECT username, email, first_name, last_name FROM users WHERE user_id = ?";
$stmt_user = $conn->prepare($sql_user);
if ($stmt_user === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$stmt_user->bind_result($username, $email, $first_name, $last_name);
$stmt_user->fetch();
$stmt_user->close();

// ดึงข้อมูลรูปโปรไฟล์จากตาราง user_images
$sql_image = "SELECT image_path FROM user_images WHERE user_id = ?";
$stmt_image = $conn->prepare($sql_image);
if ($stmt_image === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt_image->bind_param("i", $user_id);
$stmt_image->execute();
$stmt_image->bind_result($profile_image);
$stmt_image->fetch();
$stmt_image->close();

// กำหนดรูปโปรไฟล์ดีฟอลต์ ถ้าไม่มีรูปที่ดึงจากฐานข้อมูล
if (!$profile_image) {
    $profile_image = 'userimg/default_profile.png'; // รูปโปรไฟล์เริ่มต้น
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
            background-image: url('bg-image.jpg'); /* เปลี่ยนเป็นรูปพื้นหลังที่คุณต้องการ */
            background-size: cover;
            background-position: center;
        }

        .account-container {
        display: flex;
        max-width: 1000px;
        margin: 50px auto;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 15px; /* ทำให้โค้งมนทุกด้าน */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        backdrop-filter: blur(10px);
    }
    .sidebar {
        width: 250px;
        padding-right: 20px;
        text-align: center;
        background-color: rgba(247, 247, 247, 0.9);
        border-radius: 15px 0 0 15px; /* โค้งมนเฉพาะด้านซ้าย */
        position: relative;
    }

        .sidebar img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 15px;
            object-fit: cover;
            background-color: white;
            padding: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 2px solid #d4a373;
        }

        .sidebar h2 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #333;
            font-family: 'Playfair Display', serif;
        }

        .sidebar p {
            margin: 5px 0;
            color: #666;
            font-style: italic;
        }

        .sidebar a {
        display: block;
        margin: 10px 0;
        padding: 12px;
        background-color: #f4f4f4;
        color: #333;
        text-decoration: none;
        border-radius: 5px;
        transition: transform 0.3s ease, background-color 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .sidebar a:hover {
        background-color: #d4a373;
        transform: scale(1.05);
        color: white;
    }

    .content {
        flex: 1;
        padding-left: 40px;
        font-family: 'Roboto', sans-serif;
        border-radius: 0 15px 15px 0; /* โค้งมนเฉพาะด้านขวา */
    }

        .content h3 {
            font-size: 28px;
            color: #d4a373;
            margin-bottom: 20px;
        }

        .content .info-field {
            width: 100%;
            padding: 12px 0;
            margin: 10px 0;
            border-bottom: 1px solid #ddd;
            font-size: 18px;
            color: #333;
        }

    </style>
</head>
<body>

<div class="account-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="userimg/<?php echo $profile_image; ?>" alt="Profile Image">
        <h2><?php echo $username; ?></h2>
        <p><?php echo $first_name . " " . $last_name; ?></p>
        <a href="account.php" class="active">Profile</a>
        <a href="favorite_books.php">Favorite Books</a>
        <a href="edit_profile.php">Edit Profile</a>
        <a href="index.php">Back</a>
    </div>

    <!-- Content -->
    <div class="content">
        <h3>ข้อมูลส่วนตัว</h3>
        <label>Email:</label>
        <div class="info-field"><?php echo $email; ?></div>
        
        <label>ชื่อ:</label>
        <div class="info-field"><?php echo $first_name; ?></div>
        
        <label>นามสกุล:</label>
        <div class="info-field"><?php echo $last_name; ?></div>
    </div>
</div>

</body>
</html>
