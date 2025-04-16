<?php
session_start();
include('../assets/db.php');

// Проверка, является ли пользователь администратором (добавь свою логику при необходимости)
// if (!$_SESSION['is_admin']) { header('Location: /login.php'); exit; }

echo '<link rel="stylesheet" href="/styles/admin.css">';

// Получаем список оборудования с категориями
$stmt = $pdo->query("SELECT equipment.*, categories.name AS category_name 
                     FROM equipment 
                     LEFT JOIN categories ON equipment.category_id = categories.id
                     ORDER BY equipment.created_at DESC");
$equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

<main>
    <h1>Управление оборудованием</h1>

    <div class="admin-actions">
        <a href="add_equipment.php" class="btn">Добавить оборудование</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Название</th>
                <th>Категория</th>
                <th>Цена (руб/день)</th>
                <th>В наличии</th>
                <th>Добавлено</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipment as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['category_name'] ?? 'Без категории'); ?></td>
                    <td><?php echo $item['price_per_day']; ?></td>
                    <td><?php echo $item['availability']; ?></td>
                    <td><?php echo date('d.m.Y', strtotime($item['created_at'])); ?></td>
                    <td>
                        <a href="edit_equipment.php?id=<?php echo $item['id']; ?>">Редактировать</a> |
                        <a href="delete_equipment.php?id=<?php echo $item['id']; ?>" onclick="return confirm('Вы уверены, что хотите удалить это оборудование?');">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>
