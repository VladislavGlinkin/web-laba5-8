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
$query = "SELECT Responses.response_date
          FROM Responses 
          WHERE Responses.vacancy_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $vacancy_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Создаем ассоциативный массив для подсчета откликов по датам
$responseCountsByDate = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Получаем дату отклика
    $responseDate = date('Y-m-d', strtotime($row['response_date']));
    
    // Увеличиваем счетчик откликов для данной даты
    if (isset($responseCountsByDate[$responseDate])) {
        $responseCountsByDate[$responseDate]++;
    } else {
        $responseCountsByDate[$responseDate] = 1;
    }
}

// Преобразуем данные в формат, подходящий для построения графика
$responseDates = array_keys($responseCountsByDate);
$responseCounts = array_values($responseCountsByDate);

// Закрываем соединение с базой данных
mysqli_stmt_close($stmt);
mysqli_close($connection);
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
        // Подключаемся к базе данных
        include 'db_connect.php';

        // Запрос к базе данных для получения информации о резюме
        $resumeQuery = "SELECT Resume.full_name, Resume.skills, Resume.experience, User.email
                        FROM Responses 
                        JOIN Resume ON Responses.user_id = Resume.user_id
                        JOIN User ON Responses.user_id = User.id
                        WHERE Responses.vacancy_id = ?";
        $stmt = mysqli_prepare($connection, $resumeQuery);
        mysqli_stmt_bind_param($stmt, "i", $vacancy_id);
        mysqli_stmt_execute($stmt);
        $resumeResult = mysqli_stmt_get_result($stmt);

        // Выводим информацию о резюме
        while ($row = mysqli_fetch_assoc($resumeResult)) {
            echo '<div class="response">';
            echo '<p>Имя: ' . $row['full_name'] . '</p>';
            echo '<p>Электронная почта: <a href="mailto:' . $row['email'] . '">' . $row['email'] . '</a></p>';
            echo '<p>Навыки: ' . $row['skills'] . '</p>';
            echo '<p>Опыт: ' . $row['experience'] . '</p>';
            echo '</div>';
        }

        // Закрываем соединение с базой данных
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
        ?>
    </main>
    <canvas id="responseChart" width="800" height="400"></canvas>
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('responseChart').getContext('2d');

        var responseChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($responseDates); ?>,
                datasets: [{
                    label: 'Отклики на вакансию',
                    data: <?php echo json_encode($responseCounts); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    });
</script>
</html>
