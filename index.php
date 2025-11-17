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
?>

<h1>Каталог оборудования</h1>

<!-- Форма фильтра -->
<div class="filter-wrapper">
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
        <button type="submit">Применить</button>
    </form>
</div>

<?php if (count($equipments) > 0): ?>
    <div class="equipment-container">
        <?php foreach ($equipments as $equipment): ?>
            <div class="equipment-card">
                <?php if (!empty($equipment['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($equipment['image_path']); ?>"
                        alt="<?php echo htmlspecialchars($equipment['name']); ?>">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($equipment['name']); ?></h3>
                <p><strong>Категория:</strong> <?php echo htmlspecialchars($equipment['category_name'] ?? 'Без категории'); ?>
                </p>
                <p><strong>Цена за день:</strong> <?php echo htmlspecialchars($equipment['price_per_day']); ?> руб.</p>
                <p><?php echo nl2br(htmlspecialchars($equipment['description'])); ?></p>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] != 'admin'):?>
                    <a href="booking.php?equipment_id=<?php echo $equipment['id']; ?>" class="btn">Забронировать</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Оборудование не найдено по выбранным фильтрам.</p>
<?php endif; ?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>