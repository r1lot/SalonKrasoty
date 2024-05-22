<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/Style2.css" type="text/css">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Oswald:400,300" type="text/css">
    <title>Личный кабинет</title>
    <style>
        body {
            font-family: 'Oswald', sans-serif;
            background-color: #fce4ec;
            color: #444;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding-top: 20px;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            outline: none;
        }
        .btn {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #f06292;
        }
    </style>
</head>
<body>
<?php 
include("clientNav.php"); 
include("db.php");

// Проверяем, установлена ли переменная client_id в сессии
if (!isset($_SESSION['client_id'])) {
    // Если нет, можно выполнить какое-то действие, например, перенаправить пользователя на страницу входа
    header("Location: log.php"); // Предполагается, что у вас есть страница входа с именем login.php
    exit(); // Завершаем выполнение скрипта
}

// Теперь, когда мы убедились, что client_id установлен в сессии, мы можем его использовать
$client_id = $_SESSION['client_id'];

$sql = "SELECT * FROM clients WHERE client_id=$client_id";
$result = mysqli_query($db, $sql);

// Проверяем, выполнился ли запрос успешно
if (!$result) {
    // Если запрос не выполнен, выводим сообщение об ошибке и завершаем выполнение скрипта
    echo "Ошибка при выполнении запроса: " . mysqli_error($db);
    exit();
}

// Проверяем, есть ли записи в результате запроса
if (mysqli_num_rows($result) == 0) {
    // Если нет записей, выводим сообщение об ошибке и завершаем выполнение скрипта
    echo "Не удалось найти информацию о клиенте с ID $client_id";
    exit();
}

$myrow = mysqli_fetch_array($result);
//полученные из БД значения полей запишем в переменные
$login = $myrow["login"];
$passw = $myrow["passw"];
$client_name = $myrow["client_name"];
$birth_date = $myrow["birth_date"];
$gender = $myrow["gender"];
$phone_number = $myrow["phone_number"];
$email = $myrow["email"];
?>

<div class="container">
    <div class="form-container">
        <form action="#" method="POST">
            <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
            <h2>Редактирование профиля</h2>
            <div class="form-group">
                <label for="login">Логин</label>
                <input type="text" id="login" name="login" placeholder="Логин" class="form-control" value="<?php echo $login; ?>" required>
            </div>
            <div class="form-group">
                <label for="passw">Пароль</label>
                <input type="password" id="passw" name="passw" placeholder="Пароль" class="form-control" value="<?php echo $passw; ?>" required>
            </div>
            <div class="form-group">
                <label for="client_name">ФИО</label>
                <input type="text" id="client_name" name="client_name" placeholder="ФИО" class="form-control" value="<?php echo $client_name; ?>">
            </div>
            <div class="form-group">
                <label for="birth_date">Дата рождения</label>
                <input type="date" id="birth_date" name="birth_date" class="form-control" value="<?php echo $birth_date; ?>">
            </div>
            <div class="form-group">
                <label>Гендер</label>
                <select name="gender" class="form-control">
                    <option value="Male" <?php if ($gender == 'Male') echo 'selected'; ?>>Мужской</option>
                    <option value="Female" <?php if ($gender == 'Female') echo 'selected'; ?>>Женский</option>
                </select>
            </div>
            <div class="form-group">
                <label for="phone_number">Телефон</label>
                <input type="text" id="phone_number" name="phone_number" placeholder="Телефон" class="form-control" value="<?php echo $phone_number; ?>">
            </div>
            <div class="form-group">
                <label for="email">Почта</label>
                <input type="email" id="email" name="email" placeholder="Email" class="form-control" value="<?php echo $email; ?>">
            </div>
            <button type="submit" name="submit" class="btn" style="background-color: pink; color:#fff;">Сохранить изменения</button>
        </form>
    </div>
</div>

<?php
if (isset($_POST['submit'])) {
    $client_id = $_POST["client_id"]; // Получаем client_id из скрытого поля
    $login = $_POST["login"];
    $passw = $_POST["passw"];
    $client_name = $_POST["client_name"];
    $birth_date = $_POST["birth_date"];
    $gender = $_POST["gender"];
    $phone_number = $_POST["phone_number"];
    $email = $_POST["email"];
    
    // Обновляем данные в базе данных
    $sql = "UPDATE clients SET login='$login', passw='$passw', client_name='$client_name', birth_date='$birth_date', gender='$gender', phone_number='$phone_number', email='$email' WHERE client_id=$client_id";
    $result = mysqli_query($db, $sql);
    
    // Проверяем, успешно ли обновлены данные
    if ($result) {
        echo "<script> alert('Данные успешно сохранены!'); window.location.href = 'client.php'; </script>";
    } else {
        echo "<script> alert('Ошибка при сохранении данных!'); </script>";
    }
}
?>
</body>
</html>
