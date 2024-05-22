<?php
include("manNav.php");
include("db.php");

// Проверяем, был ли передан идентификатор услуги
if (isset($_GET['service_id'])) {
    $service_id = $_GET['service_id'];

    // Получаем данные о выбранной услуге из базы данных
    $result = mysqli_query($db, "SELECT * FROM services WHERE service_id=$service_id");
    if ($result) {
        $row = mysqli_fetch_array($result);
        $service_name = $row['service_name'];
        $service_price = $row['service_price'];
    } else {
        echo "Ошибка получения данных об услуге";
        exit();
    }
} else {
    echo "Не передан идентификатор услуги";
    exit();
}

// Обработка нажатия кнопки "Сохранить"
if (isset($_POST['save'])) {
    $new_name = $_POST['newName'];
    $new_price = $_POST['newPrice'];
    $sql = "UPDATE services SET service_name='$new_name', service_price='$new_price' WHERE service_id=$service_id";
    $result = mysqli_query($db, $sql);

    if ($result) {
        header("Location: serviceList.php");
        exit();
    } else {
        echo "Ошибка сохранения данных об услуге";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование услуги</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/css/bootstrap.min.css" integrity="sha512-MFT3h9KDR/Uo3NYaUuLzM67YvSpUfXhMu80vERPrJ4mICv7WtjrLKfDyQoJlrDik2QrKc5K0XxhxWxDV90uF0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #fce4ec; /* Розовый фон */
            font-family: 'Arial', sans-serif;
            color: #444;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding-top: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form method="post" action="">
                    <h4>Редактировать услугу</h4>
                    <div class="form-group">
                        <label for="newName">Новое название:</label>
                        <input type="text" id="newName" name="newName" class="form-control" value="<?php echo $service_name; ?>">
                    </div>
                    <div class="form-group">
                        <label for="newPrice">Новая стоимость:</label>
                        <input type="number" id="newPrice" name="newPrice" class="form-control" value="<?php echo $service_price; ?>">
                    </div>
                    <button type="submit" name="save" class="btn btn-primary">Сохранить</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
