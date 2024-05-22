
<?php
session_start();
include("db.php");
include("directorNav.php");

$total_price = 0;
$total_service_count = 0;

$limit = 10; // Лимит записей на страницу
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

if (isset($_POST['submit']) || isset($_GET['start_date'])) {
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : $_GET['start_date'];
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : $_GET['end_date'];

    // Запрос для выборки данных с учетом пагинации и исключением заявок со статусом 1
    $query = "SELECT services.service_name, COUNT(*) AS service_count, SUM(services.service_price) AS total_price
              FROM appointments
              INNER JOIN services ON appointments.service_id = services.service_id
              WHERE appointments.appointment_date BETWEEN '$start_date' AND '$end_date'
              AND appointments.status != 1
              GROUP BY services.service_id
              LIMIT $limit OFFSET $offset";
    $result = mysqli_query($db, $query);

    // Запрос для подсчета общего количества записей и общей суммы
    $count_query = "SELECT COUNT(DISTINCT services.service_id) AS total_services, SUM(services.service_price) AS total_price, COUNT(appointments.appointment_id) AS total_service_count
                    FROM appointments
                    INNER JOIN services ON appointments.service_id = services.service_id
                    WHERE appointments.appointment_date BETWEEN '$start_date' AND '$end_date'
                    AND appointments.status != 1";
    $count_result = mysqli_query($db, $count_query);
    if ($count_result) {
        $count_row = mysqli_fetch_assoc($count_result);
        $total_services = $count_row['total_services'];
        $total_price = $count_row['total_price'];
        $total_service_count = $count_row['total_service_count'];
        $total_pages = ceil($total_services / $limit);
    }

    if (!$result) {
        echo "Ошибка запроса: " . mysqli_error($db);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отчет об объемах оказанных услуг</title>
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
        canvas {
            width: 100%; /* Растягиваем canvas на всю ширину */
            height: auto; /* Автоматическая высота */
            max-width: 1200px; /* Максимальная ширина canvas */
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mt-4 mb-4">Отчет об объемах оказанных услуг</h2>

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
                    <th>Количество</th>
                    <th>Общая стоимость</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($result)) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['service_name'] . "</td>";
                        echo "<td>" . $row['service_count'] . "</td>";
                        echo "<td>" . $row['total_price'] . "</td>";
                        echo "</tr>";
                    }

                    // Итоговая строка
                    echo "<tr>";
                    echo "<td><strong>Итого</strong></td>";
                    echo "<td><strong>$total_service_count</strong></td>";
                    echo "<td><strong>$total_price</strong></td>";
                    echo "</tr>";
                } else {
                    echo "<tr><td colspan='3'>Нет данных для отображения</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Пагинация -->
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

    <!-- Добавляем график -->
    <div class="mt-4">
        <canvas id="serviceChart"></canvas>
    </div>
</div>

<!-- Подключаем библиотеку Chart.js для построения графика -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script>
    // Получаем данные из PHP для построения графика
    let serviceNames = [];
    let serviceCounts = [];
    <?php
    if (isset($result)) {
        mysqli_data_seek($result, 0); // Возвращаем указатель результата на начало
        while ($row = mysqli_fetch_assoc($result)) {
            echo "serviceNames.push('" . $row['service_name'] . "');";
            echo "serviceCounts.push(" . $row['service_count'] . ");";
        }
    }
    ?>

    // Создаем график
    var ctx = document.getElementById('serviceChart').getContext('2d');
    var serviceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: serviceNames,
            datasets: [{
                label: 'Количество услуг',
                data: serviceCounts,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>