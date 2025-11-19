<header>
    <nav class="nav-wrapper">

        <div class="nav-left">
            <a href="/index.php">Аренда оборудования</a>
        </div>

        <div class="nav-center">
            <ul>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>

                    <li><a href="/admin/users.php">Пользователи</a></li>
                    <li><a href="/admin/equipment.php">Оборудование</a></li>
                    <li><a href="/admin/bookings.php">Бронирования</a></li>
                    <li><a href="/admin/categories.php">Категории оборудования</a></li>

                <?php else: ?>

                    <li>
                        <a href="/index.php">
                            <img src="/assets/catalogue.png" class="menu-icon">Каталог
                        </a>
                    </li>

                <?php endif; ?>
            </ul>
        </div>

        <div class="nav-right">
            <ul>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>

                    <li><a href="/logout.php">Выход</a></li>

                <?php else: ?>

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <li class="dropdown">
                            <a href="/account/account.php">
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

                        <li>
                            <a href="/login.php">
                                <img src="/assets/enter.png" class="menu-icon">Вход
                            </a>
                        </li>

                    <?php endif; ?>

                <?php endif; ?>
            </ul>
        </div>

    </nav>
</header>