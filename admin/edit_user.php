<?php
session_start();
include('../assets/db.php');

// Проверка авторизации и роли администратора
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

echo '<link rel="stylesheet" href="/styles/admin.css">';

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
    $message = "Пользователь не найден.";
    $message_type = "error";  // Тип сообщения: ошибка
} else {
    // Обработка формы
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name  = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $role  = $_POST['role'];

        // Проверка роли
        if ($role !== 'user' && $role !== 'admin') {
            $message = "Некорректное значение роли.";
            $message_type = "error";  // Тип сообщения: ошибка
        }

        // Дополнительная проверка, чтобы администратор не мог изменить свою роль на "user"
        elseif ($_SESSION['user_id'] == $user_id && $role == 'user') {
            $message = "Вы не можете изменить свою роль на пользователя.";
            $message_type = "error";  // Тип сообщения: ошибка
        }

        // Обновление данных пользователя
        else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ? WHERE id = ?");
            $stmt->execute([$name, $email, $phone, $role, $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $message = "Данные пользователя успешно обновлены.";
            $message_type = "success";  // Тип сообщения: успех
                
        }
    }
}
?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>
<link rel="stylesheet" href="/styles/admin.css">

<main>
    <h1 class="text-center">Редактировать пользователя</h1>

    <?php if (isset($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

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
        <a href="/admin/users.php" class="cancel-button">Отмена</a>
    </form>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
