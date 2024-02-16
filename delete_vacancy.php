<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    // Если не авторизован, отправляем ошибку
    http_response_code(401);
    exit("Ошибка: Пользователь не авторизован.");
}

// Проверяем, был ли отправлен POST-запрос на удаление вакансии
if (isset($_POST['delete_vacancy'])) {
    // Проверяем, был ли передан идентификатор вакансии
    if (isset($_POST['vacancy_id'])) {
        // Подключаемся к базе данных
        include 'db_connect.php';

        // Получаем идентификатор вакансии
        $vacancy_id = $_POST['vacancy_id'];

        // Запрос к базе данных для удаления откликов на вакансию
        $query_delete_responses = "DELETE FROM Responses WHERE vacancy_id = ?";
        $stmt_delete_responses = mysqli_prepare($connection, $query_delete_responses);
        mysqli_stmt_bind_param($stmt_delete_responses, "i", $vacancy_id);
        
        // Удаляем отклики на вакансию
        if (mysqli_stmt_execute($stmt_delete_responses)) {
            // Освобождаем ресурсы
            mysqli_stmt_close($stmt_delete_responses);

            // Запрос к базе данных для удаления самой вакансии
            $query_delete_vacancy = "DELETE FROM Vacancy WHERE id = ?";
            $stmt_delete_vacancy = mysqli_prepare($connection, $query_delete_vacancy);
            mysqli_stmt_bind_param($stmt_delete_vacancy, "i", $vacancy_id);

            // Удаляем вакансию
            if (mysqli_stmt_execute($stmt_delete_vacancy)) {
                // Успешно удалено
                mysqli_stmt_close($stmt_delete_vacancy);
                mysqli_close($connection);
                exit("success"); // Возвращаем строку "success" в случае успешного удаления
            } else {
                // Ошибка при удалении вакансии
                mysqli_stmt_close($stmt_delete_vacancy);
                mysqli_close($connection);
                http_response_code(500);
                exit("Ошибка при удалении вакансии.");
            }
        } else {
            // Ошибка при удалении откликов
            mysqli_stmt_close($stmt_delete_responses);
            mysqli_close($connection);
            http_response_code(500);
            exit("Ошибка при удалении откликов на вакансию.");
        }
    } else {
        // Если идентификатор вакансии не был передан
        http_response_code(400);
        exit("Ошибка: Идентификатор вакансии не был передан.");
    }
} else {
    // Если запрос на удаление вакансии не был отправлен методом POST
    http_response_code(405);
    exit("Ошибка: Неверный метод запроса.");
}
?>
