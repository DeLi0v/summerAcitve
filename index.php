<?php
// index.php
session_start();
include('assets/db.php');
$page_title = 'Каталог оборудования';
include('templates/header.php');

$stmt = mysqli_query($conn, "SELECT * FROM equipment WHERE availability > 0");
$equipments = mysqli_fetch_all($stmt, MYSQLI_ASSOC);
?>

<h1>Каталог оборудования</h1>

<?php if (count($equipments) > 0): ?>
    <div class="equipment-container">
        <?php foreach ($equipments as $equipment): ?>
            <div class="equipment-card">
                <h3><?php echo $equipment['name']; ?></h3>
                <p><strong>Категория:</strong> <?php echo htmlspecialchars($equipment['category']); ?></p>
                <p><strong>Цена за день:</strong> <?php echo $equipment['price_per_day']; ?> руб.</p>
                <a href="booking.php?equipment_id=<?php echo $equipment['id']; ?>">Забронировать</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>На данный момент всё оборудование недоступно.</p>
<?php endif; ?>

<?php include('templates/footer.php'); ?>