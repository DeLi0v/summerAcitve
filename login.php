<?php
// login.php
session_start();
include('assets/db.php');

$error = '';

// Включаем вывод ошибок, чтобы видеть, если что-то не так
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
            header('Location: panel.php');
        } else {
            header('Location: dashboard.php');
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

<?php if ($error): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST">
    <label for="email">Email:</label><br>
    <input type="email" name="email" id="email" required><br><br>

    <label for="password">Пароль:</label><br>
    <input type="password" name="password" id="password" required><br><br>

    <input type="submit" value="Войти">
</form>

<p>Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>

<?php include('templates/footer.php'); ?>
