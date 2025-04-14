<?php
// cancel_booking.php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$booking_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Проверяем, существует ли бронирование и принадлежит ли оно текущему пользователю
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if ($booking && $booking['status'] == 'В обработке') {
    // Обновляем статус бронирования на "Отменено"
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'Отменено' WHERE id = ?");
    $stmt->execute([$booking_id]);
    header('Location: dashboard.php');
} else {
    echo "Бронирование не найдено или не может быть отменено.";
}
?>
