<!-- templates/header.php -->
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($page_title) ? $page_title : 'Аренда оборудования'; ?>
    </title>
    <link rel="stylesheet" href="/styles/main.css">
</head>

<body>
    <header>
        <div class="logo">
            <a href="/index.php">Аренда оборудования</a>
        </div>
        <nav>
            <ul>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>

                    <!-- Админ-навигация -->
                    <li><a href="/admin/users.php"><img src="/assets/users.png" alt="" class="menu-icon">Пользователи</a></li>
                    <li><a href="/admin/equipment.php"><img src="/assets/equipment.png" alt="" class="menu-icon">Оборудование</a></li>
                    <li><a href="/admin/bookings.php"><img src="/assets/bookings.png" alt="" class="menu-icon">Бронирования</a></li>
                    <li><a href="/admin/categories.php"><img src="/assets/categories.png" alt="" class="menu-icon">Категории оборудования</a></li>
                    <li style="margin-left: 25px;"><a href="/logout.php"><img src="/assets/logout.png" alt="" class="menu-icon">Выход</a></li>

                <?php else: ?>
                
                    <!-- Обычная навигация для пользователей -->
                    <li><a href="/index.php"><img src="/assets/catalogue.png" alt="Каталог" class="menu-icon">Каталог</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="/account/account.php"><img src="/assets/account.png" alt="Личный кабинет" class="menu-icon">Личный кабинет</a></li>
                        <li style="margin-left: 25px;"><a href="/logout.php"><img src="/assets/logout.png" alt="Выход" class="menu-icon">Выход</a></li>
                    <?php else: ?>
                        <li><a href="/login.php"><img src="/assets/enter.png" alt="Вход" class="menu-icon">Вход</a></li>
                    <?php endif; ?>

                <?php endif; ?>
            </ul>
        </nav>
    </header>
