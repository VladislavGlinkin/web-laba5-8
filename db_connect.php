<?php
// Параметры подключения к базе данных
$host = 'localhost'; // Имя хоста базы данных
$username = 'root'; // Имя пользователя базы данных
$password = '1'; // Пароль для доступа к базе данных
$database = 'head'; // Имя базы данных

// Подключение к базе данных
$connection = mysqli_connect($host, $username, $password, $database);

// Проверка подключения
if (!$connection) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}
?>
