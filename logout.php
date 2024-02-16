<?php
session_start();

// Уничтожаем сессию
session_destroy();

// Перенаправляем пользователя на страницу входа или на другую страницу
header("Location: registration.php");
exit;
?>
