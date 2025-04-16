<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/db.php');

echo '<link rel="stylesheet" href="/styles/register.css">';

$page_title = 'Регистрация';
include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php');

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];
    $errors   = [];

    // Простая валидация
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $errors[] = 'Пожалуйста, заполните все поля.';
    }

    // Проверка уникальности email и телефона
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$email, $phone]);
    if ($stmt->fetch()) {
        $errors[] = 'Пользователь с таким email или телефоном уже существует.';
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $password_hash]);

        // После регистрации — вход и переход в личный кабинет
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['role'] = 'client';
        header('Location: account/account.php');
        exit;
    }
}
?>

<h1>Регистрация</h1>

<?php if (!empty($errors)): ?>
    <div class="error">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

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

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
