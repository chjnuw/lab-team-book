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

// ลบผู้ใช้เมื่อมีการส่งคำขอ POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $user_id = $_POST['user_id'];
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo "User deleted successfully!";
    } else {
        echo "Error deleting user: " . $stmt->error;
    }
}

// ดึงข้อมูลผู้ใช้ทั้งหมดจากฐานข้อมูล
$sql = "SELECT user_id, username, email FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .user-list {
            max-width: 800px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .user-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 5px 10px;
            background-color: #ff4a4a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #e53e3e;
        }
        .edit-button {
            background-color: #4CAF50;
        }
        .edit-button:hover {
            background-color: #45a049;
        }
        .back-button {
            display: block;
            width: 150px;
            margin: 20px auto;
            padding: 10px;
            background-color: #007bff;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        // ฟังก์ชันแสดงการยืนยันการลบ
        function confirmDelete() {
            return confirm("Are you sure you want to delete this user?");
        }
    </script>
</head>
<body>

<div class="user-list">
    <h2>Manage Users</h2>
    <?php while ($user = $result->fetch_assoc()): ?>
        <div class="user-item">
            <span><?php echo $user['username'] . " (" . $user['email'] . ")"; ?></span>
            <div>
                <form method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                    <button type="submit" name="delete">Delete</button>
                </form>
                <form method="GET" action="edit_user.php" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                    <button type="submit" class="edit-button">Edit User</button>
                </form>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- ปุ่มย้อนกลับ -->
<a href="admin.php" class="back-button">Back</a>

</body>
</html>
