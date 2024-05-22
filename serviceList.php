
<?php
include("manNav.php");
include("db.php");

// Параметры пагинации
$limit = 10; // Лимит записей на страницу
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Обработка нажатия кнопки "Добавить"
if (isset($_POST['submit'])) {
    $service_name = $_POST['addName'];
    $service_price = $_POST['addPrice'];
    
    $query = "INSERT INTO services (service_name, service_price) VALUES ('$service_name', '$service_price')";
    $result = mysqli_query($db, $query);
    
    if ($result) {
        header("Location: serviceList.php");
        exit();
    } else {
        echo "Ошибка добавления услуги";
    }
}

// Обработка нажатия кнопки "Изменить"
if (isset($_POST['correct'])) {
    $service_id = $_POST['service_id'];
    header("Location: serviceUpd.php?service_id=$service_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление услугами</title>
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
        .mt-4 {
            margin-top: 20px; /* Увеличиваем расстояние между объектами */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form method="post" action="">
                    <h4>Добавить новую услугу</h4>
                    <div class="form-group">
                        <label for="addName">Название программы обучения:</label>
                        <input type="text" id="addName" name="addName" placeholder="Введите название" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="addPrice">Стоимость:</label>
                        <input type="number" id="addPrice" name="addPrice" placeholder="1000" class="form-control">
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Добавить</button>
                </form>
            </div>
        </div>
        <div class="row justify-content-center mt-4">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <h4>Редактирование программ</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="background-color: #ff80ab;">Номер</th>
                                <th style="background-color: #ff80ab;">Название</th>

<th style="background-color: #ff80ab;">Цена</th>
                                <th style="background-color: #ff80ab;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Подсчет общего количества услуг для пагинации
                            $count_result = mysqli_query($db, "SELECT COUNT(*) AS total_services FROM services");
                            $total_services = mysqli_fetch_assoc($count_result)['total_services'];
                            $total_pages = ceil($total_services / $limit);

                            // Получение услуг с учетом лимита и смещения
                            $result = mysqli_query($db, "SELECT * FROM services LIMIT $limit OFFSET $offset");
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['service_id'] . "</td>";
                                echo "<td>" . $row['service_name'] . "</td>";
                                echo "<td>" . $row['service_price'] . "</td>";
                                echo "<td><form method='post'>";
                                echo "<button type='submit' name='correct' class='btn btn-primary'>Изменить</button>";
                                echo "<input type='hidden' name='service_id' value='" . $row['service_id'] . "'>";
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
        </div>
    </div>
</body>
</html>