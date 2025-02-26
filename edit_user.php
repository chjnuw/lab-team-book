<?php
session_start();
include('db.php');



// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
//if (!isset($_SESSION['user_id'])) {
    //header('Location: login.php');
    //exit;
//}


// ตรวจสอบว่าผู้ใช้เป็น Admin หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
} 

// ตรวจสอบว่ามีการส่ง user_id หรือไม่
if (!isset($_GET['user_id'])) {
    header('Location: manage_users.php');
    exit;
}

$user_id = $_GET['user_id'];

// ดึงข้อมูลผู้ใช้ที่ต้องการแก้ไขจากฐานข้อมูล
$sql = "SELECT username, email, first_name, last_name, password FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $first_name, $last_name, $password);
$stmt->fetch();
$stmt->close();

// อัปเดตข้อมูลผู้ใช้เมื่อมีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = $_POST['username'];
    $new_first_name = $_POST['first_name'];
    $new_last_name = $_POST['last_name'];
    $new_password = $_POST['password'];

    // อัปเดตข้อมูลในฐานข้อมูล
    $sql_update = "UPDATE users SET username = ?, first_name = ?, last_name = ?, password = ? WHERE user_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssi", $new_username, $new_first_name, $new_last_name, $new_password, $user_id);
    
    if ($stmt_update->execute()) {
        echo "User updated successfully!";
    } else {
        echo "Error updating user: " . $stmt_update->error;
    }

    $stmt_update->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .edit-user-form {
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="file"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            background-color: #ff4a4a;
            color: white;
            padding: 10px 15px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #e53e3e;
        }
    </style>
</head>
<body>

<div class="edit-user-form">
    <h2>Edit User</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>" readonly>

        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" required>

        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" value="<?php echo $password; ?>" required>

        <button type="submit">Update User</button>
    </form>
    <a href="manage_users.php" class="back-button">Back</a>
</div>

</body>
</html>
