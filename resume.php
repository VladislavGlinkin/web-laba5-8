<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    // Если не авторизован, перенаправляем на страницу входа
    header("Location: login.php");
    exit();
}

// Подключаемся к базе данных
include 'db_connect.php';

// Получаем ID пользователя
$user_id = $_SESSION['user_id'];

// Проверяем, была ли отправлена форма с данными о резюме
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы
    $full_name = $_POST['full_name'];
    $skills = $_POST['skills'];
    $experience = $_POST['experience'];

    // Проверяем, существует ли уже запись о резюме в базе данных
    $query = "SELECT * FROM Resume WHERE user_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Если запись о резюме уже существует, обновляем информацию
        $updateQuery = "UPDATE Resume SET full_name=?, skills=?, experience=? WHERE user_id=?";
        $updateStmt = mysqli_prepare($connection, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "sssi", $full_name, $skills, $experience, $user_id);

        if (mysqli_stmt_execute($updateStmt)) {
            $successMessage = "Резюме успешно обновлено.";
        } else {
            $errorMessage = "Ошибка при обновлении резюме: " . mysqli_error($connection);
        }

        mysqli_stmt_close($updateStmt);
    } else {
        // Если запись о резюме не существует, создаем новую запись
        $insertQuery = "INSERT INTO Resume (user_id, full_name, skills, experience) VALUES (?, ?, ?, ?)";
        $insertStmt = mysqli_prepare($connection, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "isss", $user_id, $full_name, $skills, $experience);

        if (mysqli_stmt_execute($insertStmt)) {
            $successMessage = "Резюме успешно добавлено.";
        } else {
            $errorMessage = "Ошибка при добавлении резюме: " . mysqli_error($connection);
        }

        mysqli_stmt_close($insertStmt);
    }

    mysqli_stmt_close($stmt);
}

// Получаем данные о резюме из базы данных
$query = "SELECT * FROM Resume WHERE user_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $full_name = $row['full_name'];
    $skills = $row['skills'];
    $experience = $row['experience'];
}

mysqli_stmt_close($stmt);
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Моё резюме</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключение CSS-файла -->
</head>
<body>
    <header>
        <Uh1>Моё резюме</h1>
    </header>
    <nav>
        <ul>
            <li><a href="main.php">Главная</a></li>
            <li><a href="all_vacancies.php">Вакансии</a></li>
            <li><a href="resume.php">Моё резюме</a></li>
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
        <section>
            <h2>Информация о вашем резюме</h2>
            <?php
            if (isset($successMessage)) {
                echo '<div class="success-message">' . $successMessage . '</div>';
            } elseif (isset($errorMessage)) {
                echo '<div class="error-message">' . $errorMessage . '</div>';
            }
            ?>
            <form method="post">
                <div>
                    <label for="full_name">Имя и фамилия:</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo $full_name; ?>" required>
                </div>
                <div>
                    <label for="skills">Навыки:</label>
                    <textarea id="skills" name="skills" required><?php echo $skills; ?></textarea>
                </div>
                <div>
                    <label for="experience">Опыт работы:</label>
                    <textarea id="experience" name="experience" required><?php echo $experience; ?></textarea>
                </div>
                <button type="submit">Сохранить</button>
            </form>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Наш сайт по поиску работы</p>
    </footer>
</body>
</html>
