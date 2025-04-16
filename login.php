<?php
// login.php
session_start();
include('assets/db.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header('Location: ' . $_SERVER['DOCUMENT_ROOT'] .  'admin/panel.php');
        } else {
            header('Location: ' . $_SERVER['DOCUMENT_ROOT'] .  '/account/account.php');
        }
        exit;
    } else {
        $error = 'Неверный email или пароль.';
    }
}

$page_title = 'Вход';
include('templates/header.php');
?>

<h1>Вход</h1>

<div class="form-container">
    <?php if ($error): ?>
        <div class="error" style="text-align: center; max-width: 500px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password" required>

        <input type="submit" value="Войти">
    </form>

    <div class="form-link">
        Нет аккаунта? <a href="/register.php">Зарегистрируйтесь</a>
    </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . 'templates/footer.php'); ?>
