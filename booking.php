<?php
session_start();
include('assets/db.php');

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

if (!isset($_GET['equipment_id'])) {
    header('Location: /index.php');
    exit;
}

$equipment_id = $_GET['equipment_id'];

// Получаем информацию о выбранном оборудовании
$stmt = $pdo->prepare("SELECT * FROM equipment WHERE id = ?");
$stmt->execute([$equipment_id]);
$equipment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipment) {
    header('Location: /index.php');
    exit;
}

// Обработка формы бронирования
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $user_id = $_SESSION['user_id'];
    $total_price = $equipment['price_per_day'] * (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);

    // Проверяем доступность оборудования на выбранные даты
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE equipment_id = ? AND (start_date <= ? AND end_date >= ?)");
    $stmt->execute([$equipment_id, $end_date, $start_date]);
    $existing_booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing_booking) {
        $error = "Оборудование уже забронировано на выбранные даты.";
    } else {
        // Создаем новое бронирование
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, equipment_id, start_date, end_date, status, total_price) 
                               VALUES (?, ?, ?, ?, 'В обработке', ?)");
        $stmt->execute([$user_id, $equipment_id, $start_date, $end_date, $total_price]);

        $_SESSION['success'] = "Бронирование успешно создано!";
        header('Location: /account.php');
        exit;
    }
}

include('templates/header.php');
?>

<main>
    <h1>Бронирование оборудования: <?php echo htmlspecialchars($equipment['name']); ?></h1>

    <div class="equipment-details">
        <p><strong>Описание:</strong> <?php echo htmlspecialchars($equipment['description']); ?></p>
        <p><strong>Цена за день:</strong> <?php echo $equipment['price_per_day']; ?> руб.</p>
        <p><strong>Доступность:</strong> <?php echo $equipment['availability'] > 0 ? 'Есть в наличии' : 'Недоступно'; ?></p>
    </div>

    <h2>Выберите даты бронирования</h2>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="start_date">Дата начала:</label>
        <input type="date" id="start_date" name="start_date" required>

        <label for="end_date">Дата окончания:</label>
        <input type="date" id="end_date" name="end_date" required>

        <input type="submit" value="Забронировать">
    </form>
</main>

<?php include('templates/footer.php'); ?>
