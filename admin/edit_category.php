<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/db.php');

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: /login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: /admin/categories.php');
    exit;
}

$category_id = $_GET['id'];

// Получаем информацию о категории
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header('Location: /admin/categories.php');
    exit;
}

// Обработка формы редактирования категории
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];

    // Обновляем категорию в базе
    $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
    $stmt->execute([$name, $category_id]);

    $_SESSION['success'] = "Категория успешно обновлена!";
    header('Location: /admin/categories.php');
    exit;
}

include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php');
?>

<main>
    <h1>Редактировать категорию</h1>

    <form method="POST">
        <label for="name">Название категории</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>

        <input type="submit" value="Сохранить изменения">
    </form>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
