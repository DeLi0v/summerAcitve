<?php
// logout.php
session_start();
session_unset(); // удаляет все переменные сессии
session_destroy(); // уничтожает сессию

header('Location: index.php'); // перенаправляем на главную
exit;
