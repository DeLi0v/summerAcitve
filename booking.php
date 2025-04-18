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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $user_id = $_SESSION['user_id'];

    if (strtotime($end_date) <= strtotime($start_date)) {
        $error = "Дата окончания должна быть позже даты начала.";
    } else {
        $days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
        $total_price = $equipment['price_per_day'] * $days;

        // Считаем количество уже забронированных единиц на указанный период
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as booked_count
            FROM bookings 
            WHERE equipment_id = :equipment_id 
            AND status != 'Отменено'
            AND (start_date <= :end_date AND end_date >= :start_date)
        ");
        $stmt->execute([
            'equipment_id' => $equipment_id,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $booked_count = $result['booked_count'] ?? 0;

        $available_units = $equipment['availability'] - $booked_count;

        if ($available_units <= 0) {
            $error = "Оборудование полностью забронировано на выбранные даты.";
        } else {
            // Вставляем новое бронирование
            $stmt = $pdo->prepare("
                INSERT INTO bookings (user_id, equipment_id, start_date, end_date, status, total_price) 
                VALUES (?, ?, ?, ?, 'В обработке', ?)
            ");
            $stmt->execute([$user_id, $equipment_id, $start_date, $end_date, $total_price]);

            $_SESSION['success'] = "Бронирование успешно создано!";
            header('Location: /account/account.php');
            exit;
        }
    }
}

include('templates/header.php');
?>

<main class="container">
    <div class="title-back-header">
        <?php
        $back_url = '/index.php';
        include($_SERVER['DOCUMENT_ROOT'] . '/templates/back_button.php');
        ?>
        <h1>Бронирование: <?php echo htmlspecialchars($equipment['name']); ?></h1>
    </div>
    

    <div class="equipment-details-card">
        <?php if (!empty($equipment['image_path'])): ?>
            <img src="<?php echo '/' . ltrim($equipment['image_path'], '/'); ?>"
                alt="<?php echo htmlspecialchars($equipment['name']); ?>">
        <?php endif; ?>

        <div>
            <p><strong>Категория:</strong>
                <?php echo htmlspecialchars($equipment['category_name'] ?? 'Без категории'); ?></p>
            <p><strong>Описание:</strong> <?php echo nl2br(htmlspecialchars($equipment['description'])); ?></p>
            <p><strong>Цена за день:</strong> <?php echo htmlspecialchars($equipment['price_per_day']); ?> руб.</p>
            <p><strong>Всего в наличии:</strong> <?php echo (int) $equipment['availability']; ?></p>
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