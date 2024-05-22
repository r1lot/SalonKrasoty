<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма авторизации и регистрации</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fce4ec; /* Розовый фон */
        }
        .container {
            margin-top: 100px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #ff80ab; /* Розовый заголовок */
            color: #fff;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .btn-toggle {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card" id="login-card">
                <div class="card-header">
                    <h3>Авторизация</h3>
                </div>
                <div class="card-body">
                    <form id="login-form" action="log.php" method="POST" onsubmit="return valid('login')">
                        <input type="hidden" name="action" value="signin">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Логин" name="login" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="Пароль" name="passw" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" name="submit">Войти</button>
                    </form>
                </div>
            </div>
            <div class="card mt-4 d-none" id="register-card">
                <div class="card-header">
                    <h3>Регистрация</h3>
                </div>
                <div class="card-body">
                    <form id="register-form" action="log.php" method="POST" onsubmit="return valid('signup')">
                        <input type="hidden" name="action" value="signup">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Логин" name="login" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="Пароль" name="passw" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="Повторите пароль" name="repassw" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" name="submit">Зарегистрироваться</button>
                    </form>
                </div>
            </div>
            <div class="text-center btn-toggle">
                <button class="btn btn-link" id="toggle-btn">Авторизация/Регистрация</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Переключение между формами
        $('#toggle-btn').click(function() {
            $('#login-card').toggleClass('d-none');
            $('#register-card').toggleClass('d-none');
        });
    });
</script>

<?php
if (isset($_POST['submit'])) {
    $action = $_POST['action']; // Добавлено получение значения $_POST['action']
    if ($action == "signup") { // Проверка на регистрацию
        $login = $_POST['login'];
        $passw = $_POST['passw'];
        $repassw = $_POST['repassw'];

        if (empty($login) || empty($passw) || empty($repassw)) {
            exit("Вы ввели не всю информацию");
        }

        if ($passw != $repassw) {
            exit("Пароли не совпадают");
        }

        include("db.php");

        // Пример запроса на вставку данных в базу данных без хеширования пароля
        $query = "INSERT INTO clients (login, Passw) VALUES ('$login', '$passw')";
        $result = mysqli_query($db, $query);

        if ($result) {
            echo "Вы успешно зарегистрированы. Теперь Вы можете авторизоваться и перейти в личный кабинет";
            $_SESSION['login'] = $login;
            $_SESSION['client_id'] = mysqli_insert_id($db);
            echo "<script> document.location.href = 'client.php'</script>";
        } else {
            echo "Ошибка регистрации: " . mysqli_error($db);
        }
    } elseif ($action == "signin") { // Авторизация
        $login = $_POST['login'];
        $passw = $_POST['passw'];

        if (empty($login) || empty($passw)) {
            exit("Вы ввели не всю информацию");
        }

        include("db.php");

        // Пример запроса к базе данных для получения пользователя по логину
        $query = "SELECT * FROM clients WHERE login='$login'";
        $result = mysqli_query($db, $query);

        if (mysqli_num_rows($result) == 0) {
            exit("Извините, пользователь с таким логином не зарегистрирован");
        }

        $row = mysqli_fetch_assoc($result);
        $db_passw = $row['passw'];

        // Сравнение паролей
        if ($passw == $db_passw) {
            $_SESSION['login'] = $row['login'];
            $_SESSION['client_id'] = $row['client_id'];

            if ($login == "manager") {
                echo "<script> document.location.href = 'manOrders.php'</script>";
            } elseif ($login == "director") {
                echo "<script> document.location.href = 'director.php'</script>";
            } else {
                echo "<script> document.location.href = 'client.php'</script>";
            }
        } else {
            exit("Пароль неверный");
        }
    }
}
?>
</body>
</html>
