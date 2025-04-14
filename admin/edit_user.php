<?php
// edit_user.php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

$page_title = 'Редактировать пользователя';
include('templates/header.php');

$user_id = $_GET['id'];

// Получаем информацию о пользователе
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Пользователь не найден.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    // Обновляем информацию о пользователе
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ? WHERE id = ?");
    $stmt->execute([$name, $email, $phone, $role, $user_id]);

    header('Location: panel.php');
}
?>

<h1>Редактировать пользователя</h1>

<form method="POST">
    <label for="name">Имя</label>
    <input type="text" name="name" id="name" value="<?php echo $user['name']; ?>" required>

    <label for="email">Email</label>
    <input type="email" name="email" id="email" value="<?php echo $user['email']; ?>" required>

    <label for="phone">Телефон</label>
    <input type="text" name="phone" id="phone" value="<?php echo $user['phone']; ?>" required>

    <label for="role">Роль</label>
    <select name="role" id="role">
        <option value="client" <?php echo $user['role'] == 'client' ? 'selected' : ''; ?>>Клиент</option>
        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Администратор</option>
    </select>

    <button type="submit">Сохранить изменения</button>
</form>

<?php include('templates/footer.php'); ?>
