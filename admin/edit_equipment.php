<?php
session_start();
include('../assets/db.php');

// Проверка авторизации и роли администратора
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

echo '<link rel="stylesheet" href="/styles/admin.css">';

// Получаем ID оборудования
if (!isset($_GET['id'])) {
    header("Location: admin_equipment.php");
    exit;
}

$equipment_id = (int)$_GET['id'];

// Получаем список категорий
$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем текущее оборудование
$stmt = $pdo->prepare("SELECT * FROM equipment WHERE id = ?");
$stmt->execute([$equipment_id]);
$equipment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipment) {
    echo "Оборудование не найдено.";
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id = $_POST['category_id'] ?: null;
    $description = trim($_POST['description']);
    $price_per_day = floatval($_POST['price_per_day']);
    $availability = intval($_POST['availability']);
    $image_path = $equipment['image_path'];

    if ($name === '' || $price_per_day <= 0) {
        $errors[] = "Пожалуйста, заполните все обязательные поля корректно.";
    }

    // Загрузка нового изображения
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

    // Обновление в БД
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE equipment SET name = ?, category_id = ?, description = ?, price_per_day = ?, availability = ?, image_path = ? WHERE id = ?");
        $stmt->execute([$name, $category_id, $description, $price_per_day, $availability, $image_path, $equipment_id]);

        header("Location: admin_equipment.php");
        exit;
    }
}

echo '<link rel="stylesheet" href="/styles/admin.css">';
?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

<main>
    <h1 class="text-center">Редактировать оборудование</h1>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="admin-form">
        <label for="name">Название *</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($equipment['name']) ?>" required>

        <label for="category_id">Категория</label>
        <select name="category_id" id="category_id">
            <option value="">Без категории</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= ($equipment['category_id'] == $category['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="description">Описание</label>
        <textarea name="description" id="description" rows="4"><?= htmlspecialchars($equipment['description']) ?></textarea>

        <label for="price_per_day">Цена за день (₽) *</label>
        <input type="number" name="price_per_day" id="price_per_day" value="<?= $equipment['price_per_day'] ?>" step="0.01" required>

        <label for="availability">Количество в наличии *</label>
        <input type="number" name="availability" id="availability" value="<?= $equipment['availability'] ?>" min="0" required>

        <?php if ($equipment['image_path']): ?>
            <p>Текущее изображение:</p>
            <img src="<?= $equipment['image_path'] ?>" alt="Изображение" style="max-width: 200px; display: block; margin-bottom: 10px;">
        <?php endif; ?>

        <label for="image">Новое изображение (если нужно заменить)</label>
        <input type="file" name="image" id="image" accept="image/*">

        <input type="submit" value="Сохранить изменения">
    </form>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
