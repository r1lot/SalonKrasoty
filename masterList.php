
<?php
include("manNav.php");
include("db.php");

// Параметры пагинации
$limit = 10; // Лимит записей на страницу
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Обработка нажатия кнопки "Добавить"
if (isset($_POST['submit'])) {
    $master_name = $_POST['addMasterName'];
    $birth_date = $_POST['addBirthDate'];
    $gender = $_POST['addGender'];
    $phone_number = $_POST['addPhoneNumber'];
    $hire_date = $_POST['addHireDate'];
    
    $query = "INSERT INTO masters (master_name, birth_date, gender, phone_number, hire_date) VALUES ('$master_name', '$birth_date', '$gender', '$phone_number', '$hire_date')";
    $result = mysqli_query($db, $query);
    
    if ($result) {
        header("Location: masterList.php");
        exit();
    } else {
        echo "Ошибка добавления мастера: " . mysqli_error($db);
    }
}

// Обработка нажатия кнопки "Изменить"
if (isset($_POST['correct'])) {
    $master_id = $_POST['master_id'];
    header("Location: masterUpd.php?master_id=$master_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление мастерами</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/css/bootstrap.min.css" integrity="sha512-MFT3h9KDR/Uo3NYaUuLzM67YvSpUfXhMu80vERPrJ4mICv7WtjrLKfDyQoJlrDik2QrKc5K0XxhxWxDV90uF0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #fce4ec; /* Розовый фон */
            font-family: 'Arial', sans-serif;
            color: #444;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding-top: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .table {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #ff80ab; /* Розовый цвет для заголовков */
            color: #fff;
            font-weight: bold;
            text-align: center;
        }
        .table td {
            text-align: center;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="table-responsive">
                    <h4>Список мастеров</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="background-color: #ff80ab;">Номер</th>
                                <th style="background-color: #ff80ab;">Имя</th>
                                <th style="background-color: #ff80ab;">Дата рождения</th>
                                <th style="background-color: #ff80ab;">Пол</th>
                                <th style="background-color: #ff80ab;">Номер телефона</th>
                                <th style="background-color: #ff80ab;">Дата найма</th>
                                <th style="background-color: #ff80ab;">Дата увольнения</th>
                                <th style="background-color: #ff80ab;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Подсчет общего количества мастеров для пагинации

$count_result = mysqli_query($db, "SELECT COUNT(*) AS total_masters FROM masters");
                            $total_masters = mysqli_fetch_assoc($count_result)['total_masters'];
                            $total_pages = ceil($total_masters / $limit);

                            // Получение мастеров с учетом лимита и смещения
                            $result = mysqli_query($db, "SELECT * FROM masters LIMIT $limit OFFSET $offset");
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['master_id'] . "</td>";
                                echo "<td>" . $row['master_name'] . "</td>";
                                echo "<td>" . $row['birth_date'] . "</td>";
                                echo "<td>" . $row['gender'] . "</td>";
                                echo "<td>" . $row['phone_number'] . "</td>";
                                echo "<td>" . $row['hire_date'] . "</td>";
                                echo "<td>" . $row['dismissal_date'] . "</td>";
                                echo "<td><form method='post'>";
                                echo "<button type='submit' name='correct' class='btn btn-primary'>Изменить</button>";
                                echo "<input type='hidden' name='master_id' value='" . $row['master_id'] . "'>";
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
            <div class="col-lg-4">
                <h4>Добавить нового мастера</h4>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="addMasterName">Имя мастера:</label>
                        <input type="text" id="addMasterName" name="addMasterName" placeholder="Введите имя мастера" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="addBirthDate">Дата рождения:</label>
                        <input type="date" id="addBirthDate" name="addBirthDate" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="addGender">Пол:</label>
                        <select id="addGender" name="addGender" class="form-control">
                            <option value="male">Мужской</option>
                            <option value="female">Женский</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addPhoneNumber">Номер телефона:</label>
                        <input type="text" id="addPhoneNumber" name="addPhoneNumber" placeholder="Введите номер телефона" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="addHireDate">Дата найма:</label>
                        <input type="date" id="addHireDate" name="addHireDate" class="form-control">
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Добавить</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>