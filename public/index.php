<?php
require_once '../config/db.php';
include '../includes/header.php';

echo "<h2>Каталог оборудования</h2>";

$stmt = $pdo->query("SELECT * FROM equipment");
$items = $stmt->fetchAll();

if (count($items) === 0) {
    echo "<p>Оборудование пока недоступно.</p>";
} else {
    echo "<ul>";
    foreach ($items as $item) {
        echo "<li><strong>{$item['name']}</strong> — {$item['description']} ({$item['price_per_day']} руб/день)</li>";
    }
    echo "</ul>";
}

include '../includes/footer.php';