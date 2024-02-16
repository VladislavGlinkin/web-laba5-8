<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вакансии</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключение CSS-файла -->
</head>
<body>
    <header>
        <h1>Все вакансии</h1>
    </header>
    <nav>
        <ul>
            <li><a href="main.php">Главная</a></li>
            <li><a href="all_vacancies.php">Вакансии</a></li>
            <?php
            // Проверяем, авторизован ли пользователь
            if (isset($_SESSION['user_id'])) {
                // Проверяем роль пользователя
                if ($_SESSION['role'] == 'employer') {
                    // Если пользователь работодатель, отображаем пункт меню "Создать вакансию"
                    echo '<li><a href="create_vacancy.php">Создать вакансию</a></li>';
                    echo '<li><a href="my_vacancies.php">Мои вакансии</a></li>';
                    echo '<li><a href="company_info.php">Информация о компании</a></li>';
                }
                // Отображаем ссылку на выход
                echo '<li><a href="logout.php">Выход</a></li>';
            } else {
                // Иначе отображаем ссылку на регистрацию
                echo '<li><a href="registration.php">Вход/Регистрация</a></li>';
            }
            ?>
        </ul>
    </nav>
    <main>
        <div class="search-container">
            <form method="GET" action="all_vacancies.php">
                <input type="text" name="query" placeholder="Введите ключевое слово">
                <button type="submit">Поиск</button>
            </form>
        </div>
        <div class="vacancies-container">
            <?php
            include 'db_connect.php';

            // Проверяем, был ли отправлен запрос поиска
            if (isset($_GET['query'])) {
                $search_query = $_GET['query'];
                // Запрос к базе данных для поиска вакансий
                $query = "SELECT v.id, v.title, v.description, v.created_at, c.name AS company_name 
                          FROM Vacancy AS v 
                          LEFT JOIN CompanyInfo AS c ON v.employer_id = c.employer_id 
                          WHERE v.title LIKE '%$search_query%' OR v.description LIKE '%$search_query%'";
            } else {
                // Если запроса поиска нет, выбираем все вакансии
                $query = "SELECT v.id, v.title, v.description, v.created_at, c.name AS company_name 
                          FROM Vacancy AS v 
                          LEFT JOIN CompanyInfo AS c ON v.employer_id = c.employer_id";
            }

            $result = mysqli_query($connection, $query);

            // Выводим результаты поиска или все вакансии
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="vacancy">';
                    echo "<h2>Название: " . $row['title'] . "</h2>";
                    echo "<p>Опубликовано: " . $row['created_at'] . "</p>";
                    echo "<p>Опубликовал: " . $row['company_name'] . "</p>";
                    echo '<button onclick="toggleDescription(this)">Подробнее</button>';
                    echo '<p class="description" style="display:none;">' . $row['description'] . '</p>';
                    // Добавляем кнопку "Откликнуться" с атрибутом data-vacancy-id для хранения ID вакансии
                    // Добавляем кнопку "Откликнуться" только для соискателей
                    if ($_SESSION['role'] == 'applicant') {
                        echo '<button class="apply-btn" data-vacancy-id="' . $row['id'] . '">Откликнуться</button>';
                    }
                    echo '</div>';
                }
            } else {
                echo "Ничего не найдено.";
            }

            // Закрываем соединение с базой данных
            mysqli_close($connection);
            ?>
        </div>
    </main>
    <script>
        function toggleDescription(button) {
            var description = button.nextElementSibling;
            if (description.style.display === "none") {
                description.style.display = "block";
                button.textContent = "Скрыть";
            } else {
                description.style.display = "none";
                button.textContent = "Подробнее";
            }
        }

        // Добавляем обработчик события для кнопок "Откликнуться"
        var applyButtons = document.querySelectorAll('.apply-btn');
        applyButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var vacancyId = button.dataset.vacancyId;
                // Отправляем AJAX запрос на сервер для создания записи об отклике в базе данных
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'handle_response.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            // Обработка успешного ответа от сервера
                            alert('Вы откликнулись на вакансию с ID ' + vacancyId);
                        } else {
                            // Обработка ошибки
                            alert('Произошла ошибка при отправке запроса');
                        }
                    }
                };
                xhr.send('vacancy_id=' + encodeURIComponent(vacancyId));
            });
        });
    </script>
</body>
</html>
