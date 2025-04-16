<?php
$host = "192.168.0.2";
$dbname = "rental_db";
$username = "webuser";
$password = "1";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Включим режим ошибок
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Включаем вывод ошибок, чтобы видеть, если что-то не так
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>