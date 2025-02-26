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

// ตรวจสอบว่ามีการส่งฟอร์มเข้ามาหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $category_id = $_POST['category'];
    
    // ตรวจสอบว่ามีการอัปโหลดรูปภาพ
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        // อัปโหลดไฟล์รูปภาพไปยังโฟลเดอร์ uploads
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
    } else {
        $image = 'default.jpg'; // หากไม่มีการอัปโหลดรูปภาพ จะใช้รูปค่าเริ่มต้น
    }

    // เพิ่มหนังสือลงในฐานข้อมูล
    $sql = "INSERT INTO books (title, author, description, image, category_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $title, $author, $description, $image, $category_id);

    if ($stmt->execute()) {
        echo "Book added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// ดึงหมวดหมู่จากฐานข้อมูลเพื่อแสดงในฟอร์ม
$category_sql = "SELECT * FROM categories";
$category_result = $conn->query($category_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .form-container {
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
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
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
</head>
<body>

<div class="form-container">
    <h2>Add New Book</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" placeholder="Enter book title" required>

        <label for="author">Author:</label>
        <input type="text" id="author" name="author" placeholder="Enter author name" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" placeholder="Enter book description" required></textarea>

        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <option value="">Select Category</option>
            <?php
            // แสดงหมวดหมู่หนังสือในฟอร์ม
            while ($category = $category_result->fetch_assoc()) {
                echo "<option value='{$category['category_id']}'>{$category['category_name']}</option>";
            }
            ?>
        </select>

        <label for="image">Book Image:</label>
        <input type="file" id="image" name="image">

        <button type="submit">Add Book</button>
    </form>

    <!-- ปุ่ม back -->
    <a href="admin.php" class="back-button">Back</a>
</div>

</body>
</html>
