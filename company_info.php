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

// Получаем ID работодателя
$employerId = $_SESSION['user_id'];

// Проверяем, была ли отправлена форма с данными о компании
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы
    $companyName = $_POST['companyName'];
    $companyDescription = $_POST['companyDescription'];

    // Проверяем, есть ли уже запись о компании в базе данных
    $query = "SELECT * FROM CompanyInfo WHERE employer_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $employerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Если запись о компании уже существует, обновляем информацию
        $updateQuery = "UPDATE CompanyInfo SET name=?, description=? WHERE employer_id=?";
        $updateStmt = mysqli_prepare($connection, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "ssi", $companyName, $companyDescription, $employerId);

        if (mysqli_stmt_execute($updateStmt)) {
            $successMessage = "Информация о компании успешно обновлена.";
        } else {
            $errorMessage = "Ошибка при обновлении информации о компании: " . mysqli_error($connection);
        }

        mysqli_stmt_close($updateStmt);
    } else {
        // Если запись о компании не существует, создаем новую запись
        $insertQuery = "INSERT INTO CompanyInfo (name, description, employer_id) VALUES (?, ?, ?)";
        $insertStmt = mysqli_prepare($connection, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "ssi", $companyName, $companyDescription, $employerId);

        if (mysqli_stmt_execute($insertStmt)) {
            $successMessage = "Информация о компании успешно добавлена.";
        } else {
            $errorMessage = "Ошибка при добавлении информации о компании: " . mysqli_error($connection);
        }

        mysqli_stmt_close($insertStmt);
    }

    mysqli_stmt_close($stmt);
}

// Получаем данные о компании из базы данных
$query = "SELECT * FROM CompanyInfo WHERE employer_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $employerId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $companyName = $row['name'];
    $companyDescription = $row['description'];
}

mysqli_stmt_close($stmt);
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информация о компании</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключение CSS-файла -->
</head>
<body>
    <header>
        <h1>Информация о компании</h1>
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
        <section>
            <h2>Информация о вашей компании</h2>
            <?php
            if (isset($successMessage)) {
                echo '<div class="success-message">' . $successMessage . '</div>';
            } elseif (isset($errorMessage)) {
                echo '<div class="error-message">' . $errorMessage . '</div>';
            }
            ?>
            <form method="post">
                <div>
                    <label for="companyName">Название компании:</label>
                    <input type="text" id="companyName" name="companyName" value="<?php echo $companyName; ?>" required>
                </div>
                <div>
                    <label for="companyDescription">Описание компании:</label>
                    <textarea id="companyDescription" name="companyDescription" required><?php echo $companyDescription; ?></textarea>
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
