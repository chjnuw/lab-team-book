<?php
session_start();
include('db.php'); // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'];

// ตรวจสอบว่าผู้ใช้เคยกดไลค์หนังสือเล่มนี้หรือไม่
$sql_check = "SELECT * FROM book_likes WHERE user_id = ? AND book_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $user_id, $book_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows == 0) {
    // ถ้ายังไม่เคยไลค์ ให้เพิ่มการไลค์
    $sql_like = "INSERT INTO book_likes (user_id, book_id) VALUES (?, ?)";
    $stmt_like = $conn->prepare($sql_like);
    $stmt_like->bind_param("ii", $user_id, $book_id);
    $stmt_like->execute();
    echo "liked"; // ส่งผลลัพธ์กลับว่าไลค์แล้ว
    $stmt_like->close();
} else {
    // ถ้าเคยไลค์แล้ว ให้ลบการไลค์
    $sql_unlike = "DELETE FROM book_likes WHERE user_id = ? AND book_id = ?";
    $stmt_unlike = $conn->prepare($sql_unlike);
    $stmt_unlike->bind_param("ii", $user_id, $book_id);
    $stmt_unlike->execute();
    echo "unliked"; // ส่งผลลัพธ์กลับว่ายกเลิกไลค์แล้ว
    $stmt_unlike->close();
}

$stmt_check->close();
$conn->close();
?>
