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


// ตรวจสอบว่ามีการส่ง book_id หรือไม่
if (!isset($_POST['book_id'])) {
    header('Location: book_list.php');
    exit;
}

$book_id = $_POST['book_id'];

// ดึงข้อมูลหนังสือที่ต้องการแก้ไขจากฐานข้อมูล
$sql = "SELECT title, author, description, category_id, image FROM books WHERE book_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$stmt->bind_result($title, $author, $description, $category_id, $current_image);
$stmt->fetch();
$stmt->close();

// อัปเดตข้อมูลหนังสือเมื่อมีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $new_title = $_POST['title'];
    $new_author = $_POST['author'];
    $new_description = $_POST['description'];
    $new_category_id = $_POST['category_id'];
    $new_image = $current_image; // ใช้รูปเดิมหากไม่มีการอัปโหลดใหม่

    // ตรวจสอบว่ามีการอัปโหลดไฟล์รูปภาพใหม่หรือไม่
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/"; // โฟลเดอร์ที่เก็บรูปภาพ
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // ตรวจสอบประเภทไฟล์
        $valid_extensions = array("jpg", "jpeg", "png", "gif");
        if (in_array($imageFileType, $valid_extensions)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $new_image = basename($_FILES["image"]["name"]); // เก็บชื่อไฟล์รูปภาพใหม่
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    $sql_update = "UPDATE books SET title = ?, author = ?, description = ?, category_id = ?, image = ? WHERE book_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssisi", $new_title, $new_author, $new_description, $new_category_id, $new_image, $book_id);
    
    if ($stmt_update->execute()) {
        echo "Book updated successfully!";
    } else {
        echo "Error updating book: " . $stmt_update->error;
    }

    $stmt_update->close();
}

// ดึงข้อมูลหมวดหมู่ทั้งหมดจากฐานข้อมูลเพื่อให้เลือกในการแก้ไข
$sql_categories = "SELECT category_id, category_name FROM categories";
$result_categories = $conn->query($sql_categories);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .edit-book-form {
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
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="file"] {
            margin-bottom: 10px;
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
        img {
            max-width: 150px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="edit-book-form">
    <h2>Edit Book</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?php echo $title; ?>" required>

        <label for="author">Author</label>
        <input type="text" id="author" name="author" value="<?php echo $author; ?>" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="5" required><?php echo $description; ?></textarea>

        <label for="category_id">Category</label>
        <select id="category_id" name="category_id">
            <?php while ($category = $result_categories->fetch_assoc()): ?>
                <option value="<?php echo $category['category_id']; ?>" <?php if ($category_id == $category['category_id']) echo 'selected'; ?>>
                    <?php echo $category['category_name']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Current Image</label><br>
        <img src="uploads/<?php echo $current_image; ?>" alt="Current Book Image"><br>

        <label for="image">Upload New Image (Optional)</label>
        <input type="file" id="image" name="image" accept="image/*">

        <button type="submit" name="update">Update Book</button>
    </form>
    <br>
    <a href="book_list.php"><button type="button">Back to Book List</button></a>
</div>

</body>
</html>
