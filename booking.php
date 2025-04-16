<?php
session_start();
include('assets/db.php');

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

// Проверка ID оборудования
if (!isset($_GET['equipment_id'])) {
    header('Location: /index.php');
    exit;
}

echo '<link rel="stylesheet" href="/styles/booking.css">';
echo '<link rel="stylesheet" href="/styles/equipment_catalog.css">';

$equipment_id = $_GET['equipment_id'];

// Получаем информацию об оборудовании с категорией
$stmt = $pdo->prepare("
    SELECT e.*, c.name AS category_name 
    FROM equipment e 
    LEFT JOIN categories c ON e.category_id = c.id 
    WHERE e.id = ?
");
$stmt->execute([$equipment_id]);
$equipment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipment) {
    header('Location: /index.php');
    exit;
}

$error = '';
$success = '';

// Обработка формы бронирования
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $user_id = $_SESSION['user_id'];

    // Проверка корректности дат
    if (strtotime($end_date) <= strtotime($start_date)) {
        $error = "Дата окончания должна быть позже даты начала.";
    } else {
        $days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
        $total_price = $equipment['price_per_day'] * $days;

        // Проверка доступности
        $stmt = $pdo->prepare("
            SELECT * FROM bookings 
            WHERE equipment_id = ? 
              AND (start_date <= ? AND end_date >= ?)
        ");
        $stmt->execute([$equipment_id, $end_date, $start_date]);
        $existing_booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_booking) {
            $error = "Оборудование уже забронировано на выбранные даты.";
        } else {
            // Добавление бронирования
            $stmt = $pdo->prepare("
                INSERT INTO bookings (user_id, equipment_id, start_date, end_date, status, total_price) 
                VALUES (?, ?, ?, ?, 'В обработке', ?)
            ");
            $stmt->execute([$user_id, $equipment_id, $start_date, $end_date, $total_price]);

            $_SESSION['success'] = "Бронирование успешно создано!";
            header('Location: /account.php');
            exit;
        }
    }
}

include('templates/header.php');
?>

<main>
    <h1>Бронирование: <?php echo htmlspecialchars($equipment['name']); ?></h1>

    <div class="equipment-details-card">
        <?php if (!empty($equipment['image_path'])): ?>
            <img src="<?php echo htmlspecialchars($equipment['image_path']); ?>" alt="<?php echo htmlspecialchars($equipment['name']); ?>">
        <?php endif; ?>

        <div>
            <p><strong>Категория:</strong> <?php echo htmlspecialchars($equipment['category_name'] ?? 'Без категории'); ?></p>
            <p><strong>Описание:</strong> <?php echo nl2br(htmlspecialchars($equipment['description'])); ?></p>
            <p><strong>Цена за день:</strong> <?php echo htmlspecialchars($equipment['price_per_day']); ?> руб.</p>
            <p><strong>Доступность:</strong> <?php echo $equipment['availability'] > 0 ? 'В наличии' : 'Нет в наличии'; ?></p>
        </div>
    </div>

    <h2>Выберите даты бронирования</h2>

    <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" class="booking-form">
        <label for="start_date">Дата начала:</label>
        <input type="date" id="start_date" name="start_date" required>

        <label for="end_date">Дата окончания:</label>
        <input type="date" id="end_date" name="end_date" required>

        <button type="submit" class="btn">Забронировать</button>
    </form>
</main>

<?php include('templates/footer.php'); ?>
