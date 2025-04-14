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

    if (!$equipment) {
        echo "Оборудование не найдено.";
        exit;
    }

    // Проверка доступности оборудования на выбранные даты
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        // Проверка на корректность дат
        if (strtotime($start_date) >= strtotime($end_date)) {
            $error_message = "Дата начала не может быть позже даты окончания.";
        } else {
            // Проверка на занятость оборудования
            $stmt = $pdo->prepare("SELECT * FROM bookings WHERE equipment_id = ? AND status = 'Выдано' AND (
                (start_date BETWEEN ? AND ?) OR (end_date BETWEEN ? AND ?)
            )");
            $stmt->execute([$equipment_id, $start_date, $end_date, $start_date, $end_date]);
            $existing_booking = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_booking) {
                $error_message = "Оборудование уже забронировано на выбранные даты.";
            } else {
                // Вычисляем итоговую стоимость
                $total_price = $equipment['price_per_day'] * (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);

                // Добавляем новое бронирование
                $stmt = $pdo->prepare("INSERT INTO bookings (user_id, equipment_id, start_date, end_date, total_price, status) VALUES (?, ?, ?, ?, ?, 'В обработке')");
                $stmt->execute([$_SESSION['user_id'], $equipment['id'], $start_date, $end_date, $total_price]);

                // Обновляем количество доступного оборудования
                $stmt = $pdo->prepare("UPDATE equipment SET availability = availability - 1 WHERE id = ?");
                $stmt->execute([$equipment['id']]);

                header('Location: dashboard.php');
                exit;
            }
        }
    }
} else {
    echo "Оборудование не указано.";
    exit;
}
?>

<h1>Бронирование оборудования: <?php echo $equipment['name']; ?></h1>

<?php if (isset($error_message)): ?>
    <div class="error-message"><?php echo $error_message; ?></div>
<?php endif; ?>

<form method="POST">
    <label for="start_date">Дата начала:</label><br>
    <input type="date" id="start_date" name="start_date" required><br><br>

    <label for="end_date">Дата окончания:</label><br>
    <input type="date" id="end_date" name="end_date" required><br><br>

    <input type="submit" value="Забронировать">
</form>

<?php include('templates/footer.php'); ?>
