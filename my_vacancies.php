<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои вакансии</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключение CSS-файла -->
</head>
<body>
    <?php
        session_start();

        // Проверяем, авторизован ли пользователь
        if (!isset($_SESSION['user_id'])) {
            // Если не авторизован, перенаправляем на страницу входа
            header("Location: login.php");
            exit();
        }
    ?>

    <header>
        <h1>Мои вакансии</h1>
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
    <div class="vacancies-container">
    <?php

    include 'db_connect.php';

    // Получаем ID работодателя
    $employer_id = $_SESSION['user_id'];

    // Запрос к базе данных для получения вакансий работодателя
    $query = "SELECT * FROM Vacancy WHERE employer_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $employer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Проверяем, было ли удаление вакансии и выводим уведомление
    if (isset($_GET['deleted'])) {
        echo '<div class="notification">Вакансия успешно удалена!</div>';
    }

    // Выводим вакансии работодателя
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="vacancy">';
            echo "<h2>Название: " . $row['title'] . "</h2>";
            echo "<hr>"; // Разделяющая линия
            echo "<p>Описание: " . $row['description'] . "</p>";
            // Добавляем форму для кнопки удаления
            echo '<form class="delete-form">';
            echo '<input type="hidden" class="vacancy_id" value="' . $row['id'] . '">';
            echo '<a href="responses.php?vacancy_id=' . $row['id'] . '">Просмотреть отклики</a>';
            echo '<input type="submit" class="delete-button" value="Удалить вакансию">';
            echo '</form>';
            echo '</div>';
        }
    } else {
        echo "У вас пока нет созданных вакансий.";
    }

    // Закрываем соединение с базой данных
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    ?>

    </div>
</main>

    <div id="notification" class="notification">
    Вакансия успешно удалена!
    </div>

    <script>
    // Обработчик события отправки формы удаления
    document.querySelectorAll('.delete-form').forEach(item => {
        item.addEventListener('submit', event => {
            event.preventDefault(); // Предотвращаем отправку формы
            var vacancyId = item.querySelector('.vacancy_id').value;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_vacancy.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        var response = xhr.responseText;
                        if (response === "success") {
                            // Удаление вакансии из DOM-дерева
                            item.closest('.vacancy').remove();
                            showNotification("Вакансия успешно удалена!");
                            // Перезагрузка страницы
                            location.reload();
                        } else {
                            console.error("Ошибка при удалении вакансии.");
                        }
                    } else {
                        console.error("Ошибка при удалении вакансии. Статус: " + xhr.status);
                    }
                }
            };
            xhr.send("delete_vacancy=true&vacancy_id=" + encodeURIComponent(vacancyId));
        });
    });

    // Отображение уведомления
    function showNotification(message) {
        var notification = document.getElementById("notification");
        notification.innerText = message;
        notification.style.display = "block";
        // Скрытие уведомления через 3 секунды
        setTimeout(function() {
            notification.style.display = "none";
        }, 3000);
    }
</script>


</body>
</html>
