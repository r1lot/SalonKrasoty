<?php
include("manNav.php");
include("db.php");

if(isset($_GET['master_id'])) {
    $master_id = $_GET['master_id'];

    $query = "SELECT * FROM masters WHERE master_id = '$master_id'";
    $result = mysqli_query($db, $query);

    if(mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $master_name = $row['master_name'];
        $birth_date = $row['birth_date'];
        $gender = $row['gender'];
        $phone_number = $row['phone_number'];
        $hire_date = $row['hire_date'];
        $dismissal_date = $row['dismissal_date'];
    } else {
        echo "Мастер не найден.";
    }
}

if(isset($_POST['update'])) {
    $master_id = $_POST['master_id'];
    $master_name = $_POST['master_name'];
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $phone_number = $_POST['phone_number'];
    $hire_date = $_POST['hire_date'];
    $dismissal_date = $_POST['dismissal_date'];

    $query = "UPDATE masters SET master_name = '$master_name', birth_date = '$birth_date', gender = '$gender', phone_number = '$phone_number', hire_date = '$hire_date', dismissal_date = '$dismissal_date' WHERE master_id = '$master_id'";
    $result = mysqli_query($db, $query);

    if($result) {
        header("Location: masterList.php");
        exit();
    } else {
        echo "Ошибка при обновлении информации о мастере: " . mysqli_error($db);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование информации о мастере</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/css/bootstrap.min.css" integrity="sha512-MFT3h9KDR/Uo3NYaUuLzM67YvSpUfXhMu80vERPrJ4mICv7WtjrLKfDyQoJlrDik2QrKc5K0XxhxWxDV90uF0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <h2>Редактирование информации о мастере</h2>
                <form method="post" action="">
                    <input type="hidden" name="master_id" value="<?php echo $master_id; ?>">
                    <div class="form-group">
                        <label for="master_name">Имя мастера:</label>
                        <input type="text" id="master_name" name="master_name" class="form-control" value="<?php echo $master_name; ?>">
                    </div>
                    <div class="form-group">
                        <label for="birth_date">Дата рождения:</label>
                        <input type="date" id="birth_date" name="birth_date" class="form-control" value="<?php echo $birth_date; ?>">
                    </div>
                    <div class="form-group">
                        <label for="gender">Пол:</label>
                        <select id="gender" name="gender" class="form-control">
                            <option value="male" <?php if($gender == 'male') echo 'selected'; ?>>Мужской</option>
                            <option value="female" <?php if($gender == 'female') echo 'selected'; ?>>Женский</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Номер телефона:</label>
                        <input type="text" id="phone_number" name="phone_number" class="form-control" value="<?php echo $phone_number; ?>">
                    </div>
                    <div class="form-group">
                        <label for="hire_date">Дата найма:</label>
                        <input type="date" id="hire_date" name="hire_date" class="form-control" value="<?php echo $hire_date; ?>">
                    </div>
                    <div class="form-group">
                        <label for="dismissal_date">Дата увольнения:</label>
                        <input type="date" id="dismissal_date" name="dismissal_date" class="form-control" value="<?php echo $dismissal_date; ?>">
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Обновить</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
