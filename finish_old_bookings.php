<?php
// Скрипт завершает просроченные бронирования и увеличивает количество доступного оборудования

require_once(__DIR__ . '/assets/db.php');

try {
    // Получаем все бронирования, которые нужно завершить
    $stmt = $pdo->prepare("SELECT id, equipment_id FROM bookings 
                           WHERE end_date < CURDATE() AND status = 'Выдано'");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $count = 0;

    foreach ($bookings as $booking) {
        // Завершаем бронирование
        $updateBooking = $pdo->prepare("UPDATE bookings SET status = 'Завершено' WHERE id = ?");
        $updateBooking->execute([$booking['id']]);

        // Увеличиваем доступность оборудования
        $updateEquipment = $pdo->prepare("UPDATE equipment SET availability = availability + 1 WHERE id = ?");
        $updateEquipment->execute([$booking['equipment_id']]);

        $count++;
    }

    // Лог
    file_put_contents(__DIR__ . '/cron.log', "[" . date('Y-m-d H:i:s') . "] Завершено: {$count} бронирований" . PHP_EOL, FILE_APPEND);

} catch (PDOException $e) {
    file_put_contents(__DIR__ . '/cron.log', "[" . date('Y-m-d H:i:s') . "] Ошибка: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
}
?>
