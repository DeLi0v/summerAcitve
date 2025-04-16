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
    header('Location: equipment.php');
    exit;
}

$equipment_id = (int)$_GET['id'];

// Удаляем оборудование
$stmt = $pdo->prepare("DELETE FROM equipment WHERE id = ?");
$stmt->execute([$equipment_id]);

// После удаления — редирект
header('Location: equipment.php');
exit;
