<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход и регистрация</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключение CSS-файла -->
</head>
<body>
    <header>
        <h1>Вход и регистрация</h1>
    </header>
    <nav>
        <ul>
            <li><a href="main.php">Главная</a></li>
            <!-- Другие пункты навигации -->
        </ul>
    </nav>
    <main>
        <section class="login-registration">
            <h2>Выберите действие</h2>
            <div class="forms">
                <form id="login-form" action="login_process.php" method="post">
                    <h3>Вход</h3>
                    <label for="login-username">Имя пользователя:</label>
                    <input type="text" id="login-username" name="login-username" required>
                    <label for="login-password">Пароль:</label>
                    <input type="password" id="login-password" name="login-password" required>
                    <button type="submit">Войти</button>
                </form>
                <div id="login-message"></div>
                <form id="registration-form" action="registration_process.php" method="post">
                    <h3>Регистрация</h3>
                    <label for="reg-username">Имя пользователя:</label>
                    <input type="text" id="reg-username" name="reg-username" required>
                    <label for="reg-email">Email:</label>
                    <input type="email" id="reg-email" name="reg-email" required>
                    <hr>
                    <label for="reg-password">Пароль:</label>
                    <input type="password" id="reg-password" name="reg-password" required>
                    <label for="role">Роль:</label>
                    <select id="role" name="role" required>
                        <option value="employer">Работодатель</option>
                        <option value="applicant">Соискатель</option>
                    </select>
                    <button type="button" onclick="registerUser()">Зарегистрироваться</button>
                </form>
            </div>
            <div id="registration-message"></div>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Наш сайт по поиску работы</p>
    </footer>
    <script>
        document.getElementById("login-form").addEventListener("submit", function (event) {
    event.preventDefault();
    var formData = new FormData(this);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", this.action, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                window.location.href = "main.php"; // Перенаправление на главную страницу при успешном входе
            } else {
                document.getElementById("login-message").innerHTML = "Ошибка: " + response.message;
            }
        }
    };
    xhr.onerror = function () {
        document.getElementById("login-message").innerHTML = "Ошибка при отправке запроса";
    };
    xhr.send(formData);
});

    </script>
    <script>
        function registerUser() {
    var form = document.getElementById("registration-form");
    var formData = new FormData(form);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", form.action, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                document.getElementById("registration-message").innerHTML = "Регистрация успешна!";
            } else {
                document.getElementById("registration-message").innerHTML = response.message;
            }
        }
    };
    xhr.onerror = function () {
        document.getElementById("registration-message").innerHTML = "Ошибка при отправке запроса";
    };
    xhr.send(formData);
}

    </script>
</body>
</html>
