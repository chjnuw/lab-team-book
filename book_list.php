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


// ลบหนังสือเมื่อมีการส่งคำขอ POST สำหรับการลบ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $book_id = $_POST['book_id'];
    $sql = "DELETE FROM books WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    if ($stmt->execute()) {
        echo "Book deleted successfully!";
    } else {
        echo "Error deleting book: " . $stmt->error;
    }
}

// ดึงหนังสือทั้งหมดจากฐานข้อมูล
$sql = "SELECT book_id, title FROM books";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .book-list {
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
        .book-item {
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
            margin-right: 10px;
        }
        .edit-button:hover {
            background-color: #45a049;
        }
        .button-group {
            display: flex;
        }
        .back-button {
            display: block;
            width: 100px;
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
            return confirm("Are you sure you want to delete this book?");
        }
    </script>
</head>
<body>

<div class="book-list">
    <h2>Book List</h2>
    <?php while ($book = $result->fetch_assoc()): ?>
        <div class="book-item">
            <span><?php echo $book['title']; ?></span>
            <div class="button-group">
                <form method="POST" action="edit_book.php">
                    <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                    <button type="submit" class="edit-button">Edit</button>
                </form>
                <form method="POST" onsubmit="return confirmDelete();">
                    <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                    <button type="submit" name="delete">Delete</button>
                </form>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- ปุ่ม back -->
<a href="admin.php" class="back-button">Back</a>

</body>
</html>
