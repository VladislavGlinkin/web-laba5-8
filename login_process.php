<?php
include 'db_connect.php';

session_start();

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $username = $_POST['login-username'];
    $password = $_POST['login-password'];

    // Поиск пользователя в базе данных
    $query = "SELECT * FROM User WHERE name='$username'";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            // Успешный вход
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role']; // Сохраняем роль пользователя в сессии
            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['message'] = "Неверное имя пользователя или пароль";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Неверное имя пользователя или пароль";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Ошибка: запрос не был отправлен методом POST";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
