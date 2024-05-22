<?php
include("manNav.php");
include("db.php");

if (isset($_GET['client_id'])) {
    $client_id = $_GET['client_id'];
    $query = "SELECT * FROM clients WHERE client_id=$client_id";
    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $login = $row['login'];
        $passw = $row['passw'];
        $client_name = $row['client_name'];
        $birth_date = $row['birth_date'];
        $gender = $row['gender'];
        $phone_number = $row['phone_number'];
        $email = $row['email'];
    } else {
        echo "Клиент не найден";
        exit();
    }
} else {
    echo "Ошибка: Не указан ID клиента";
    exit();
}

if (isset($_POST['submit'])) {
    $login = $_POST['updLogin'];
    $passw = $_POST['updPassword'];
    $client_name = $_POST['updName'];
    $birth_date = $_POST['updBirthDate'];
    $gender = $_POST['updGender'] == 'Мужской' ? 'male' : 'female'; // Преобразуем значение gender в соответствии с требованиями таблицы
    $phone_number = $_POST['updPhoneNumber'];
    $email = $_POST['updEmail'];
    
    $query = "UPDATE clients SET login='$login', passw='$passw', client_name='$client_name', birth_date='$birth_date', gender='$gender', phone_number='$phone_number', email='$email' WHERE client_id=$client_id";
    $result = mysqli_query($db, $query);
    
    if ($result) {
        header("Location: clientList.php");
        exit();
    } else {
        echo "Ошибка обновления данных клиента: " . mysqli_error($db);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование клиента</title>
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
        <h4 class="text-center">Редактирование клиента</h4>
        <form method="post" action="">
            <div class="form-group">
                <label for="updLogin">Логин:</label>
                <input type="text" id="updLogin" name="updLogin" value="<?php echo $login; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="updPassword">Пароль:</label>
                <input type="password" id="updPassword" name="updPassword" value="<?php echo $passw; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="updName">Имя клиента:</label>
                <input type="text" id="updName" name="updName" value="<?php echo $client_name; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="updBirthDate">Дата рождения:</label>
                <input type="date" id="updBirthDate" name="updBirthDate" value="<?php echo $birth_date; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="updGender">Пол:</label>
                <select id="updGender" name="updGender" class="form-control">
                    <option <?php if ($gender == 'male') echo 'selected'; ?>>Мужской</option>
                    <option <?php if ($gender == 'female') echo 'selected'; ?>>Женский</option>
                </select>
            </div>
            <div class="form-group">
                <label for="updPhoneNumber">Номер телефона:</label>
                <input type="text" id="updPhoneNumber" name="updPhoneNumber" value="<?php echo $phone_number; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="updEmail">Email:</label>
                <input type="email" id="updEmail" name="updEmail" value="<?php echo $email; ?>" class="form-control">
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Сохранить изменения</button>
        </form>
    </div>
</body>
</html>
