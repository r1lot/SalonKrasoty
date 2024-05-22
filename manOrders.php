
<?php
include("manNav.php");
include("db.php");

// Параметры пагинации
$limit = 10; // Лимит записей на страницу
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление заявками</title>
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
        .table {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .table th {
            background-color: #ff80ab; /* Розовый цвет для заголовков */
            color: #fff;
            font-weight: bold;
        }
        .btn {
            border-radius: 5px;
            transition: background-color 0.3s;
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
                    <div class="form-group">
                        <h4>Выберите тип заявок:</h4>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rb" id="rb0" value="0" checked>
                            <label class="form-check-label" for="rb0">Активные заявки</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rb" id="rb1" value="1">
                            <label class="form-check-label" for="rb1">Отмененные заявки</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rb" id="rb2" value="2">
                            <label class="form-check-label" for="rb2">Выполненные заявки</label>
                        </div>
                    </div>
                    <button type="submit" name="search" class="btn btn-primary">Поиск</button>
                </form>
            </div>
        </div>
        <div class="row justify-content-center mt-4">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <?php
                    if (isset($_POST['search']) || isset($_GET['stat'])) {
                        $stat = isset($_POST['rb']) ? $_POST['rb'] : $_GET['stat'];
                        
                        $sql = "SELECT COUNT(*) AS total_appointments FROM appointments WHERE status=$stat";
                        $count_result = mysqli_query($db, $sql);
                        $total_appointments = mysqli_fetch_assoc($count_result)['total_appointments'];
                        $total_pages = ceil($total_appointments / $limit);
$sql = "SELECT appointments.appointment_id, appointments.status, appointments.appointment_date, appointments.appointment_time, 
                                services.service_name, clients.client_name, masters.master_name
                                FROM appointments 
                                INNER JOIN services ON services.service_id=appointments.service_id  
                                INNER JOIN clients ON appointments.client_id=clients.client_id 
                                INNER JOIN masters ON masters.master_id=appointments.master_id 
                                WHERE appointments.status=$stat
                                LIMIT $limit OFFSET $offset";
                        $result = mysqli_query($db, $sql);

                        if (!$result) {
                            echo "Error: " . mysqli_error($db);
                        } else {
                            echo "<h4 class='text-center mb-4'>Заявки</h4>";
                            echo "<table class='table table-bordered'>
                                <thead>
                                    <tr>
                                        <th>Номер</th>
                                        <th>Клиент</th>
                                        <th>Услуга</th>
                                        <th>Мастер</th>
                                        <th>Дата услуги</th>
                                        <th>Время услуги</th>
                                        <th>Статус</th>";

                            if ($stat == 0) {
                                echo "<th>Действия</th>";
                            } else {
                                echo "<th>Действие</th>";
                            }

                            echo "</tr></thead><tbody>";

                            while ($myrow = mysqli_fetch_array($result)) {
                                $appointment_id = $myrow['appointment_id'];
                                $stat_text = ""; 
                                if ($myrow['status'] == 0) {
                                    $stat_text = "Активна";
                                } elseif ($myrow['status'] == 1) {
                                    $stat_text = "Отклонена";
                                } elseif ($myrow['status'] == 2) {
                                    $stat_text = "Выполнена";
                                } else {
                                    $stat_text = "Неизвестный статус";
                                }

                                echo "<tr>";
                                echo "<td>" . $appointment_id . "</td>";
                                echo "<td>" . $myrow['client_name'] . "</td>";
                                echo "<td>" . $myrow['service_name'] . "</td>";
                                echo "<td>" . $myrow['master_name'] . "</td>";
                                echo "<td>" . $myrow['appointment_date'] . "</td>";
                                echo "<td>" . $myrow['appointment_time'] . "</td>";
                                echo "<td>" . $stat_text . "</td>";

                                if ($stat == 0) {
                                    echo "<td>
                                        <form method='POST'>
                                            <button type='submit' name='confirm' class='btn btn-success btn-sm'>Выполнена</button>
                                            <button type='submit' name='reject' class='btn btn-danger btn-sm'>Отменить</button>
                                            <input type='hidden' name='appointment_id' value='$appointment_id'>
                                        </form>
                                    </td>";

} else {
                                    echo "<td>
                                        <form method='POST'>
                                            <button type='submit' name='delete' class='btn btn-danger btn-sm'>Удалить</button>
                                            <input type='hidden' name='appointment_id' value='$appointment_id'>
                                        </form>
                                    </td>";
                                }

                                echo "</tr>"; 
                            } 
                            echo "</tbody></table>";

                            // Пагинация
                            echo '<nav>';
                            echo '<ul class="pagination">';
                            for ($i = 1; $i <= $total_pages; $i++) {
                                $active = ($i == $page) ? 'active' : '';
                                echo "<li class='page-item $active'><a class='page-link' href='?page=$i&stat=$stat'>$i</a></li>";
                            }
                            echo '</ul>';
                            echo '</nav>';
                        }
                    }

                    if (isset($_POST['confirm']) || isset($_POST['reject']) || isset($_POST['delete'])) {
                        if (isset($_POST['confirm']) || isset($_POST['reject'])) {
                            $status = isset($_POST['confirm']) ? 2 : 1;
                            $appointment_id = $_POST['appointment_id'];

                            $sql = "UPDATE appointments SET status=$status WHERE appointment_id=$appointment_id";
                            $result = mysqli_query($db, $sql);
                        } elseif (isset($_POST['delete'])) {
                            $appointment_id = $_POST['appointment_id'];

                            $sql = "DELETE FROM appointments WHERE appointment_id=$appointment_id";
                            $result = mysqli_query($db, $sql);
                        }

                        if ($result == TRUE) {
                            echo "<script>document.location.href = 'manOrders.php?stat=$stat&page=$page'</script>";
                        } else {
                            echo "Ошибка обновления";
                            echo "$sql";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>