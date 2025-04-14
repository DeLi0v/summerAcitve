<?php
// register.php
include('includes/db.php');
$page_title = 'Регистрация';
include('templates/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $password]);

    header('Location: login.php');
}

?>
<h1>Регистрация</h1>
<form method="POST">
    <label for="name">Имя:</label><br>
    <input type="text" id="name" name="name" required><br><br>

    <label for="email">Электронная почта:</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="phone">Телефон:</label><br>
    <input type="text" id="phone" name="phone" required><br><br>

    <label for="password">Пароль:</label><br>
    <input type="password" id="password" name="password" required><br><br>

    <input type="submit" value="Зарегистрироваться">
</form>

<?php include('templates/footer.php'); ?>
