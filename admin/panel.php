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

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/back_button.php'); ?>

    <div class="admin-sections">
        <a href="users.php">
            <div class="admin-section">
                <h2>Пользователи</h2>
                <!-- Управление пользователями -->
            </div>
        </a>

        <a href="equipment.php">
            <div class="admin-section">
                <h2>Оборудование</h2>
                <!-- Управление оборудованием -->
            </div>
        </a>

        <a href="bookings.php">
            <div class="admin-section">
                <h2>Бронирования</h2>
                <!-- Просмотр бронирований -->
            </div>
        </a>
        <a href="categories.php">
            <div class="admin-section">
                <h2>Категории оборудования</h2>
            </div>
        </a>
    </div>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>