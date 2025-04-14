<?php
// login.php
session_start();
include('includes/db.php');
$page_title = 'Вход';
include('templates/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
    } else {
        echo "Неверный email или пароль.";
    }
}

?>
<h1>Вход</h1>
<form method="POST">
    <label for="email">Электронная почта:</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">Пароль:</label><br>
    <input type="password" id="password" name="password" required><br><br>

    <input type="submit" value="Войти">
</form>

<?php include('templates/footer.php'); ?>
