<?php
include("manNav.php");
include("db.php");

// Параметры пагинации
$limit = 10; // Лимит записей на страницу

// Получаем список мастеров
$query_masters = "SELECT * FROM masters";
$result_masters = mysqli_query($db, $query_masters);

// Обработка нажатия кнопки "Сформировать отчет о занятости специалиста"
if (isset($_POST['submit_appointments']) || isset($_GET['master_id1'])) {
    $master_id1 = isset($_POST['master_id1']) ? $_POST['master_id1'] : $_GET['master_id1'];
    $selected_date1 = isset($_POST['selected_date1']) ? $_POST['selected_date1'] : $_GET['selected_date1'];
    $page1 = isset($_GET['page1']) ? (int)$_GET['page1'] : 1;
    $offset1 = ($page1 - 1) * $limit;

    // Запрос для выборки данных с учетом пагинации и исключением заявок со статусом 1
    $query_report_appointments = "SELECT clients.client_name, services.service_name, appointments.appointment_date, appointments.appointment_time, 
    CASE
        WHEN appointments.status = 0 THEN 'Активна'
        WHEN appointments.status = 1 THEN 'Отклонена'
        WHEN appointments.status = 2 THEN 'Выполнена'
        ELSE 'Неизвестный статус'
    END AS status_text
    FROM appointments
    INNER JOIN clients ON appointments.client_id = clients.client_id
    INNER JOIN services ON appointments.service_id = services.service_id
    WHERE appointments.master_id = '$master_id1' AND appointments.appointment_date = '$selected_date1' AND appointments.status != 1
    LIMIT $limit OFFSET $offset1";
    $result_report_appointments = mysqli_query($db, $query_report_appointments);
// Запрос для подсчета общего количества записей
$count_query_appointments = "SELECT COUNT(*) AS total_appointments
FROM appointments
WHERE master_id = '$master_id1' AND appointment_date = '$selected_date1' AND status != 1";
$count_result_appointments = mysqli_query($db, $count_query_appointments);
$total_appointments = mysqli_fetch_assoc($count_result_appointments)['total_appointments'];
$total_pages1 = ceil($total_appointments / $limit);
}

// Обработка нажатия кнопки "Сформировать отчет об оказании услуг мастером"
if (isset($_POST['submit_services']) || isset($_GET['master_id2'])) {
$master_id2 = isset($_POST['master_id2']) ? $_POST['master_id2'] : $_GET['master_id2'];
$page2 = isset($_GET['page2']) ? (int)$_GET['page2'] : 1;
$offset2 = ($page2 - 1) * $limit;

// Запрос для выборки данных с учетом пагинации и исключением заявок со статусом 1
$query_report_services = "SELECT appointments.appointment_date, services.service_name, clients.client_name, services.service_price
FROM appointments
INNER JOIN clients ON appointments.client_id = clients.client_id
INNER JOIN services ON appointments.service_id = services.service_id
WHERE appointments.master_id = '$master_id2' AND appointments.status != 1
LIMIT $limit OFFSET $offset2";
$result_report_services = mysqli_query($db, $query_report_services);

// Запрос для подсчета общего количества записей
$count_query_services = "SELECT COUNT(*) AS total_services
FROM appointments
WHERE master_id = '$master_id2' AND status != 1";
$count_result_services = mysqli_query($db, $count_query_services);
$total_services = mysqli_fetch_assoc($count_result_services)['total_services'];
$total_pages2 = ceil($total_services / $limit);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Отчеты</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/css/bootstrap.min.css" integrity="sha512-MFT3h9KDR/Uo3NYaUuLzM67YvSpUfXhMu80vERPrJ4mICv7WtjrLKfDyQoJlrDik2QrKc5K0XxhxWxDV90uF0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
body {
background-color: #fce4ec; /* Розовый фон */
font-family: 'Arial', sans-serif;
color: #444;
}
.container {
margin-top: 20px;
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
.form-control {
font-size: 16px;
padding: 12px;
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
<div class="row justify-content-center">
<div class="col-md-6">
<form method="post" action="">
<h3>Сформировать отчет о занятости специалиста</h3>
<div class="form-group">
<label for="master_id1">Выберите мастера:</label>
<select id="master_id1" name="master_id1" class="form-control">
<?php
while ($row = mysqli_fetch_assoc($result_masters)) {
echo "<option value='" . $row['master_id'] . "'>" . $row['master_name'] . "</option>";
}
?>
</select>
</div>
<div class="form-group">
<label for="selected_date1">Выберите дату:</label>
<input type="date" id="selected_date1" name="selected_date1" class="form-control">
</div>
<button type="submit" name="submit_appointments" class="btn btn-primary">Сформировать отчет</button>
</form>
</div>
</div>
<div class="row justify-content-center">
<div class="col-md-6">
<form method="post" action="">
<h3>Сформировать отчет об оказании услуг мастером</h3>
<div class="form-group">
<label for="master_id2">Выберите мастера:</label>
<select id="master_id2" name="master_id2" class="form-control">
<?php
mysqli_data_seek($result_masters, 0); // Возвращаем указатель результата запроса к началу
while ($row = mysqli_fetch_assoc($result_masters)) {
echo "<option value='" . $row['master_id'] . "'>" . $row['master_name'] . "</option>";
}
?>
</select>
</div>
<button type="submit" name="submit_services" class="btn btn-primary">Сформировать отчет</button>
</form>
</div>
</div>
<div class="row justify-content-center">
<div class="col-md-12">
<div class="table-responsive">
<?php
if (isset($result_report_appointments) && $result_report_appointments && mysqli_num_rows($result_report_appointments) > 0) {
echo "<h3>Отчет о занятости специалиста</h3>";
echo "<table class='table table-bordered'>";
echo "<thead>";
echo "<tr>";
echo "<th>Клиент</th>";
echo "<th>Услуга</th>";
echo "<th>Дата</th>";
echo "<th>Время</th>";
echo "<th>Статус</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
while ($row = mysqli_fetch_assoc($result_report_appointments)) {
echo "<tr>";
echo "<td>" . $row['client_name'] . "</td>";
echo "<td>" . $row['service_name'] . "</td>";
echo "<td>" . $row['appointment_date'] . "</td>";
echo "<td>" . $row['appointment_time'] . "</td>";
echo "<td>" . $row['status_text'] . "</td>";
echo "</tr>";
}
echo "</tbody>";
echo "</table>";

// Пагинация
echo '<nav>';
echo '<ul class="pagination">';
for ($i = 1; $i <= $total_pages1; $i++) {
$active = ($i == $page1) ? 'active' : '';
echo "<li class='page-item $active'><a class='page-link' href='?page1=$i&master_id1=$master_id1&selected_date1=$selected_date1'>$i</a></li>";
}
echo '</ul>';
echo '</nav>';
} elseif (isset($result_report_appointments) && !$result_report_appointments) {
echo "Ошибка при формировании отчета: " . mysqli_error($db);
}

if (isset($result_report_services) && $result_report_services && mysqli_num_rows($result_report_services) > 0) {
$total_price = 0; // Обнуляем значение total_price
echo "<h3>Сведенья об оказании услуг мастером</h3>";
echo "<table class='table table-bordered'>";
echo "<thead>";
echo "<tr>";
echo "<th>Дата</th>";
echo "<th>Наименование услуги</th>";
echo "<th>ФИО клиента</th>";
echo "<th>Стоимость</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
while ($row = mysqli_fetch_assoc($result_report_services)) {
$total_price += $row['service_price']; // Суммируем стоимость услуг
echo "<tr>";
echo "<td>" . $row['appointment_date'] . "</td>";
echo "<td>" . $row['service_name'] . "</td>";
echo "<td>" . $row['client_name'] . "</td>";
echo "<td>" . $row['service_price'] . "</td>";
echo "</tr>";
}
echo "<tr><td colspan='3' class='text-right'><strong>Итого:</strong></td><td><strong>$total_price</strong></td></tr>";
echo "</tbody>";
echo "</table>";

// Пагинация
echo '<nav>';
echo '<ul class="pagination">';
for ($i = 1; $i <= $total_pages2; $i++) {
$active = ($i == $page2) ? 'active' : '';
echo "<li class='page-item $active'><a class='page-link' href='?page2=$i&master_id2=$master_id2'>$i</a></li>";
}
echo '</ul>';
echo '</nav>';
} elseif (isset($result_report_services) && !$result_report_services) {
echo "Ошибка при формировании отчета: " . mysqli_error($db);
}
?>
</div>
</div>
</div>
</div>
</body>
</html>

