<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/db.php');

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: /login.php');
    exit;
}

echo '<link rel="stylesheet" href="/styles/admin.css">';

// Получаем все бронирования
$stmt = $pdo->prepare("SELECT bookings.*, users.name AS user_name, equipment.name AS equipment_name 
                       FROM bookings 
                       JOIN users ON bookings.user_id = users.id
                       JOIN equipment ON bookings.equipment_id = equipment.id");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php');
?>

<main>
    <div class="admin-header">
        <?php
        $back_url = '/admin/panel.php';
        include($_SERVER['DOCUMENT_ROOT'] . '/templates/back_button.php');
        ?>
        <h1>Управление бронированиями</h1>
    </div>

    <div class="main-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Пользователь</th>
                    <th>Оборудование</th>
                    <th>Дата начала</th>
                    <th>Дата окончания</th>
                    <th>Статус</th>
                    <th>Итоговая стоимость</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['equipment_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($booking['end_date']); ?></td>
                        <td><?php echo htmlspecialchars($booking['status']); ?></td>
                        <td><?php echo htmlspecialchars($booking['total_price']); ?> руб.</td>
                        <td>
                            <a href="view_booking.php?id=<?php echo $booking['id']; ?>">Просмотреть</a>
                            <?php if ($booking['status'] == 'В обработке'): ?>
                                <a href="cancel_booking.php?id=<?php echo $booking['id']; ?>"
                                    onclick="return confirm('Вы уверены?')">Отменить</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>