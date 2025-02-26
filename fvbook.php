<?php
session_start();
include('db.php'); // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลหนังสือที่ผู้ใช้กดไลค์
$sql = "SELECT b.book_id, b.title, b.image FROM books b
        JOIN book_likes bl ON b.book_id = bl.book_id
        WHERE bl.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// ใช้ bind_result เพื่อดึงข้อมูล
$stmt->bind_result($book_id, $title, $image);

// สร้างอาร์เรย์เพื่อเก็บข้อมูลหนังสือที่ถูกไลค์
$liked_books = [];
while ($stmt->fetch()) {
    $liked_books[] = ['book_id' => $book_id, 'title' => $title, 'image' => $image];
}
$stmt->close();

// การยกเลิกไลค์เมื่อมีการกดปุ่ม
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['unlike'])) {
    $book_id_to_unlike = $_POST['book_id'];
    $sql = "DELETE FROM book_likes WHERE user_id = ? AND book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $book_id_to_unlike);
    $stmt->execute();
    $stmt->close();
    // รีเฟรชหน้าเพื่อแสดงการเปลี่ยนแปลง
    header("Location: favorite_books.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <title>Your Favorite Books</title>
    <style>
        body {
            font-family: 'Playfair Display', serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-bottom: 20px;
            text-align: center;
            color: #d4a373;
        }
        .products {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .product-item {
            background-color: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            width: 200px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .product-item:hover {
            transform: scale(1.05);
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.2);
        }
        .product-item img {
            width: 100%;
            height: auto;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        h3 {
            font-size: 16px; /* ลดขนาดตัวหนังสือลง */
            margin-bottom: 10px;
        }
        .like-button, .detail-button {
            display: inline-block;
            width: 100%;
            padding: 8px; /* ปรับขนาด padding ให้เหมาะสม */
            background-color: #d4a373;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
            transition: background-color 0.3s ease;
        }
        .like-button:hover, .detail-button:hover {
            background-color: #b6895e;
        }
        .back-button-container {
            text-align: center; /* จัดปุ่มให้อยู่ตรงกลาง */
            margin-top: 30px;
        }
        .back-button {
            display: inline-block;
            padding: 10px 30px; /* ปรับขนาดปุ่มให้เหมาะสม */
            background-color: #d4a373;
            color: white;
            border: none;
            border-radius: 25px; /* ปรับปุ่มให้มีความโค้งมน */
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #b6895e;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Your Favorite Books</h1>
    <div class="products">
        <?php if (empty($liked_books)): ?>
            <p>No favorite books found.</p>
        <?php else: ?>
            <?php foreach ($liked_books as $book): ?>
                <div class="product-item">
                    <img src="uploads/<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
                    <h3><?php echo $book['title']; ?></h3>
                    <form method="POST" action="">
                        <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                        <button type="submit" name="unlike" class="like-button">ยกเลิกไลค์</button>
                        <a href="book_detail.php?book_id=<?php echo $book['book_id']; ?>">
                            <button type="button" class="detail-button">ดูรายละเอียด</button>
                        </a>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- ปุ่มย้อนกลับ -->
    <div class="back-button-container">
        <a href="index.php"><button class="back-button">ย้อนกลับ</button></a>
    </div>
</div>

</body>
</html>
