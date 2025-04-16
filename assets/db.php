<?php
$host = "192.168.0.2";
$dbname = "rental_db";
$username = "webuser";
$password = "1";

$conn = mysqli_connect($host, $username, $password, $dbname);

// Проверяем, удалось ли подключиться к базе данных
if (!$conn) {
    die("Подключение не удалось: " . mysqli_connect_error());
}
?>