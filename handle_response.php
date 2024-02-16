<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Пользователь не авторизован, перенаправляем на страницу входа
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vacancy_id'])) {
    // Получаем ID пользователя и ID вакансии
    $user_id = $_SESSION['user_id'];
    $vacancy_id = $_POST['vacancy_id'];

    // Подключение к базе данных
    include 'db_connect.php';

    // Вставляем запись об отклике в таблицу Responses
    $query = "INSERT INTO Responses (vacancy_id, user_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ii", $vacancy_id, $user_id);
    if (mysqli_stmt_execute($stmt)) {
        // Отправляем успешный ответ обратно на клиент
        http_response_code(200);
        echo "Success";
    } else {
        // Отправляем код ошибки обратно на клиент
        http_response_code(500);
        echo "Error";
    }

    // Закрываем соединение с базой данных
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
} else {
    // Некорректный запрос
    http_response_code(400);
    echo "Bad Request";
}
?>
