<?php
session_start();
include('../assets/db.php');

// Проверка авторизации и роли администратора
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

// Проверка наличия ID
if (!isset($_GET['id'])) {
    header('Location: admin_users.php');
    exit;
}

$user_id = $_GET['id'];

// Не даём администратору удалить самого себя
if ($_SESSION['user_id'] == $user_id) {
    $_SESSION['message'] = 'Вы не можете удалить самого себя.';
    $_SESSION['message_type'] = 'error';
    exit;
}

// Удаление пользователя
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$user_id]);

$_SESSION['message'] = 'Пользователь успешно удалён.';
$_SESSION['message_type'] = 'success';
header('Location: admin_users.php');
exit;
?>
