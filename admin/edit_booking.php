<?php
// edit_booking.php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

$page_title = 'Редактировать бронирование';
include('templates/header.php');

$booking_id = $_GET['id'];

// Получаем информацию о бронировании
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    echo "Бронирование не найдено.";
    exit;
}

// Получаем список оборудования
$stmt = $pdo->query("SELECT * FROM equipment");
$equipments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $equipment_id = $_POST['equipment_id'];
    $status = $_POST['status'];

    // Обновляем информацию о бронировании
    $stmt = $pdo->prepare("UPDATE bookings SET start_date = ?, end_date = ?, equipment_id = ?, status = ? WHERE id = ?");
    $stmt->execute([$start_date, $end_date, $equipment_id, $status, $booking_id]);

    header('Location: panel.php');
}
?>

<h1>Редактировать бронирование</h1>

<form method="POST">
    <label for="equipment_id">Оборудование</label>
    <select name="equipment_id" id="equipment_id">
        <?php foreach ($equipments as $equipment): ?>
            <option value="<?php echo $equipment['id']; ?>" <?php echo $equipment['id'] == $booking['equipment_id'] ? 'selected' : ''; ?>>
                <?php echo $equipment['name']; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="start_date">Дата начала</label>
    <input type="date" name="start_date" id="start_date" value="<?php echo $booking['start_date']; ?>" required>

    <label for="end_date">Дата окончания</label>
    <input type="date" name="end_date" id="end_date" value="<?php echo $booking['end_date']; ?>" required>

    <label for="status">Статус</label>
    <select name="status" id="status">
        <option value="В обработке" <?php echo $booking['status'] == 'В обработке' ? 'selected' : ''; ?>>В обработке</option>
        <option value="Выдано" <?php echo $booking['status'] == 'Выдано' ? 'selected' : ''; ?>>Выдано</option>
        <option value="Отменено" <?php echo $booking['status'] == 'Отменено' ? 'selected' : ''; ?>>Отменено</option>
        <option value="Завершено" <?php echo $booking['status'] == 'Завершено' ? 'selected' : ''; ?>>Завершено</option>
    </select>

    <button type="submit">Сохранить изменения</button>
</form>

<?php include('templates/footer.php'); ?>
