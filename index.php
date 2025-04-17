<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/db.php');

echo '<link rel="stylesheet" href="/styles/equipment_catalog.css">';

$page_title = 'Каталог оборудования';
include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php');

// Получаем текущую дату
$today = date('Y-m-d');

// Получаем категории для фильтра
$category_stmt = $pdo->query("SELECT id, name FROM categories");
$categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем параметры фильтра
$selected_category = $_GET['category'] ?? '';
$only_available = isset($_GET['available']) && $_GET['available'] === '1';

// Запрашиваем оборудование с учётом фильтра по категории
$query = "
    SELECT e.*, c.name AS category_name
    FROM equipment e
    LEFT JOIN categories c ON e.category_id = c.id
";
$params = [];

if ($selected_category !== '') {
    $query .= " WHERE e.category_id = :category_id";
    $params['category_id'] = $selected_category;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
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

// Если установлен флаг "только доступное", фильтруем
if ($only_available) {
    $equipments = array_filter($equipments, function ($equipment) use ($active_counts) {
        $total = (int)$equipment['availability'];
        $used = $active_counts[$equipment['id']] ?? 0;
        return $total - $used > 0;
    });
}
?>

<h1>Каталог оборудования</h1>

<!-- Форма фильтра -->
<form method="GET" class="filter-form">
    <label for="category">Категория:</label>
    <select name="category" id="category">
        <option value="">Все категории</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $selected_category == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>
        <input type="checkbox" name="available" value="1" <?= $only_available ? 'checked' : '' ?>>
        Только в наличии
    </label>

    <button type="submit">Применить</button>
</form>

<?php if (count($equipments) > 0): ?>
    <div class="equipment-container">
        <?php foreach ($equipments as $equipment): ?>
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
    <p>Оборудование не найдено по выбранным фильтрам.</p>
<?php endif; ?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
