<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/assets/db.php');

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: /login.php');
    exit;
}

echo '<link rel="stylesheet" href="/styles/admin.css">';

// Получаем все категории
$stmt = $pdo->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php');
?>

<main>

    <div class="admin-header">
        <?php
        $back_url = '/admin/panel.php';
        include($_SERVER['DOCUMENT_ROOT'] . '/templates/back_button.php');
        ?>
        <h1>Управление категориями</h1>
    </div>

    <div class="admin-actions">
        <a href="/admin/add_category.php">Добавить категорию</a>
    </div>

    <div class="main-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td>
                            <a href="edit_category.php?id=<?php echo $category['id']; ?>">Редактировать</a> |
                            <a href="delete_category.php?id=<?php echo $category['id']; ?>"
                                onclick="return confirm('Вы уверены?')">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>