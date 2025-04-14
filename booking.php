<?php
// booking.php
session_start();
include('includes/db.php');
$page_title = 'Бронирование';
include('templates/header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['equipment_id'])) {
    $equipment_id = $_GET['equipment_id'];
    $stmt = $pdo->prepare("SELECT * FROM equipment WHERE id = ?");
    $stmt->execute([$equipment_id]);
    $equipment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $total_price = $equipment['price_per_day'] * (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);

        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, equipment_id, start_date, end_date, total_price) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $equipment['id'], $start_date, $end_date, $total_price]);

        header('Location: index.php');
    }
}
?>
<h1>Бронирование оборудования: <?php echo $equipment['name']; ?></h1>

<form method="POST">
    <label for="start_date">Дата начала:</label><br>
    <input type="date" id="start_date" name="start_date" required><br><br>

    <label for="end_date">Дата окончания:</label><br>
    <input type="date" id="end_date" name="end_date" required><br><br>

    <input type="submit" value="Забронировать">
</form>

<?php include('templates/footer.php'); ?>
