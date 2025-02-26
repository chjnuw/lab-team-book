<?php
session_start();
include('db.php'); // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้และรูปภาพโปรไฟล์จาก user_images
$sql = "SELECT u.username, u.email, u.first_name, u.last_name, ui.image_path 
        FROM users u 
        LEFT JOIN user_images ui ON u.user_id = ui.user_id 
        WHERE u.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $first_name, $last_name, $profile_image);
$stmt->fetch();
$stmt->close();

// อัปเดตข้อมูลเมื่อมีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $password = $_POST['password'];
    $profile_image = $profile_image; // เก็บค่ารูปภาพปัจจุบัน

    // ตรวจสอบว่ามีการอัปโหลดรูปภาพใหม่หรือไม่
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "userimg/";
        $profile_image_name = basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $profile_image_name;

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = $profile_image_name;

            // ตรวจสอบว่าผู้ใช้มีรูปภาพเดิมหรือไม่
            $sql_check = "SELECT COUNT(*) FROM user_images WHERE user_id = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("i", $user_id);
            $stmt_check->execute();
            $stmt_check->bind_result($image_count);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($image_count > 0) {
                // ถ้ามีรูปภาพเดิม ให้อัปเดตรูปภาพ
                $sql_update_image = "UPDATE user_images SET image_path = ? WHERE user_id = ?";
                $stmt_update_image = $conn->prepare($sql_update_image);
                $stmt_update_image->bind_param("si", $profile_image, $user_id);
                $stmt_update_image->execute();
                $stmt_update_image->close();
            } else {
                // ถ้าไม่มีรูปภาพเดิม ให้เพิ่มข้อมูลรูปภาพใหม่
                $sql_insert_image = "INSERT INTO user_images (user_id, image_path) VALUES (?, ?)";
                $stmt_insert_image = $conn->prepare($sql_insert_image);
                $stmt_insert_image->bind_param("is", $user_id, $profile_image);
                $stmt_insert_image->execute();
                $stmt_insert_image->close();
            }
        }
    }

    // แปลงรหัสผ่านเป็นแฮชถ้าผู้ใช้กรอก
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET first_name = ?, last_name = ?, password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $first_name, $last_name, $hashed_password, $user_id);
    } else {
        $sql = "UPDATE users SET first_name = ?, last_name = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $first_name, $last_name, $user_id);
    }

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Profile updated successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error updating profile: " . $stmt->error . "</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-bottom: 20px;
            color: #d4a373;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }
        input[type="file"] {
            margin-bottom: 10px;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #d4a373;
        }
        button {
            padding: 10px 20px;
            background-color: #d4a373;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #b6895e;
        }
        .profile-image-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }
        .back-button {
            margin-top: 10px;
            display: inline-block;
            background-color: #f4f4f4;
            padding: 10px 20px;
            color: #333;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Edit Profile</h1>

    <!-- ส่วนของการแสดงรูปโปรไฟล์ -->
    <div class="profile-image-container">
        <img class="profile-image" src="userimg/<?php echo $profile_image; ?>" alt="Profile Image">
    </div>

    <form method="POST" enctype="multipart/form-data">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?php echo $username; ?>" readonly>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>" readonly>

        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" required>

        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" required>

        <!-- เพิ่มฟิลด์อัปโหลดรูปภาพ -->
        <label for="profile_image">Profile Image</label>
        <input type="file" id="profile_image" name="profile_image">

        <label for="password">Password (รหัสผ่านใหม่)</label>
        <input type="password" id="password" name="password" placeholder="New Password">

        <button type="submit">Update Profile</button>
    </form>

    <a href="account.php" class="back-button">Back</a>
</div>

</body>
</html>
