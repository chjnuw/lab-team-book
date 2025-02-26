<?php
// Database connection
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ตรวจสอบว่ารหัสผ่านและการยืนยันรหัสผ่านตรงกันหรือไม่
    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Insert user into the database
        $sql_user = "INSERT INTO users (email, first_name, last_name, username, password) VALUES (?, ?, ?, ?, ?)";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("sssss", $email, $first_name, $last_name, $username, $hashed_password);

        if ($stmt_user->execute()) {
            // รับค่า user_id หลังจากเพิ่มผู้ใช้เรียบร้อยแล้ว
            $user_id = $stmt_user->insert_id;

            // ตรวจสอบว่ามีการอัปโหลดไฟล์รูปภาพหรือไม่
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
                // ตั้งชื่อโฟลเดอร์และพาธที่เก็บรูป
                $target_dir = "userimg/";
                $profile_image_name = basename($_FILES["profile_image"]["name"]);
                $target_file = $target_dir . $profile_image_name;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // ตรวจสอบว่าเป็นไฟล์รูปภาพหรือไม่
                $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
                if ($check !== false) {
                    // อัปโหลดไฟล์รูปภาพไปยังเซิร์ฟเวอร์
                    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                        // บันทึกแค่ชื่อไฟล์ลงในตาราง user_images (ไม่ใช่พาธเต็ม)
                        $sql_image = "INSERT INTO user_images (user_id, image_path) VALUES (?, ?)";
                        $stmt_image = $conn->prepare($sql_image);
                        $stmt_image->bind_param("is", $user_id, $profile_image_name); // บันทึกเฉพาะชื่อไฟล์
                        $stmt_image->execute();
                        echo "Registration successful with profile image!";
                        header('Location: login.php'); // เปลี่ยนเส้นทางไปหน้า login
                        exit();
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                    }
                } else {
                    echo "File is not an image.";
                }
            } else {
                echo "Registration successful without profile image!";
                header('Location: login.php');
                exit();
            }
        } else {
            echo "Error: " . $stmt_user->error;
        }

        $stmt_user->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* ปรับใช้รูปภาพพื้นหลัง */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('img/teacup-7526022_1280.webp'); /* ใส่พาธของรูปที่คุณอัปโหลด */
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .register-container {
            max-width: 450px;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px); /* เพิ่มความเบลอ */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            animation: fadeIn 1s ease-in-out;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
            font-weight: 600;
            font-size: 24px;
        }
        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 14px;
        }
        input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        input:focus {
            border-color: #d4a373;
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #d4a373;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 15px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #b6895e;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
        .login-link a {
            text-decoration: none;
            color: #007bff; /* เปลี่ยนสีเป็นฟ้า */
        }
        .login-link a:hover {
            text-decoration: underline;
            color: #0056b3; /* สีฟ้าเข้มเมื่อ hover */
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Register</h2>
    <form action="register.php" method="POST" enctype="multipart/form-data">
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <input type="file" name="profile_image">
        <button type="submit">Register</button>
    </form>
    <div class="login-link">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>

</body>
</html>
