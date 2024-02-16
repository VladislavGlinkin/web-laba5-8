<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    // Если не авторизован, перенаправляем на страницу входа
    header("Location: login.php");
    exit();
}

// Проверяем, передан ли параметр vacancy_id
if (!isset($_GET['vacancy_id'])) {
    // Если параметр не передан, перенаправляем на страницу моих вакансий
    header("Location: my_vacancies.php");
    exit();
}

// Получаем ID вакансии из параметра GET
$vacancy_id = $_GET['vacancy_id'];

// Подключаемся к базе данных
include 'db_connect.php';

// Запрос к базе данных для получения откликов для выбранной вакансии, информации из таблиц Resume и User
$query = "SELECT Responses.response_id, Responses.response_date, Resume.full_name, Resume.skills, Resume.experience, User.email
          FROM Responses 
          JOIN Resume ON Responses.user_id = Resume.user_id
          JOIN User ON Responses.user_id = User.id
          WHERE Responses.vacancy_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $vacancy_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отклики на вакансию</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключение CSS-файла -->
</head>
<body>
    <header>
        <h1>Отклики на вакансию</h1>
    </header>
    <nav>
        <ul>
            <li><a href="main.php">Главная</a></li>
            <li><a href="all_vacancies.php">Вакансии</a></li>
            <li><a href="my_vacancies.php">Мои вакансии</a></li>
            <li><a href="company_info.php">Информация о компании</a></li>
            <li><a href="logout.php">Выход</a></li>
        </ul>
    </nav>
    <main>
        <?php
        // Выводим отклики для выбранной вакансии
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="response">';
                echo '<p>Имя: ' . $row['full_name'] . '</p>';
                echo '<p>Электронная почта: <a href="mailto:' . $row['email'] . '">' . $row['email'] . '</a></p>';
                echo '<p>Навыки: ' . $row['skills'] . '</p>';
                echo '<p>Опыт: ' . $row['experience'] . '</p>';
                echo '<p>Дата отклика: ' . $row['response_date'] . '</p>';
                // и т.д.
                echo '</div>';
            }
        } else {
            echo '<p>На эту вакансию пока нет откликов.</p>';
        }
        ?>
    </main>
</body>
</html>

<?php
// Закрываем соединение с базой данных
mysqli_stmt_close($stmt);
mysqli_close($connection);
?>
