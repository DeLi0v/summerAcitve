<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/db.php');

echo '<link rel="stylesheet" href="/styles/equipment_catalog.css">';

$page_title = 'Каталог оборудования';
include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php');

// Получаем текущую дату
$today = date('Y-m-d');

// Запрашиваем оборудование с категориями
$stmt = $pdo->query("
    SELECT e.*, c.name AS category_name
    FROM equipment e
    LEFT JOIN categories c ON e.category_id = c.id
");
$equipments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем количество активных бронирований для каждого оборудования
$booking_stmt = $pdo->prepare("
    SELECT equipment_id, COUNT(*) AS active_bookings
    FROM bookings
    WHERE status != 'Отменено'
      AND start_date <= :today
      AND end_date >= :today
    GROUP BY equipment_id
");
$booking_stmt->execute(['today' => $today]);
$bookings = $booking_stmt->fetchAll(PDO::FETCH_ASSOC);

// Преобразуем в удобный массив
$active_counts = [];
foreach ($bookings as $row) {
    $active_counts[$row['equipment_id']] = $row['active_bookings'];
}

// Фильтруем доступное оборудование
$available_equipments = [];
foreach ($equipments as $equipment) {
    $total = (int)$equipment['availability'];
    $used = $active_counts[$equipment['id']] ?? 0;

    if ($total - $used > 0) {
        $available_equipments[] = $equipment;
    }
}
?>

<h1>Каталог оборудования</h1>

<?php if (count($available_equipments) > 0): ?>
    <div class="equipment-container">
        <?php foreach ($available_equipments as $equipment): ?>
            <div class="equipment-card">
                <?php if (!empty($equipment['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($equipment['image_path']); ?>" alt="<?php echo htmlspecialchars($equipment['name']); ?>">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($equipment['name']); ?></h3>
                <p><strong>Категория:</strong> <?php echo htmlspecialchars($equipment['category_name'] ?? 'Без категории'); ?></p>
                <p><strong>Цена за день:</strong> <?php echo htmlspecialchars($equipment['price_per_day']); ?> руб.</p>
                <p><?php echo nl2br(htmlspecialchars($equipment['description'])); ?></p>
                <a href="booking.php?equipment_id=<?php echo $equipment['id']; ?>" class="btn">Забронировать</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>На данный момент всё оборудование недоступно.</p>
<?php endif; ?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
