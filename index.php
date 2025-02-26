<?php
session_start();
include('db.php'); // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// SQL query เพื่อดึงข้อมูลหนังสือทั้งหมด
$sql_books = "
    SELECT b.book_id, b.title, b.author, b.image, c.category_name
    FROM books b
    JOIN categories c ON b.category_id = c.category_id";
$result_books = $conn->query($sql_books);

// ดึงข้อมูลทั้งหมดในรูปแบบ array
$books = [];
while ($row = $result_books->fetch_assoc()) {
    $books[] = $row;
}

// ดึงหมวดหมู่จากฐานข้อมูล
$sql_categories = "SELECT category_name FROM categories";
$result_categories = $conn->query($sql_categories);
$categories = [];
while ($row = $result_categories->fetch_assoc()) {
    $categories[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <title>BookVerse - Book Verse</title>
    <style>
        body {
            font-family: 'Playfair Display', serif;
            margin: 0;
            padding: 0;
            background-color: #faf9f6;
            color: #333;
        }

        /* Animation Classes */
        .animate-up {
            opacity: 0;
            transform: translateY(50px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* General Styling */
        .book-cover img {
            width: 280px;
            height: 420px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .book-info h1 {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            line-height: 1.2;
            margin: 0;
        }

        .book-info p {
            color: #555;
            font-size: 18px;
            margin: 10px 0 20px;
        }

        /* Header */
        .header {

   
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header .logo {
            font-size: 32px;
            font-weight: bold;
        }

        .header .nav a {
            text-decoration: none;
            color: #333;
            margin: 0 15px;
            font-size: 16px;
        }

        /* Main section */
        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
            position: relative;
        }

        .book-info {
            max-width: 600px;
            text-align: left;
            margin-right: 50px;
        }

        .book-info .author {
            font-size: 16px;
            color: #888;
            margin-bottom: 20px;
        }

        .book-info .read-more {
            padding: 10px 25px;
            background-color: #d4a373;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-transform: uppercase;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .book-info .read-more:hover {
            background-color: #b6895e;
        }

        .book-cover {
            max-width: 300px;
        }

        /* ปุ่มเลื่อน */
        .arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 30px;
            cursor: pointer;
            user-select: none;
        }

        .arrow.left {
            left: 20px;
        }

        .arrow.right {
            right: 20px;
        }

        /* เส้นขีดแบ่งสำหรับหัวข้อ */
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .section-title .subheading {
            font-size: 14px;
            color: #888;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .section-title h2 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 700;
            margin: 0;
            color: #333;
        }

        .section-title .divider {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }

        .section-title .divider::before,
        .section-title .divider::after {
            content: '';
            height: 1px;
            background-color: #ddd;
            flex: 1;
            margin: 0 20px;
        }

        /* Featured Books Section */
        .featured-section {
            padding: 0 50px;
            text-align: center;
        }

        .featured-books {
            display: flex;
            justify-content: space-around;
            gap: 20px;
        }

        .featured-book {
            width: 200px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .featured-book:hover {
            transform: scale(1.1);
        }

        .featured-book img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .featured-book .title {
            font-size: 16px;
            margin-top: 10px;
        }

        .featured-book .author {
            color: #888;
            font-size: 14px;
        }

        /* เส้นขีดล่างของหนังสือ */
        .divider {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 50px auto;
            width: 80%;
        }

        .divider::before,
        .divider::after {
            content: '';
            height: 1px;
            background-color: #ddd;
            flex: 1;
            margin: 0 20px;
        }

        /* หมวดหมู่ */
        .category-filters {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .category-filters a {
            font-size: 16px;
            color: #333;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            position: relative;
        }

        .category-filters a.active {
            font-weight: bold;
            color: #d4a373;
        }

        .category-filters a.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #d4a373;
        }

        .category-filters a:hover {
            background-color: #f0f0f0;
        }

        /* รูปแบบการจัดเรียงหนังสือ */
        .popular-books {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            justify-items: center;
            align-items: start;
        }

        .book-card {
            width: 200px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .book-card:hover {
            transform: scale(1.05);
        }

        .book-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .book-card .title {
            font-size: 16px;
            margin-top: 10px;
        }

        .book-card .author {
            color: #888;
            font-size: 14px;
            margin-top: 5px;
        }

        /* Section: Quote of the Day */
        .quote-section {
            text-align: center;
            margin: 40px 0;
            font-size: 20px;
            color: #555;
        }

        .quote-section .quote-text {
            font-style: italic;
            margin-bottom: 10px;
            color: #333;
        }

        .quote-section .quote-author {
            font-weight: bold;
            color: #888;
            font-size: 18px;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header animate-up">
    <div class="logo">BookVerse</div>
    <div class="nav">
        <a href="index.php">Home</a>
        <a href="home.php">All Books</a>
        <a href="fvbook.php">Favorite Bookst</a>
        <a href="admin.php">Admin</a>
        <a href="account.php">Account</a>
        <a href="logout.php">Logout</a>
        
    </div>
</div>

<!-- Main Content -->
<div class="main-content animate-up">
    <div class="arrow left" onclick="previousBook()">&#10094;</div>
    <div class="book-info" id="book-info">
        <!-- ข้อมูลหนังสือจะแสดงผลที่นี่ผ่าน JavaScript -->
    </div>
    <div class="book-cover" id="book-cover">
        <!-- รูปหนังสือจะแสดงผลที่นี่ผ่าน JavaScript -->
    </div>
    <div class="arrow right" onclick="nextBook()">&#10095;</div>
</div>

<!-- Featured Books Section -->
<div class="section-title animate-up">
    <p class="subheading">SOME QUALITY ITEMS</p>
    <h2>Featured Books</h2>
    <div class="divider"></div> <!-- เส้นขีดบน -->
</div>

<div class="featured-section animate-up">
    <div class="featured-books" id="featured-books">
        <!-- หนังสือแบบสุ่มจะแสดงที่นี่ -->
    </div>
</div>

<!-- เส้นขีดล่าง -->
<div class="divider"></div>

<!-- Popular Books Section -->
<div class="section-title animate-up">
    <p class="subheading">SOME QUALITY ITEMS</p>
    <h2>Popular Books</h2>
    <div class="divider"></div>
</div>

<div class="category-filters animate-up" id="category-filters">
    <?php foreach ($categories as $category): ?>
        <a href="#" onclick="filterBooks('<?php echo $category['category_name']; ?>', event)">
            <?php echo $category['category_name']; ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="popular-section animate-up">
    <div class="popular-books" id="popular-books">
        <!-- หนังสือในหมวดหมู่จะแสดงที่นี่ -->
    </div>
</div>

<div class="divider"></div>

<!-- Section: Quote of the Day -->
<div class="section-title animate-up">
    <h2>Quote Of The Day</h2>
    <div class="divider"></div>
</div>

<div class="quote-section animate-up">
    <p class="quote-text">“The more that you read, the more things you will know. The more that you learn, the more places you'll go.”</p>
    <p class="quote-author">- Dr. Seuss</p>
</div>

<div class="divider"></div>



<script>
// หนังสือจาก PHP
var books = <?php echo json_encode($books); ?>;
var currentBookIndex = 0;
var filteredBooks = books; // ตัวแปรสำหรับเก็บหนังสือตามหมวดหมู่

// ฟังก์ชันแสดงผลหนังสือ
function showBook(index) {
    var book = books[index];
    document.getElementById('book-info').innerHTML = `
        <h1>${book.title}</h1>
        <p class="author">โดย ${book.author}</p>
        <button class="read-more" onclick="location.href='book_detail.php?book_id=${book.book_id}'">Read More</button>
    `;
    document.getElementById('book-cover').innerHTML = `
        <img src="uploads/${book.image}" alt="${book.title}">
    `;
}

// แสดงหนังสือเล่มแรก
showBook(currentBookIndex);

// ฟังก์ชันเลื่อนหนังสือไปข้างหน้า
function nextBook() {
    currentBookIndex = (currentBookIndex + 1) % books.length;
    showBook(currentBookIndex);
}

// ฟังก์ชันเลื่อนหนังสือไปข้างหลัง
function previousBook() {
    currentBookIndex = (currentBookIndex - 1 + books.length) % books.length;
    showBook(currentBookIndex);
}

// สลับหนังสืออัตโนมัติทุก 5 วินาที
setInterval(nextBook, 5000);

// ฟังก์ชันแสดง Featured Books แบบสุ่ม
function showFeaturedBooks() {
    const featuredBooksContainer = document.getElementById('featured-books');
    featuredBooksContainer.innerHTML = ''; // ล้างเนื้อหาก่อน
    
    let randomBooks = [];
    while (randomBooks.length < 4) {
        let randomIndex = Math.floor(Math.random() * books.length);
        if (!randomBooks.includes(randomIndex)) {
            randomBooks.push(randomIndex);
        }
    }

    randomBooks.forEach(index => {
        const book = books[index];
        featuredBooksContainer.innerHTML += `
            <div class="featured-book" onclick="location.href='book_detail.php?book_id=${book.book_id}'">
                <img src="uploads/${book.image}" alt="${book.title}">
                <div class="title">${book.title}</div>
                <div class="author">by ${book.author}</div>
            </div>
        `;
    });
}

// แสดง Featured Books ตอนโหลดหน้า
showFeaturedBooks();

// เปลี่ยน Featured Books ทุก 10 วินาที
setInterval(showFeaturedBooks, 10000);

// ฟังก์ชันแสดง Popular Books
function showBooks() {
    const bookContainer = document.getElementById('popular-books');
    bookContainer.innerHTML = ''; // ล้างเนื้อหาก่อน
    let randomBooks = filteredBooks.slice(0, 12); // สุ่มหนังสือ 12 เล่ม

    randomBooks.forEach(book => {
        bookContainer.innerHTML += `
            <div class="book-card" onclick="location.href='book_detail.php?book_id=${book.book_id}'">
                <img src="uploads/${book.image}" alt="${book.title}">
                <div class="title">${book.title}</div>
                <div class="author">by ${book.author}</div>
            </div>
        `;
    });
}

// ฟังก์ชันกรองหนังสือตามหมวดหมู่
function filterBooks(category, event) {
    event.preventDefault(); // ป้องกันการเด้งกลับไปด้านบน
    document.querySelectorAll('.category-filters a').forEach(function(link) {
        link.classList.remove('active'); // เอา class active ออกจากหมวดหมู่อื่น
    });
    event.target.classList.add('active'); // เพิ่ม class active ให้หมวดหมู่ที่เลือก

    filteredBooks = books.filter(book => book.category_name === category);
    showBooks();
}

// แสดง Popular Books ตอนโหลดหน้า
showBooks();

// Intersection Observer สำหรับ scroll animation
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
});

// เพิ่ม observer ให้กับ element ที่มี class "animate-up"
document.querySelectorAll('.animate-up').forEach(el => {
    observer.observe(el);
});
</script>



</body>
</html>
