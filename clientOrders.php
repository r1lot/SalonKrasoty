<?php
include("clientNav.php");
include("db.php");

// Переменные для хранения выбранных значений фильтров
$selected_service = isset($_GET['service_name']) ? $_GET['service_name'] : "";
$selected_master = isset($_GET['master_name']) ? $_GET['master_name'] : "";
$selected_date = isset($_GET['appointment_date']) ? $_GET['appointment_date'] : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заявки</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
        <div class="col-md-6">
            <form method="get" action="" class="mb-4">
                <div class="form-group">
                    <label for="service_name">Название услуги:</label>
                    <select class="form-control" id="service_name" name="service_name">
                        <option value="">Выберите услугу</option>
                        <?php
                        $sql_services = "SELECT DISTINCT service_name FROM services";
                        $result_services = mysqli_query($db, $sql_services);
                        if ($result_services) {
                            while ($row = mysqli_fetch_assoc($result_services)) {
                                $selected = $selected_service == $row['service_name'] ? "selected" : "";
                                echo "<option value='" . $row['service_name'] . "' $selected>" . $row['service_name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="master_name">ФИО мастера:</label>
                    <select class="form-control" id="master_name" name="master_name">
                        <option value="">Выберите мастера</option>
                        <?php
                        $sql_masters = "SELECT DISTINCT master_name FROM masters";
                        $result_masters = mysqli_query($db, $sql_masters);
                        if ($result_masters) {
                            while ($row = mysqli_fetch_assoc($result_masters)) {
                                $selected = $selected_master == $row['master_name'] ? "selected" : "";
                                echo "<option value='" . $row['master_name'] . "' $selected>" . $row['master_name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="appointment_date">Дата записи:</label>
                    <input type="date" class="form-control" id="appointment_date" name="appointment_date" value="<?php echo $selected_date; ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Применить фильтр</button>
                    <a href="clientOrders.php" class="btn btn-secondary ml-2">Сбросить фильтр</a>
                </div>
            </form>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <?php
            $client_id=$_SESSION['client_id'];
            $sql = "SELECT
                        appointments.appointment_id,
                        appointments.status,
                        appointments.appointment_date,
                        appointments.appointment_time,
                        services.service_id,
                        services.service_name,
                        masters.master_name
                    FROM appointments 
                    INNER JOIN services ON appointments.service_id = services.service_id 
                    INNER JOIN masters ON appointments.master_id = masters.master_id 
                    INNER JOIN clients ON appointments.client_id = clients.client_id 
                    WHERE clients.client_id = $client_id";

            // Применение фильтров, если они указаны
            if (!empty($_GET['service_name'])) {
                $service_name = mysqli_real_escape_string($db, $_GET['service_name']);
                $sql .= " AND services.service_name = '$service_name'";
            }
            if (!empty($_GET['master_name'])) {
                $master_name = mysqli_real_escape_string($db, $_GET['master_name']);
                $sql .= " AND masters.master_name = '$master_name'";
            }
            if (!empty($_GET['appointment_date'])) {
                $appointment_date = mysqli_real_escape_string($db, $_GET['appointment_date']);
                $sql .= " AND appointments.appointment_date = '$appointment_date'";
            }

            $result_appointments = mysqli_query($db, $sql);

            if (!$result_appointments) {
                echo "Error: " . mysqli_error($db);
            } else {
                echo "<h4 class='mb-3'>Мои заявки</h4>";
                echo "<div class='table-responsive'>
                        <table class='table table-bordered'>
                            <thead class='thead-light'>
                                <tr>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th>Мастер</th>
                                    <th>Дата записи</th>
                                    <th>Время записи</th>
                                    <th>Статус</th>
                                </tr>
                            </thead>
                            <tbody>";

                while ($myrow = mysqli_fetch_array($result_appointments)) {
                    $stat = "";
                    if($myrow['status']==0) {
                        $stat = "Активна";
                    } else if($myrow['status']==1) {
                        $stat = "Отменена";
                    } else if($myrow['status']==2) {
                        $stat = "Выполнена";
                    }

                    echo "<tr>";
                    echo "<td>".$myrow['appointment_id']."</td>";
                    echo "<td>".$myrow['service_name']."</td>";
                    echo "<td>".$myrow['master_name']."</td>";
                    echo "<td>".$myrow['appointment_date']."</td>";
                    echo "<td>".$myrow['appointment_time']."</td>";
                    echo "<td>".$stat."</td>";
                    echo "</tr>";
                } 
                echo "</tbody></table></div>";
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>
