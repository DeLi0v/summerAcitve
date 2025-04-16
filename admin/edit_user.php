<?php
session_start();
include('../assets/db.php');

// Проверка авторизации и роли администратора
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

// Получение ID пользователя из URL
if (!isset($_GET['id'])) {
    header('Location: admin_users.php');
    exit;
}

$user_id = $_GET['id'];

// Получение текущих данных пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Пользователь не найден.";
    exit;
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role  = $_POST['role'];

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ? WHERE id = ?");
    $stmt->execute([$name, $email, $phone, $role, $user_id]);

    header('Location: admin_users.php');
    exit;
}
?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>
<link rel="stylesheet" href="/styles/admin.css">

<main>
    <h1>Редактировать пользователя</h1>

    <form method="POST" class="admin-form">
        <label>Имя</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Телефон</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>

        <label>Роль</label>
        <select name="role" required>
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Пользователь</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Администратор</option>
        </select>

        <button type="submit">Сохранить</button>
    </form>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
