<?php
// panel.php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

$page_title = 'Админ-панель';
include('templates/header.php');

// Получаем все бронирования
$stmt = $pdo->query("SELECT bookings.*, equipment.name AS equipment_name, users.name AS user_name FROM bookings 
    JOIN equipment ON bookings.equipment_id = equipment.id
    JOIN users ON bookings.user_id = users.id");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем всех пользователей
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем все оборудование
$stmt = $pdo->query("SELECT * FROM equipment");
$equipments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Админ-панель</h1>

<h2>Список бронирований</h2>
<table>
    <tr>
        <th>Оборудование</th>
        <th>Пользователь</th>
        <th>Дата начала</th>
        <th>Дата окончания</th>
        <th>Статус</th>
        <th>Итоговая стоимость</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($bookings as $booking): ?>
        <tr>
            <td><?php echo $booking['equipment_name']; ?></td>
            <td><?php echo $booking['user_name']; ?></td>
            <td><?php echo $booking['start_date']; ?></td>
            <td><?php echo $booking['end_date']; ?></td>
            <td><?php echo $booking['status']; ?></td>
            <td><?php echo $booking['total_price']; ?> руб.</td>
            <td>
                <a href="edit_booking.php?id=<?php echo $booking['id']; ?>">Изменить</a> |
                <a href="delete_booking.php?id=<?php echo $booking['id']; ?>">Удалить</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Список пользователей</h2>
<table>
    <tr>
        <th>Имя</th>
        <th>Email</th>
        <th>Телефон</th>
        <th>Роль</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['name']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td><?php echo $user['phone']; ?></td>
            <td><?php echo $user['role']; ?></td>
            <td>
                <a href="edit_user.php?id=<?php echo $user['id']; ?>">Изменить</a> |
                <a href="delete_user.php?id=<?php echo $user['id']; ?>">Удалить</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Список оборудования</h2>
<table>
    <tr>
        <th>Название</th>
        <th>Категория</th>
        <th>Цена за день</th>
        <th>Доступность</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($equipments as $equipment): ?>
        <tr>
            <td><?php echo $equipment['name']; ?></td>
            <td><?php echo $equipment['category']; ?></td>
            <td><?php echo $equipment['price_per_day']; ?> руб.</td>
            <td><?php echo $equipment['availability']; ?></td>
            <td>
                <a href="edit_equipment.php?id=<?php echo $equipment['id']; ?>">Изменить</a> |
                <a href="delete_equipment.php?id=<?php echo $equipment['id']; ?>">Удалить</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include('templates/footer.php'); ?>
