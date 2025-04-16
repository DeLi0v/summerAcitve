<!-- templates/header.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Аренда оборудования'; ?></title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php">Аренда оборудования</a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Каталог</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="/panel.php">Личный кабинет</a></li>
                    <li><a href="logout.php">Выход</a></li>
                <?php else: ?>
                    <li><a href="login.php">Вход</a></li>
                    <li><a href="register.php">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
