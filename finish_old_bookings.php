<?php
// Скрипт завершает просроченные бронирования

require_once(__DIR__ . '/assets/db.php');

try {
    // Находим просроченные бронирования со статусом "Выдано"
    $stmt = $pdo->prepare("SELECT id FROM bookings 
                           WHERE end_date < CURDATE() AND status = 'Выдано'");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $count = 0;

    foreach ($bookings as $booking) {
        // Меняем статус на "Завершено"
        $updateBooking = $pdo->prepare("UPDATE bookings SET status = 'Завершено' WHERE id = ?");
        $updateBooking->execute([$booking['id']]);
        $count++;
    }

    // Логирование
    file_put_contents(__DIR__ . '/cron.log', "[" . date('Y-m-d H:i:s') . "] Завершено: {$count} бронирований" . PHP_EOL, FILE_APPEND);

} catch (PDOException $e) {
    file_put_contents(__DIR__ . '/cron.log', "[" . date('Y-m-d H:i:s') . "] Ошибка: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
}
?>
