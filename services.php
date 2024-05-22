
<?php
session_start();
include("clientNav.php");
include("db.php");

$title = "Программа обучения";
if (!empty($_GET['service_name']) || !empty($_GET['max_price'])) {
    $title .= " (Фильтр)";
}

echo "<title>$title</title>";

$sql = "SELECT * FROM services";
if (!empty($_GET['service_name'])) {
    $service_name = mysqli_real_escape_string($db, $_GET['service_name']);
    $sql .= " WHERE service_name LIKE '%$service_name%'";
}
if (!empty($_GET['max_price'])) {
    $max_price = (float)$_GET['max_price'];
    $sql .= (!empty($_GET['service_name']) ? " AND" : " WHERE") . " service_price <= $max_price";
}

$result_services = mysqli_query($db, $sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['validate_appointment'])) {
        $appointment_date = mysqli_real_escape_string($db, $_POST['appointment_date']);
        $appointment_time = mysqli_real_escape_string($db, $_POST['appointment_time']);
        $master_id = (int)$_POST['master_id'];

        // Check if the appointment slot is already taken
        $sql = "SELECT COUNT(*) as count FROM appointments WHERE master_id = $master_id AND appointment_date = '$appointment_date' AND appointment_time = '$appointment_time'";
        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_assoc($result);

        if ($row['count'] > 0) {
            // Find the latest occupied time slot for the master on the same day
            $sql = "SELECT MAX(appointment_time) as last_appointment_time FROM appointments WHERE master_id = $master_id AND appointment_date = '$appointment_date'";
            $result = mysqli_query($db, $sql);
            $row = mysqli_fetch_assoc($result);
            $last_appointment_time = $row['last_appointment_time'];

            $suggested_time = new DateTime($last_appointment_time);
            $suggested_time->modify('+1 hour');
            echo json_encode(['status' => 'occupied', 'suggested_time' => $suggested_time->format('H:i')]);
        } else {
            echo json_encode(['status' => 'available']);
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #fce4ec; /* Розовый фон */
            font-family: Arial, sans-serif;
            color: #444;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding-top: 20px;
        }
        .table {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle !important;
        }
        .btn-appointment {
            background-color: #e91e63; /* Розовый цвет кнопки */
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
        .btn-appointment:hover {
            background-color: #c2185b; /* Изменение цвета при наведении */
        }
        .form-control {
            border-color: #e91e63; /* Розовая рамка для полей формы */
        }
        .btn-primary {
            background-color: #e91e63; /* Розовая кнопка "Применить фильтр" */
            border-color: #e91e63;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #c2185b; /* Изменение цвета кнопки при наведении */
            border-color: #c2185b;
        }
        .btn-secondary {
            background-color: #fff; /* Белая кнопка "Очистить фильтры" */
            border-color: #ccc;

color: #444;
        }
        .btn-secondary:hover {
            background-color: #f8bbd0; /* Изменение цвета кнопки при наведении */
            border-color: #f8bbd0;
            color: #444;
        }
        .modal {
            display: none; /* Скрытие модального окна по умолчанию */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 10px;
            text-align: center;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 desc">
            <h4>Выбор услуги</h4>
            <table class='table table-bordered table-sm'>
                <tr class='table-primary'>
                    <th style='background-color: #f8bbd0;'>Номер</th>
                    <th style='background-color: #f8bbd0;'>Название</th>
                    <th style='background-color: #f8bbd0;'>Цена</th>
                    <th style='background-color: #f8bbd0;'></th>
                </tr>

                <?php
                while ($myrow = mysqli_fetch_array($result_services)) {
                    echo "<tr>";
                    echo "<td>".$myrow['service_id']."</td>";
                    echo "<td>".$myrow['service_name']."</td>";
                    echo "<td>".$myrow['service_price']."</td>";
                    echo "<td>
                        <button type='button' class='btn btn-appointment' onclick='openModal(".$myrow['service_id'].")'>
                            Записаться
                        </button>
                    </td>";
                    echo "</tr>";
                }
                ?>

            </table>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-12">
            <h4>Фильтр</h4>
            <form method="get" action="">
                <div class="form-group">
                    <label for="service_name">Название услуги:</label>
                    <select class="form-control" id="service_name" name="service_name">
                        <option value="">Выберите название услуги</option>
                        <?php
                        $sql = "SELECT DISTINCT service_name FROM services";
                        $result = mysqli_query($db, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='".$row['service_name']."'>".$row['service_name']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="max_price">Максимальная цена:</label>
                    <input type="number" class="form-control" id="max_price" name="max_price">

</div>
                <button type="submit" class="btn btn-primary">Применить фильтр</button>
                <a href="services.php" class="btn btn-secondary">Очистить фильтры</a>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно -->
<div id="appointmentModal" class="modal">
    <!-- Контент модального окна -->
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Выбор даты и времени записи</h2>
        <form id="appointmentForm" method="post" action="submitOrders.php" onsubmit="return validateAppointment()">
            <label for="appointmentDate">Дата:</label>
            <input type="date" id="appointmentDate" name="appointment_date" required><br><br>
            <label for="appointmentTime">Время:</label>
            <input type="time" id="appointmentTime" name="appointment_time" required><br><br>
            <label for="master_name">Выберите мастера:</label>
            <select class="form-control" id="master_name" name="master_id" required>
                <?php
                $sql = "SELECT master_id, master_name FROM masters WHERE dismissal_date IS NULL";
                $result = mysqli_query($db, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='".$row['master_id']."'>".$row['master_name']."</option>";
                }
                ?>
            </select>
            <input type="hidden" id="selectedServiceId" name="service_id">
            <button type="submit" class="btn btn-appointment">Записаться</button>
        </form>
    </div>
</div>

<script>
function openModal(serviceId) {
    document.getElementById('selectedServiceId').value = serviceId;
    document.getElementById('appointmentModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('appointmentModal').style.display = 'none';
}

function validateAppointment() {
    const appointmentDate = document.getElementById('appointmentDate').value;
    const appointmentTime = document.getElementById('appointmentTime').value;
    const masterId = document.getElementById('master_name').value;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'services.php', false);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('validate_appointment=1&appointment_date=' + appointmentDate + '&appointment_time=' + appointmentTime + '&master_id=' + masterId);

    const response = JSON.parse(xhr.responseText);
    if (response.status !== 'available') {
        alert('В выбранное время данный мастер занят. Пожалуйста, выберите другое время. Например, ' + response.suggested_time);
        return false;
    }

    return true;
}
</script>

</body>
</html>