<?php
session_start();
include('assets/db.php');

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

echo '<link rel="stylesheet" href="/styles/booking.css">';

$error = '';
$success = '';

// Получаем список всего оборудования с категориями
$stmt = $pdo->query("
    SELECT e.*, c.name AS category_name 
    FROM equipment e 
    LEFT JOIN categories c ON e.category_id = c.id
");
$equipment_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipment_id = $_POST['equipment_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $user_id = $_SESSION['user_id'];

    // Получаем информацию об этом оборудовании
    $stmt = $pdo->prepare("SELECT * FROM equipment WHERE id = ?");
    $stmt->execute([$equipment_id]);
    $equipment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$equipment) {
        $error = "Оборудование не найдено.";
    } elseif (strtotime($end_date) <= strtotime($start_date)) {
        $error = "Дата окончания должна быть позже даты начала.";
    } else {
        $days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
        $total_price = $equipment['price_per_day'] * $days;

        // Проверка доступности
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
    <h1>Бронирование оборудования</h1>

    <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="equipment-grid">
        <?php foreach ($equipment_list as $equipment): ?>
            <div class="equipment-card">
                <?php if (!empty($equipment['image_path'])): ?>
                    <img src="<?php echo '/' . ltrim($equipment['image_path'], '/'); ?>" alt="<?php echo htmlspecialchars($equipment['name']); ?>">
                <?php endif; ?>

                <h2><?php echo htmlspecialchars($equipment['name']); ?></h2>
                <p><strong>Категория:</strong> <?php echo htmlspecialchars($equipment['category_name'] ?? 'Без категории'); ?></p>
                <p><strong>Описание:</strong> <?php echo nl2br(htmlspecialchars($equipment['description'])); ?></p>
                <p><strong>Цена за день:</strong> <?php echo $equipment['price_per_day']; ?> руб.</p>
                <p><strong>Всего в наличии:</strong> <?php echo $equipment['availability']; ?></p>

                <form method="POST" class="booking-form">
                    <input type="hidden" name="equipment_id" value="<?php echo $equipment['id']; ?>">

                    <label>Дата начала:</label>
                    <input type="date" name="start_date" required>

                    <label>Дата окончания:</label>
                    <input type="date" name="end_date" required>

                    <button type="submit" class="btn">Забронировать</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include('templates/footer.php'); ?>
