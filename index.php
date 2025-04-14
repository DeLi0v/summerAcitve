<?php
// index.php
session_start();
include('includes/db.php');
$page_title = 'Каталог оборудования';
include('templates/header.php');

$stmt = $pdo->query("SELECT * FROM equipment WHERE availability > 0");
$equipments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Каталог оборудования</h1>
<?php foreach ($equipments as $equipment): ?>
    <div>
        <h3><?php echo $equipment['name']; ?></h3>
        <p><?php echo $equipment['description']; ?></p>
        <p>Цена: <?php echo $equipment['price_per_day']; ?> руб./день</p>
        <a href="booking.php?equipment_id=<?php echo $equipment['id']; ?>">Забронировать</a>
    </div>
<?php endforeach; ?>

<?php include('templates/footer.php'); ?>
