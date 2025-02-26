<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookVerse - Welcome</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('img/book-8643905_1280.webp');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            overflow: hidden; /* ป้องกันการเลื่อนในขณะเอฟเฟกต์ทำงาน */
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6); /* ทำพื้นหลังมืดลง */
            z-index: 0;
        }

        .content {
            position: relative;
            z-index: 1;
            text-align: center;
            opacity: 0; /* ซ่อนก่อน */
            transform: translateY(50px); /* เลื่อนลงมาที่ 50px ก่อน */
            transition: opacity 1s ease-out, transform 1s ease-out; /* กำหนดการเปลี่ยนแปลง */
        }

        .content.show {
            opacity: 1; /* แสดงเมื่อได้รับคลาส "show" */
            transform: translateY(0); /* เลื่อนขึ้นกลับที่ 0 */
        }

        .content h1 {
            font-size: 48px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .content p {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .content .quote {
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 40px;
            max-width: 800px;
            line-height: 1.5;
            color: #fff;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .buttons a {
            padding: 15px 40px;
            border: 2px solid #d4a373;
            color: white;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .buttons a:hover {
            background-color: #d4a373;
            color: white;
        }

        .btn-go {
            background-color: transparent;
            color: white;
            border: 2px solid #ff4a4a;
        }

        .logo {
            position: absolute;
            top: 20px;
            left: 40px;
            font-size: 30px;
            font-weight: 700;
            z-index: 1;
            opacity: 0; /* ซ่อนก่อน */
            transform: translateY(-50px); /* เลื่อนขึ้นมาที่ -50px ก่อน */
            transition: opacity 1s ease-out, transform 1s ease-out; /* กำหนดการเปลี่ยนแปลง */
        }

        .logo.show {
            opacity: 1; /* แสดงเมื่อได้รับคลาส "show" */
            transform: translateY(0); /* เลื่อนลงกลับที่ 0 */
        }

        .tagline {
            font-size: 18px;
            color: #bbb;
            font-style: italic;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>

    <!-- โลโก้ BookVerse -->
    <div class="logo">BookVerse</div>

    <div class="content">
        <!-- ข้อความด้านบน -->
        <p class="tagline">"A book is a device to ignite the imagination."</p>
        
        <!-- ข้อความใหญ่ (คำคมหลัก) -->
        <p class="quote">"THE LOVE OF BOOKS IS A LOVE WHICH REQUIRES NEITHER JUSTIFICATION, APOLOGY, NOR DEFENSE."</p>

        <div class="buttons">
            <a href="index.php" class="btn-go">LET'S GO</a>
        </div>
    </div>

    <script>
        // รอให้หน้าโหลดเสร็จแล้วเพิ่มคลาส "show" ให้กับองค์ประกอบ
        window.addEventListener('load', function() {
            document.querySelector('.content').classList.add('show');
            document.querySelector('.logo').classList.add('show');
        });

        // Script เพื่อเช็คว่าผู้ใช้ login แล้วหรือยัง
        document.querySelector('.btn-go').addEventListener('click', function(e) {
            <?php if (isset($_SESSION['user_id'])): ?>
                window.location.href = 'index.php'; // หาก login แล้วไปที่ index.php
            <?php else: ?>
                window.location.href = 'login.php'; // ถ้าไม่ login ให้ไปที่หน้า login.php
            <?php endif; ?>
        });
    </script>
</body>
</html>
