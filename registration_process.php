<?php
include 'db_connect.php';

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $username = $_POST['reg-username'];
    $email = $_POST['reg-email'];
    $password = $_POST['reg-password'];
    $role = $_POST['role'];

    // Проверка на уникальность имени пользователя и email
    $check_query = "SELECT * FROM User WHERE name='$username' OR email='$email'";
    $check_result = mysqli_query($connection, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $response['success'] = false;
        $response['message'] = "Пользователь с таким именем пользователя или email уже существует";
    } else {
        // Хэширование пароля перед сохранением в базе данных
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Добавление данных в базу данных
        $query = "INSERT INTO User (name, email, password, role) VALUES ('$username', '$email', '$hashed_password', '$role')";
        $result = mysqli_query($connection, $query);

        if ($result) {
            // Сохраняем роль пользователя в сессии
            session_start();
            $_SESSION['user_id'] = mysqli_insert_id($connection);
            $_SESSION['role'] = $role;
            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['message'] = "Ошибка при регистрации: " . mysqli_error($connection);
        }
    }
} else {
    $response['success'] = false;
    $response['message'] = "Ошибка: запрос не был отправлен методом POST";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
