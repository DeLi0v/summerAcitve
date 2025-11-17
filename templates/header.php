<!-- templates/header.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Аренда оборудования'; ?></title>
    <link rel="stylesheet" href="/styles/main.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="/index.php">Аренда оборудования</a>
        </div>
        <nav>
            <ul>
                <li><a href="/index.php"><img src="/assets/catalogue.png" alt="Каталог" class="menu-icon">Каталог</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'admin'):?>
                        <li><a href="/admin/panel.php"><img src="/assets/admin.png" alt="Административная панель" class="menu-icon">Административная панель</a></li>
                    <?php else: ?>
                        <li><a href="/account/account.php"><img src="/assets/account.png" alt="Личный кабинет" class="menu-icon">Личный кабинет</a></li>
                    <?php endif; ?>
                    <li style="margin-left: 25px;"><a href="/logout.php"><img src="/assets/logout.png" alt="Выход" class="menu-icon">Выход</a></li>
                <?php else: ?>
                    <li><a href="/login.php"><img src="/assets/enter.png" alt="Вход" class="menu-icon">Вход</a></li>
                    <!-- <li><a href="/register.php"><img src="/assets/catalogue.png" alt="Каталог" class="menu-icon">Регистрация</a></li> -->
                <?php endif; ?>
            </ul>
        </nav>
    </header>
