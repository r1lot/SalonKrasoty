<?php
$servername = "127.0.0.1:3306"; //хост
$database = "Salon_Krasoty"; //имя базы данных
$user = "root"; //имя пользователя
$password = ""; //пароль
// Создаем соединение
$db = mysqli_connect($servername, $user, $password, $database);
//Проверяем соединение, если подключение не выполнено, сообщение об ошибке и прекращение работы скрипта
if (!$db) {
    die("Connection failed: " .mysqli_connect_error());
}

?>