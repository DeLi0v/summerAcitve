<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/db.php');

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: /login.php');
    exit;
}

echo '<link rel="stylesheet" href="/styles/admin.css">';
?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

<main class="admin-panel">
    <h1>Административная панель</h1>

    <div class="admin-sections">
        <div class="admin-section">
            <h2>Пользователи</h2>
            <a href="users.php">Управление пользователями</a>
        </div>

        <div class="admin-section">
            <h2>Оборудование</h2>
            <a href="equipment.php">Управление оборудованием</a>
        </div>

        <div class="admin-section">
            <h2>Бронирования</h2>
            <a href="bookings.php">Просмотр бронирований</a>
        </div>
    </div>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
