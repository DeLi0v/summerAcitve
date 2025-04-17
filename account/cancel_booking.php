<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/db.php');

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: /login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: /account/account.php');
    exit;
}

$booking_id = $_GET['id'];

// Получаем информацию о бронировании
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header('Location: /account/account.php');
    exit;
}

// Отменяем бронирование
$stmt = $pdo->prepare("UPDATE bookings SET status = 'Отменено' WHERE id = ?");
$stmt->execute([$booking_id]);

$_SESSION['success'] = "Бронирование отменено!";
header('Location: /account/account.php');
exit;
?>
