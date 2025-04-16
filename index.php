<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/db.php');

echo '<link rel="stylesheet" href="/styles/equipment_catalog.css">';

$page_title = 'Каталог оборудования';
include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php');

// Запрашиваем оборудование с категориями
$stmt = $pdo->query("
    SELECT e.*, c.name AS category_name
    FROM equipment e
    LEFT JOIN categories c ON e.category_id = c.id
    WHERE e.availability > 0
");
$equipments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Каталог оборудования</h1>

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
    <p>На данный момент всё оборудование недоступно.</p>
<?php endif; ?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
