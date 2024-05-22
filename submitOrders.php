<?php
session_start();
include("db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_SESSION['client_id'];
    $service_id = (int)$_POST['service_id'];
    $appointment_date = mysqli_real_escape_string($db, $_POST['appointment_date']);
    $appointment_time = mysqli_real_escape_string($db, $_POST['appointment_time']);
    $master_id = (int)$_POST['master_id'];

    // Validate that the date is not in the past
    if (new DateTime($appointment_date) < new DateTime('today')) {
        $_SESSION['error'] = 'Невозможно записаться на прошедшую дату.';
        header('Location: services.php');
        exit();
    }

    // Check if the appointment slot is already taken
    $sql = "SELECT COUNT(*) as count FROM appointments WHERE master_id = $master_id AND appointment_date = '$appointment_date' AND appointment_time = '$appointment_time'";
    $result = mysqli_query($db, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] > 0) {
        $_SESSION['error'] = 'Данное время занято. Пожалуйста, выберите другое время.';
        header('Location: services.php');
        exit();
    }

    // Insert the appointment
    $sql = "INSERT INTO appointments (client_id, service_id, appointment_date, appointment_time, master_id) VALUES ($client_id, $service_id, '$appointment_date', '$appointment_time', $master_id)";
    if (mysqli_query($db, $sql)) {
        $_SESSION['success'] = 'Запись успешно создана.';
    } else {
        $_SESSION['error'] = 'Ошибка: ' . mysqli_error($db);
    }

    header('Location: services.php');
    exit();
}
?>
