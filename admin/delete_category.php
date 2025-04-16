<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/db.php');

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: /login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: /admin/categories.php');
    exit;
}

$category_id = $_GET['id'];

// Удаляем категорию из базы
$stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
$stmt->execute([$category_id]);

$_SESSION['success'] = "Категория успешно удалена!";
header('Location: /admin/categories.php');
exit;
?>
