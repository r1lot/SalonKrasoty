
<?php
include("manNav.php");
include("db.php");

// Параметры пагинации
$limit = 10; // Лимит записей на страницу
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Обработка нажатия кнопки "Добавить"
if (isset($_POST['submit'])) {
    $login = $_POST['addLogin'];
    $passw = $_POST['addPassword'];
    $client_name = $_POST['addName'];
    $birth_date = $_POST['addBirthDate'];
    $gender = $_POST['addGender'] == 'Мужской' ? 'male' : 'female'; // Преобразуем значение gender в соответствии с требованиями таблицы
    $phone_number = $_POST['addPhoneNumber'];
    $email = $_POST['addEmail'];
    
    $query = "INSERT INTO clients (login, passw, client_name, birth_date, gender, phone_number, email) VALUES ('$login', '$passw', '$client_name', '$birth_date', '$gender', '$phone_number', '$email')";
    $result = mysqli_query($db, $query);
    
    if ($result) {
        header("Location: clientList.php");
        exit();
    } else {
        echo "Ошибка добавления клиента: " . mysqli_error($db);
    }
}

// Обработка нажатия кнопки "Изменить"
if (isset($_POST['correct'])) {
    $client_id = $_POST['client_id'];
    header("Location: clientUpd.php?client_id=$client_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление клиентами</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/css/bootstrap.min.css" integrity="sha512-MFT3h9KDR/Uo3NYaUuLzM67YvSpUfXhMu80vERPrJ4mICv7WtjrLKfDyQoJlrDik2QrKc5K0XxhxWxDV90uF0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #fce4ec; /* Розовый фон */
            font-family: 'Arial', sans-serif;
            color: #444;
        }
        .container {
            margin-top: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #ff80ab; /* Розовая кнопка */
            border-color: #ff80ab;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #f06292; /* Розовая кнопка при наведении */
            border-color: #f06292;
        }
        .form-control {
            font-size: 16px;
            padding: 12px;
        }
        .table {
            background-color: #fff; /* Белый фон таблицы */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="table-responsive">
                    <h4>Список клиентов</h4>
                    <table class="table table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="background-color: #ff80ab;">Номер</th>
                                <th style="background-color: #ff80ab;">Логин</th>
                                <th style="background-color: #ff80ab;">Имя</th>
                                <th style="background-color: #ff80ab;">Дата рождения</th>
                                <th style="background-color: #ff80ab;">Пол</th>
                                <th style="background-color: #ff80ab;">Номер телефона</th>
                                <th style="background-color: #ff80ab;">Email</th>
                                <th style="background-color: #ff80ab;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Подсчет общего количества клиентов для пагинации
                            $count_result = mysqli_query($db, "SELECT COUNT(*) AS total_clients FROM clients");
                            $total_clients = mysqli_fetch_assoc($count_result)['total_clients'];
                            $total_pages = ceil($total_clients / $limit);

// Получение клиентов с учетом лимита и смещения
                            $result = mysqli_query($db, "SELECT * FROM clients LIMIT $limit OFFSET $offset");
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['client_id'] . "</td>";
                                echo "<td>" . $row['login'] . "</td>";
                                echo "<td>" . $row['client_name'] . "</td>";
                                echo "<td>" . $row['birth_date'] . "</td>";
                                echo "<td>" . $row['gender'] . "</td>";
                                echo "<td>" . $row['phone_number'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td><form method='post'>";
                                echo "<button type='submit' name='correct' class='btn btn-primary'>Изменить</button>";
                                echo "<input type='hidden' name='client_id' value='" . $row['client_id'] . "'>";
                                echo "</form></td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <!-- Пагинация -->
                    <nav>
                        <ul class="pagination">
                            <?php
                            for ($i = 1; $i <= $total_pages; $i++) {
                                $active = ($i == $page) ? 'active' : '';
                                echo "<li class='page-item $active'><a class='page-link' href='?page=$i'>$i</a></li>";
                            }
                            ?>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="col-md-4">
                <form method="post" action="">
                    <h4>Добавить нового клиента</h4>
                    <div class="form-group">
                        <label for="addLogin">Логин:</label>
                        <input type="text" id="addLogin" name="addLogin" placeholder="Введите логин" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="addPassword">Пароль:</label>
                        <input type="password" id="addPassword" name="addPassword" placeholder="Введите пароль" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="addName">Имя клиента:</label>
                        <input type="text" id="addName" name="addName" placeholder="Введите имя" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="addBirthDate">Дата рождения:</label>
                        <input type="date" id="addBirthDate" name="addBirthDate" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="addGender">Пол:</label>
                        <select id="addGender" name="addGender" class="form-control">
                            <option selected disabled>Выберите пол</option>
                            <option>Мужской</option>
                            <option>Женский</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addPhoneNumber">Номер телефона:</label>
                        <input type="text" id="addPhoneNumber" name="addPhoneNumber" placeholder="Введите номер телефона" class="form-control">
                    </div>

<div class="form-group">
                        <label for="addEmail">Email:</label>
                        <input type="email" id="addEmail" name="addEmail" placeholder="Введите email" class="form-control">
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Добавить</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>