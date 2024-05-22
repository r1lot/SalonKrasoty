<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/style2.css" type="text/css">
    <link rel="stylesheet" href="CSS/bootstrap.css">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Oswald:400,300" type="text/css">

    <title>Личный кабинет</title>
</head>
<body>
    <!-- nav bar -->
<nav class="navbar navbar-expand-lg brown_panel">
  <a class="navbar-brand" href="index.html">Салон красоты</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-4">
      <li class="nav-item">
        <a class="nav-link" href="client.php">Редактировать профиль</a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="services.php">Запись на услугу</a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="clientOrders.php">Мои заявки</a>
      </li>

      <li class="nav-item ">
        <a class="nav-link" href="index.html">Выход</a>
      </li>
    </ul>
  </div>
</nav>
<div>
        <?php
        $client_id=$_SESSION['client_id'];
        
        ?>
     </div> 


</body>
</html>