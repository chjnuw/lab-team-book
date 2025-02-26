<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include('db.php'); // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// SQL query เพื่อดึงหนังสือแนะนำจากแต่ละหมวดหมู่
$sql_recommended = "
    SELECT b.book_id, b.title, b.image, c.category_name 
    FROM books b
    INNER JOIN categories c ON b.category_id = c.category_id
    LIMIT 5"; // จำกัดจำนวนไว้ 5 เล่ม
$result_recommended = $conn->query($sql_recommended);

// SQL query เพื่อดึงข้อมูลหมวดหมู่ทั้งหมด
$sql_categories = "SELECT category_id, category_name FROM categories";
$result_categories = $conn->query($sql_categories);

if (!$result_recommended || !$result_categories) {
    die("Error in SQL query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <title>AllBooks</title>
    <style>
        /* การจัดการรูปภาพใน Header */
        .header-banner {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f4f4;
            overflow: hidden;
        }

        .banner-container {
            display: flex;
            width: 200%; /* ทำให้มีพื้นที่ในการเลื่อน */
            animation: scroll-banner 50s linear infinite; /* การเลื่อนแบบวนลูป */
        }

        .banner {
            width: 15%; /* ขนาดเล็กลง */
            height: auto;
            object-fit: cover;
            margin: 0 5px; /* เพิ่มช่องว่างระหว่างรูป */
            transition: transform 0.3s ease; /* เพิ่มการเคลื่อนไหวเมื่อชี้ */
            cursor: pointer;
        }

        /* เพิ่มเอฟเฟกต์การขยายเมื่อชี้ */
        .banner:hover {
            transform: scale(1.3); /* ขยายขนาดเล็กน้อยเมื่อชี้ */
        }

        /* Animation for looping the banner */
        @keyframes scroll-banner {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%); /* เลื่อนครึ่งหนึ่งของ container */
            }
        }

        /* ส่วนของหนังสือแนะนำ */
 /* การจัดการส่วนที่เหลือของหน้า */
 body {
    font-family: 'Playfair Display', serif;
            margin: 0;
            padding-top: 70px; /* เพิ่ม padding-top ให้พอดีกับ header ที่ fixed */
            background-color: #f4f4f4;
        }

        .header {
            background-color: #fff;
            padding: 15px 0;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            position: fixed; /* ทำให้ header คงอยู่ด้านบน */
            top: 0;
            width: 100%; /* กำหนดให้มีความกว้างเต็มหน้าจอ */
            z-index: 1000; /* ทำให้ header อยู่เหนือเนื้อหาอื่น */
        }

        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header a {
            text-decoration: none;
            color: #333;
            font-size: 24px; /* ขยายขนาดตัวอักษร */
            font-weight: bold; /* ทำให้ตัวหนา */
            margin-left: 20px;
        }

        .main-content {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .products-container {
            margin-top: 0;
            width: 100%; /* ความกว้างเต็มหน้าจอ */
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            overflow: hidden;
            position: relative;
        }

        /* CSS สำหรับ Recommended Books ที่เลื่อนเองแบบต่อเนื่องและมีช่องว่าง */
        .recommended-products {
            display: flex;
            gap: 20px; /* ใส่ช่องว่างระหว่างหนังสือ */
            animation: scroll 45s linear infinite; /* ทำให้เลื่อนแบบวนลูป */
        }

        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }

        .product-item {
            background-color: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            width: 180px;
            text-align: center;
            display: inline-block;
            box-sizing: border-box;
            height: 400px;
            transition: transform 0.3s ease; /* เพิ่มการเคลื่อนไหว */
        }

        /* เพิ่มเอฟเฟกต์การขยายเมื่อชี้ */
        .product-item:hover {
            transform: scale(1.1); /* ขยายขนาดเล็กน้อย */
            z-index: 1; /* ให้อยู่ด้านหน้า */
        }

        .product-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .product-item h3 {
            font-size: 16px;
            margin-bottom: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-item button {
            padding: 10px 20px;
            background-color: #d4a373;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .product-item button:hover {
            background-color: #b6895e;
        }

        .category-header {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
            margin-top: 30px;
            clear: both;
        }

        .products {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: flex-start;
        }

        @media (max-width: 768px) {
            .product-item {
                width: calc(50% - 20px);
            }
        }

        @media (max-width: 480px) {
            .product-item {
                width: 100%;
            }
        }

        /* Fullscreen Modal CSS */
 
         /* Fullscreen Modal CSS */
        .modal {
            display: none;
            position: fixed;
            z-index: 1001; /* กำหนดให้สูงกว่า header */
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        /* ปุ่มปิดยังคงอยู่ที่มุมขวาบนของหน้าจอ */
        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1002; /* ทำให้ปุ่มปิดอยู่ด้านบนสุด */
        }


    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="container">
        <div>
        </div>
        <div>
            <a href="index.php"><b>Back</b></a>
        </div>
    </div>
</div>


<div id="myModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<!-- Script to handle modal -->
<script>
function showModal(src) {
    document.getElementById("modalImage").src = src;
    document.getElementById("myModal").style.display = "block";
}

function closeModal() {
    document.getElementById("myModal").style.display = "none";
}
</script>

<!-- Recommended Books Section -->
<div class="main-content">
    <div class="products-container">
        <h3>หนังสือแนะนำ</h3>
        <div class="recommended-products">
            <?php
            // แสดงหนังสือแนะนำแบบเลื่อนอัตโนมัติ
            for ($i = 0; $i < 20; $i++) { // คัดลอกซ้ำเพื่อให้เลื่อน
                $result_recommended->data_seek(0);
                while ($row = $result_recommended->fetch_assoc()) {
                    echo "<div class='product-item'>";
                    echo "<img src='uploads/{$row['image']}' alt='{$row['title']}'>";
                    echo "<h3>{$row['title']}</h3>";
                    echo "<a href='book_detail.php?book_id={$row['book_id']}'><button>รายละเอียด</button></a>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>

    <!-- Loop through categories and show books in each category -->
    <?php
    if ($result_categories->num_rows > 0) {
        // ลูปหมวดหมู่เพื่อแสดงหนังสือทั้งหมดในแต่ละหมวดหมู่
        while ($category = $result_categories->fetch_assoc()) {
            $category_id = $category['category_id'];
            $sql_books = "SELECT book_id, title, image FROM books WHERE category_id = $category_id";
            $result_books = $conn->query($sql_books);

            if ($result_books->num_rows > 0) {
                echo "<div class='products-container'>";
                echo "<h3 class='category-header'>{$category['category_name']}</h3>";
                echo "<div class='products'>";
                
                // แสดงหนังสือในแต่ละหมวดหมู่
                while ($book = $result_books->fetch_assoc()) {
                    echo "<div class='product-item'>";
                    echo "<img src='uploads/{$book['image']}' alt='{$book['title']}'>";
                    echo "<h3>{$book['title']}</h3>";
                    echo "<a href='book_detail.php?book_id={$book['book_id']}'><button>รายละเอียด</button></a>";
                    echo "</div>";
                }

                echo "</div>";
                echo "</div>";
            }
        }
    }
    ?>
</div>

</body>
</html>