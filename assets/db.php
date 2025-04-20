<?php
$host = "192.168.0.2";
$dbname = "rental_db";
$username = "webuser";
$password = "1";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "<!DOCTYPE html>
    <html lang='ru'>
    <head>
        <meta charset='UTF-8'>
        <title>Ошибка подключения</title>
        <script>
            // Перезагрузка страницы каждые 5 секунд
            setTimeout(function() {
                location.reload();
            }, 5000);
        </script>
        <style>
            body {
                font-family: sans-serif;
                background-color: #fefefe;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100vh;
                text-align: center;
                color: #333;
            }
        </style>
    </head>
    <body>
        <h1>Ошибка подключения к базе данных</h1>
        <p>Попробуем переподключиться через несколько секунд...</p>
        <p style='color: #888;'>".htmlspecialchars($e->getMessage())."</p>
    </body>
    </html>";
    exit;
}
?>
