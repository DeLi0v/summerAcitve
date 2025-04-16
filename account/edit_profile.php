<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем информацию о пользователе
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->execute([$name, $email, $phone, $user_id]);

    $_SESSION['success'] = "Профиль успешно обновлён!";
    header('Location: dashboard.php');
    exit;
}
?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

<main>
    <h1>Редактирование профиля</h1>
    
    <form method="POST">
        <label for="name">Имя</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        
        <label for="phone">Телефон</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        
        <input type="submit" value="Сохранить изменения">
    </form>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
