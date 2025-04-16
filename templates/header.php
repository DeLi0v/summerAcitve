<!-- templates/header.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Аренда оборудования'; ?></title>
    <link rel="stylesheet" href="/assets/styles.css">
    <style>
        .test2 {
            color: #14fa00;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="/index.php" class="test">Аренда оборудования</a>
        </div>
        <nav>
            <ul>
                <li><a href="/index.php">Каталог</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="#">Личный кабинет</a></li>
                    <li><a href="#">Выход</a></li>
                <?php else: ?>
                    <li><a href="#">Вход</a></li>
                    <li><a href="#">Регистрация</a></li>
                <?php endif; ?>
            </ul>
            <div class="test2">
                <p>test2</p>
            </div>
        </nav>
    </header>
