<?php
session_start();
include("directorNav.php");
include("db.php");

$total_price = 0; // Переменная для хранения итоговой стоимости всех записей

$limit = 10; // Лимит записей на страницу
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit; // Смещение для SQL-запроса

if (isset($_POST['submit']) || isset($_GET['start_date'])) {
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : $_GET['start_date'];
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : $_GET['end_date'];

    // Запрос для выборки записей текущей страницы с учетом пагинации
    $query = "SELECT services.service_name, masters.master_name, clients.client_name, appointments.appointment_date, services.service_price
              FROM appointments
              INNER JOIN clients ON appointments.client_id = clients.client_id
              INNER JOIN services ON appointments.service_id = services.service_id
              INNER JOIN masters ON appointments.master_id = masters.master_id
              WHERE appointments.appointment_date BETWEEN '$start_date' AND '$end_date'
              AND appointments.status != 1
              LIMIT $limit OFFSET $offset";

    $result = mysqli_query($db, $query);

    if (!$result) {
        echo "Ошибка запроса: " . mysqli_error($db);
    }

    // Запрос для подсчета общей стоимости всех записей
    $count_query = "SELECT SUM(services.service_price) AS total_price
                    FROM appointments
                    INNER JOIN services ON appointments.service_id = services.service_id
                    WHERE appointments.appointment_date BETWEEN '$start_date' AND '$end_date'
                    AND appointments.status != 1";

    $count_result = mysqli_query($db, $count_query);
    if ($count_result) {
        $total_price = mysqli_fetch_assoc($count_result)['total_price'];
    }

    // Получим общее количество записей для пагинации
    $count_query = "SELECT COUNT(*) AS total FROM appointments
                    WHERE appointment_date BETWEEN '$start_date' AND '$end_date'
                    AND status != 1";
    $count_result = mysqli_query($db, $count_query);
    $total_rows = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_rows / $limit);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отчет директора</title>
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
        .pagination {
            justify-content: center;
        }
        .page-item.active .page-link {
            background-color: #ff80ab;
            border-color: #ff80ab;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mt-4 mb-4">Отчет директора</h2>

    <form class="form-inline mb-4" action="" method="POST">
        <label for="start_date" class="mr-2">Отчет за период с:</label>
        <input type="date" id="start_date" name="start_date" class="form-control mr-2" required value="<?php echo isset($start_date) ? $start_date : ''; ?>">
        <label for="end_date" class="mr-2">по:</label>
        <input type="date" id="end_date" name="end_date" class="form-control mr-2" required value="<?php echo isset($end_date) ? $end_date : ''; ?>">
        <button type="submit" class="btn btn-primary" name="submit">Сформировать отчет</button>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Услуга</th>
                    <th>Специалист</th>
                    <th>Клиент</th>
                    <th>Дата</th>
                    <th>Стоимость</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($result)) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['service_name'] . "</td>";
                        echo "<td>" . $row['master_name'] . "</td>";
                        echo "<td>" . $row['client_name'] . "</td>";
                        echo "<td>" . $row['appointment_date'] . "</td>";
                        echo "<td>" . $row['service_price'] . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"><strong>Итого:</strong></td>
                    <td><strong><?php echo $total_price; ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <?php if (isset($total_pages) && $total_pages > 1): ?>
        <nav aria-label="Пагинация">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
