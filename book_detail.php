<?php
session_start();
include('db.php');

// ตรวจสอบว่ามีการส่ง book_id หรือไม่
if (!isset($_GET['book_id'])) {
    header("Location: home.php");
    exit;
}

$book_id = $_GET['book_id'];
$user_id = $_SESSION['user_id']; // ใช้ user_id ของผู้ใช้ที่ล็อกอิน

// ดึงข้อมูลหนังสือจากฐานข้อมูล
$sql = "SELECT title, author, image, description FROM books WHERE book_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$stmt->bind_result($title, $author, $image, $description);
$stmt->fetch();
$stmt->close();

// ตรวจสอบว่าผู้ใช้เคยกด Like หนังสือเล่มนี้หรือยัง
$sql_like_check = "SELECT COUNT(*) FROM book_likes WHERE user_id = ? AND book_id = ?";
$stmt_like = $conn->prepare($sql_like_check);
$stmt_like->bind_param("ii", $user_id, $book_id);
$stmt_like->execute();
$stmt_like->bind_result($liked);
$stmt_like->fetch();
$stmt_like->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet"> <!-- ลิงก์ฟอนต์ -->
    <title><?php echo $title; ?> - Book Detail</title>
    <style>
        body {
            font-family: 'Playfair Display', serif;
            margin: 0;
            padding: 0;
            background-color: #faf9f6;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            display: flex;
            align-items: center;
        }
        .image-container {
            width: 40%;
            margin-right: 40px;
        }
        .image-container img {
            width: 100%;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .details {
            width: 60%;
            text-align: left;
        }
        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 40px;
            margin-bottom: 10px;
            color: #333;
        }
        .author {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 400;
            margin: 10px 0;
            color: #888;
        }
        .description-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            color: #333;
        }
        .description {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }

        /* ปุ่มใลค์ */
        .like-button {
            padding: 10px 20px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            background-color: #ff4a4a;
            transition: background-color 0.3s ease;
        }

        .like-button:hover {
            background-color: #e53e3e;
        }

        .liked {
            background-color: #007bff;
        }

        /* ปุ่มกลับ */
        .back-button {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            background-color: transparent;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .back-button:hover {
            color: #555;
        }

        .back-button::after {
            content: ' →';
            transition: transform 0.3s ease;
        }

        .back-button:hover::after {
            transform: translateX(5px);
        }
    </style>
    <script>
    function likeBook(button) {
        var bookId = button.getAttribute('data-book-id');

        // ใช้ AJAX เพื่อส่งคำขอไปยัง like.php
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'like.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // เมื่อทำงานเสร็จสิ้น ให้รีเฟรชหน้า
                location.reload(); 
            }
        };
        xhr.send('book_id=' + bookId);
    }
    </script>

</head>
<body>

<div class="container">
    <div class="image-container">
        <img src="uploads/<?php echo $image; ?>" alt="<?php echo $title; ?>">
    </div>
    <div class="details">
        <h1><?php echo $title; ?></h1>
        <p class="author">By <?php echo $author; ?></p>
        <p class="description-title">Synopsis:</p>
        <p class="description"><?php echo $description; ?></p>
        <button class="like-button <?php echo $liked ? 'liked' : ''; ?>" data-book-id="<?php echo $book_id; ?>" onclick="likeBook(this)">
            <?php echo $liked ? 'Liked' : 'Like'; ?>
        </button>
        
        <a href="index.php" class="back-button">Back</a>
    </div>
</div>

</body>
</html>
