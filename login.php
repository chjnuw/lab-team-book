<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('db.php');
session_start();

$error = ''; // สร้างตัวแปรสำหรับเก็บข้อความข้อผิดพลาด

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT user_id, email, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $db_email, $db_password, $role);
    
    if ($stmt->fetch()) {
        if (password_verify($password, $db_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $db_email;
            $_SESSION['role'] = $role;
            
            if ($role == 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Email not found!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('img/teacup-7526022_1280.webp'); /* ใส่พาธรูปภาพของคุณ */
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .login-container {
            max-width: 600px;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            padding: 40px;
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
            margin-bottom: 25px;
            color: #fff;
            font-weight: 600;
            font-size: 28px;
        }
        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        input {
            width: 90%;
            padding: 15px;
            margin: 5px 0;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 16px;
        }
        input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        input:focus {
            border-color: #d4a373;
            outline: none;
        }
        button {
            width: 90%;
            padding: 15px;
            background-color: #d4a373;;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            margin-top: 15px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #b6895e;
        }
        .link {
            text-align: center;
            margin-top: 20px;
        }
        .link a {
            text-decoration: none;
            color: #007bff; /* สีฟ้า */
        }
        .link a:hover {
            text-decoration: underline;
            color: #0056b3; /* สีฟ้าเข้มเมื่อ hover */
        }
        .error-message {
            background-color: rgba(255, 0, 0, 0.2);
            color: #ff4a4a;
            padding: 10px;
            width: 90%;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 10px;
            border: 1px solid rgba(255, 0, 0, 0.3);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>

    <!-- แสดงข้อความข้อผิดพลาดถ้ามี -->
    <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- ฟอร์มสำหรับ login -->
    <form method="POST" action="login.php">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>

    <div class="link">
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>
</div>

</body>
</html>
