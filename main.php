<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключение CSS-файла -->
</head>
<body>
    <header>
        <h1>Добро пожаловать на наш сайт по поиску работы</h1>
    </header>
    <nav>
        <ul>
            <li><a href="main.php">Главная</a></li>
            <li><a href="all_vacancies.php">Вакансии</a></li>
            <?php
                // Проверяем, авторизован ли пользователь
                if (isset($_SESSION['user_id'])) {
                    // Проверяем роль пользователя
                    if ($_SESSION['role'] == 'applicant') { // Исправлено: проверяем роль соискателя
                        // Если пользователь соискатель, отображаем соответствующие пункты меню
                        echo '<li><a href="resume.php">Моё резюме</a></li>';
                    } elseif ($_SESSION['role'] == 'employer') { // Оставляем проверку для работодателя
                        // Если пользователь работодатель, отображаем соответствующие пункты меню
                        echo '<li><a href="create_vacancy.php">Создать вакансию</a></li>';
                        echo '<li><a href="my_vacancies.php">Мои вакансии</a></li>';
                        echo '<li><a href="company_info.php">Информация о компании</a></li>';
                    }
                    // Отображаем ссылку на выход для всех авторизованных пользователей
                    echo '<li><a href="logout.php">Выход</a></li>';
                } else {
                    // Иначе отображаем ссылку на регистрацию для неавторизованных пользователей
                    echo '<li><a href="registration.php">Вход/Регистрация</a></li>';
                }
            ?>
        </ul>
    </nav>
    <main>
        <section class="features">
            <h2>Особенности сайта</h2>
            <div class="feature">
                <h3>Большой выбор вакансий</h3>
                <p>Найдите работу мечты из множества доступных вакансий.</p>
            </div>
            <div class="feature">
                <h3>Простой поиск</h3>
                <p>Легко находите интересующие вас вакансии по различным критериям.</p>
            </div>
            <div class="feature">
                <h3>Удобный интерфейс</h3>
                <p>Интуитивно понятный интерфейс сайта поможет вам быстро найти нужную информацию.</p>
            </div>
        </section>
        <section class="about">
            <h2>О нашем сайте</h2>
            <p>Наш сайт предлагает удобный сервис для поиска работы. Мы помогаем работодателям находить квалифицированных сотрудников и помогаем работникам найти подходящие вакансии.</p>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Наш сайт по поиску работы</p>
    </footer>
</body>
</html>
