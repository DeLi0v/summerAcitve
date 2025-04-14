<?php
// delete_booking.php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

$booking_id = $_GET['id'];

// Проверяем, существует ли бронирование
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if ($booking) {
    // Удаляем бронирование
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->execute([$booking_id]);

    header('Location: panel.php');
} else {
    echo "Бронирование не найдено.";
}
?>
