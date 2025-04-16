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

$booking_id = $_GET['id'];

// Получаем информацию о бронировании
$stmt = $pdo->prepare("SELECT bookings.*, users.name AS user_name, users.email AS user_email, equipment.name AS equipment_name, equipment.description AS equipment_description 
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

include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php');
?>

<main>
    <h1>Информация о бронировании</h1>

    <p><strong>Пользователь:</strong> <?php echo htmlspecialchars($booking['user_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['user_email']); ?></p>
    <p><strong>Оборудование:</strong> <?php echo htmlspecialchars($booking['equipment_name']); ?></p>
    <p><strong>Описание:</strong> <?php echo htmlspecialchars($booking['equipment_description']); ?></p>
    <p><strong>Дата начала:</strong> <?php echo htmlspecialchars($booking['start_date']); ?></p>
    <p><strong>Дата окончания:</strong> <?php echo htmlspecialchars($booking['end_date']); ?></p>
    <p><strong>Статус:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>
    <p><strong>Итоговая стоимость:</strong> <?php echo htmlspecialchars($booking['total_price']); ?> руб.</p>

    <a href="edit_booking.php?id=<?php echo $booking['id']; ?>">Редактировать</a>
    <a href="cancel_booking.php?id=<?php echo $booking['id']; ?>" onclick="return confirm('Вы уверены?')">Отменить</a>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
