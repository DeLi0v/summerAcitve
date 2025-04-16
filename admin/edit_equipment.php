<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/db.php');

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: /login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: /admin/bookings.php');
    exit;
}

echo '<link rel="stylesheet" href="/styles/admin.css">';
$booking_id = $_GET['id'];

// Получаем информацию о бронировании
$stmt = $pdo->prepare("SELECT bookings.*, users.name AS user_name, equipment.name AS equipment_name 
                       FROM bookings 
                       JOIN users ON bookings.user_id = users.id
                       JOIN equipment ON bookings.equipment_id = equipment.id
                       WHERE bookings.id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header('Location: /admin/bookings.php');
    exit;
}

// Обработка формы редактирования бронирования
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    // Проверка, что выбранный статус корректен
    $valid_statuses = ['В обработке', 'Выдано', 'Отменено', 'Завершено'];
    if (!in_array($status, $valid_statuses)) {
        $error = "Некорректный статус бронирования.";
    } else {
        // Обновляем информацию о бронировании
        $stmt = $pdo->prepare("UPDATE bookings SET start_date = ?, end_date = ?, status = ? WHERE id = ?");
        $stmt->execute([$start_date, $end_date, $status, $booking_id]);

        $_SESSION['success'] = "Бронирование успешно обновлено!";
        header('Location: /admin/bookings.php');
        exit;
    }
}

include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php');
?>

<main>
    <h1 class="text-center">Редактировать бронирование</h1>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/back_button.php'); ?>

    <!-- Выводим сообщение об ошибке, если оно есть -->
    <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="booking-info">
        <p><strong>Пользователь:</strong> <?php echo htmlspecialchars($booking['user_name']); ?></p>
        <p><strong>Оборудование:</strong> <?php echo htmlspecialchars($booking['equipment_name']); ?></p>
        <p><strong>Дата начала:</strong> <?php echo htmlspecialchars($booking['start_date']); ?></p>
        <p><strong>Дата окончания:</strong> <?php echo htmlspecialchars($booking['end_date']); ?></p>
        <p><strong>Статус:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>
    </div>

    <form method="POST" class="admin-form">
        <label for="start_date">Дата начала</label>
        <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($booking['start_date']); ?>" required>

        <label for="end_date">Дата окончания</label>
        <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($booking['end_date']); ?>" required>

        <label for="status">Статус</label>
        <select id="status" name="status" required>
            <option value="В обработке" <?php echo $booking['status'] == 'В обработке' ? 'selected' : ''; ?>>В обработке</option>
            <option value="Выдано" <?php echo $booking['status'] == 'Выдано' ? 'selected' : ''; ?>>Выдано</option>
            <option value="Отменено" <?php echo $booking['status'] == 'Отменено' ? 'selected' : ''; ?>>Отменено</option>
            <option value="Завершено" <?php echo $booking['status'] == 'Завершено' ? 'selected' : ''; ?>>Завершено</option>
        </select>

        <input type="submit" value="Сохранить изменения">
        <a href="/admin/bookings.php" class="cancel-button">Отмена</a>
    </form>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
