<?php
session_start();
include('../assets/db.php');

echo '<link rel="stylesheet" href="/styles/admin.css">';

// Получаем список категорий
$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id = $_POST['category_id'] ?: null;
    $description = trim($_POST['description']);
    $price_per_day = floatval($_POST['price_per_day']);
    $availability = intval($_POST['availability']);
    $image_path = null;

    // Валидация
    if ($name === '' || $price_per_day <= 0) {
        $errors[] = "Пожалуйста, заполните все обязательные поля корректно.";
    }

    // Загрузка изображения
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('img_') . '.' . $ext;
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image_path = '/uploads/' . $filename;
        } else {
            $errors[] = "Не удалось загрузить изображение.";
        }
    }

    // Сохраняем в БД
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO equipment (name, category_id, description, price_per_day, availability, image_path) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category_id, $description, $price_per_day, $availability, $image_path]);

        header("Location: equipment.php");
        exit;
    }
}

echo '<link rel="stylesheet" href="/styles/admin.css">';
?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

<main>
    <h1 class="text-center">Добавить оборудование</h1>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="admin-form">
        <label for="name">Название *</label>
        <input type="text" name="name" id="name" required>

        <label for="category_id">Категория</label>
        <select name="category_id" id="category_id">
            <option value="">Без категории</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="description">Описание</label>
        <textarea name="description" id="description" rows="4"></textarea>

        <label for="price_per_day">Цена за день (₽) *</label>
        <input type="number" name="price_per_day" id="price_per_day" step="0.01" required>

        <label for="availability">Количество в наличии *</label>
        <input type="number" name="availability" id="availability" min="0" required>

        <label for="image">Изображение</label>
        <input type="file" name="image" id="image" accept="image/*">

        <input type="submit" value="Добавить оборудование">
        <a href="/admin/equipment.php" class="cancel-button">Отмена</a>
    </form>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
