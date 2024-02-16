<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создать вакансию</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключение CSS-файла -->
</head>
<body>
    <header>
        <h1>Создать вакансию</h1>
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
        <section class="create-vacancy">
            <h2>Заполните форму для создания вакансии</h2>
            <form action="create_vacancy_process.php" method="post">
                <label for="title">Название вакансии:</label><br>
                <input type="text" id="title" name="title" required><br>
                
                <label for="description">Описание вакансии:</label><br>
                <textarea id="description" name="description" rows="4" cols="100" required></textarea><br>
                
                <button type="submit">Создать вакансию</button>
            </form>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Наш сайт по поиску работы</p>
    </footer>
</body>
</html>
