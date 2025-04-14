<?php
// dashboard.php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$page_title = 'Личный кабинет';
include('templates/header.php');

// Получаем бронирования пользователя
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ?");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Личный кабинет</h1>

<h2>Мои бронирования</h2>
<?php if (count($bookings) > 0): ?>
    <table>
        <tr>
            <th>Оборудование</th>
            <th>Дата начала</th>
            <th>Дата окончания</th>
            <th>Статус</th>
            <th>Итоговая стоимость</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?php
                    $stmt = $pdo->prepare("SELECT name FROM equipment WHERE id = ?");
                    $stmt->execute([$booking['equipment_id']]);
                    $equipment = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo $equipment['name'];
                ?></td>
                <td><?php echo $booking['start_date']; ?></td>
                <td><?php echo $booking['end_date']; ?></td>
                <td><?php echo $booking['status']; ?></td>
                <td><?php echo $booking['total_price']; ?> руб.</td>
                <td>
                    <?php if ($booking['status'] == 'В обработке'): ?>
                        <a href="cancel_booking.php?id=<?php echo $booking['id']; ?>">Отменить</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>У вас нет активных бронирований.</p>
<?php endif; ?>

<?php include('templates/footer.php'); ?>
