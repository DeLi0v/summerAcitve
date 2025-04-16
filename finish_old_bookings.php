<?php
// Скрипт завершает бронирования, у которых дата окончания меньше текущей даты

// Подключаем базу данных
require_once($_SERVER['DOCUMENT_ROOT'] . '/assets/db.php');

try {
    $stmt = $pdo->prepare("UPDATE bookings 
                           SET status = 'Завершено' 
                           WHERE end_date < CURDATE() 
                             AND status = 'Выдано'");
    $stmt->execute();

    // Можно сохранить лог (по желанию)
    file_put_contents(__DIR__ . '/cron.log', "[" . date('Y-m-d H:i:s') . "] Завершено бронирований: " . $stmt->rowCount() . PHP_EOL, FILE_APPEND);
} catch (PDOException $e) {
    file_put_contents(__DIR__ . '/cron.log', "[" . date('Y-m-d H:i:s') . "] Ошибка: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
}
?>
