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
        <nav class="nav-wrapper">
            <div class="nav-left logo">
                <a href="/index.php">Аренда оборудования</a>
            </div>
            <div class="nav-center">
                <ul>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>

                        <!-- Админ-навигация -->
                        <li><a href="/admin/users.php">Пользователи</a></li>
                        <li><a href="/admin/equipment.php">Оборудование</a></li>
                        <li><a href="/admin/bookings.php">Бронирования</a></li>
                        <li><a href="/admin/categories.php">Категории оборудования</a></li>
                        <li><a href="/logout.php">Выход</a></li>

                    <?php else: ?>

                        <!-- обычная навигация -->
                        <li><a href="/index.php"><img src="/assets/catalogue.png" class="menu-icon">Каталог</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>

                            <li class="dropdown">
                                <a href="/account/account.php" class="dropdown-toggle">
                                    <img src="/assets/account.png" class="menu-icon">Личный кабинет
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="/logout.php">
                                            <img src="/assets/logout.png" class="menu-icon">Выход
                                        </a>
                                    </li>
                                </ul>
                            </li>

                        <?php else: ?>

                            <li><a href="/login.php"><img src="/assets/enter.png" class="menu-icon">Вход</a></li>

                        <?php endif; ?>

                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>