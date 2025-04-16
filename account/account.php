<?php
session_start();
include('../assets/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем информацию о пользователе
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Получаем все бронирования текущего пользователя
$stmt = $pdo->prepare("SELECT bookings.*, equipment.name AS equipment_name FROM bookings
                       JOIN equipment ON bookings.equipment_id = equipment.id
                       WHERE bookings.user_id = ?");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

<main>
    <h1>Личный кабинет</h1>

    <div class="dashboard-container">
        <!-- Левая часть: бронирования -->
        <div class="dashboard-content">
            <h2>Мои бронирования</h2>
            <?php if (count($bookings) > 0): ?>
                <table>
                    <thead>
                        <tr>
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
                                <td><?php echo htmlspecialchars($booking['equipment_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($booking['end_date']); ?></td>
                                <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                <td><?php echo htmlspecialchars($booking['total_price']); ?> руб.</td>
                                <td>
                                    <?php if ($booking['status'] == 'В обработке'): ?>
                                        <a href="cancel_booking.php?id=<?php echo $booking['id']; ?>">Отменить</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>У вас нет активных бронирований.</p>
            <?php endif; ?>
        </div>

        <!-- Правая часть: информация о пользователе -->
        <div class="dashboard-info">
            <h3>Информация о пользователе</h3>
            <p><strong>Имя:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Телефон:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>

            <p style="margin-top: 10px;">
                <a href="/account/edit_profile.php">Изменить профиль</a>
            </p>
        </div>
    </div>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
