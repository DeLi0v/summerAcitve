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
    <div class="admin-header">
        <?php
        $back_url = '/index.php';
        include($_SERVER['DOCUMENT_ROOT'] . '/templates/back_button.php');
        ?>
        <h1>Административная панель</h1>
    </div>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
