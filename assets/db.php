<?php
var $host = "192.168.0.2";
var $dbname = "rental_db";
var $username = "webuser";
var $password = "1";

var $conn;

$conn = mysqli_connect($this->hostname, $this->username, $this->password, $this->database);
// try {
//     $conn = mysqli_connect($this->hostname, $this->username, $this->password, $this->database);
//     $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
//     // Включим режим ошибок
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//     die("Ошибка подключения к базе данных: " . $e->getMessage());
// }

if (!$this->conn) {
    die("Подключение не удалось: " . mysqli_connect_error());
}