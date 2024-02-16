<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    // Если не авторизован, перенаправляем на страницу входа
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Получаем данные из формы
$title = $_POST['title'];
$description = $_POST['description'];
$employer_id = $_SESSION['user_id']; // ID работодателя, создающего вакансию

// Подготовленный запрос для вставки данных в таблицу Vacancy
$query = "INSERT INTO Vacancy (employer_id, title, description) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($connection, $query);

// Привязываем параметры к подготовленному запросу
mysqli_stmt_bind_param($stmt, "iss", $employer_id, $title, $description);

// Выполняем запрос
if (mysqli_stmt_execute($stmt)) {
    // Если запрос выполнен успешно, перенаправляем на страницу с сообщением об успешном создании вакансии
    header("Location: my_vacancies.php");
} else {
    // Если произошла ошибка, выводим сообщение об ошибке
    echo "Ошибка при создании вакансии: " . mysqli_error($connection);
}

// Закрываем подготовленное выражение и соединение с базой данных
mysqli_stmt_close($stmt);
mysqli_close($connection);
?>
