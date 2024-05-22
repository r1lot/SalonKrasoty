<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/Style2.css" type="text/css">
    <link rel="stylesheet" href="CSS/bootstrap.css">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Oswald:400,300" type="text/css">
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <!-- Bootstrap Bundle JS (Cloudflare CDN) -->
  <script defer src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/js/bootstrap.min.js" integrity="sha512-UR25UO94eTnCVwjbXozyeVd6ZqpaAE9naiEUBK/A+QDbfSTQFhPGj5lOR6d8tsgbBk84Ggb5A3EkjsOgPRPcKA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <title>Личный кабинет менеджера</title>
</head>
<body>
    <!-- nav bar -->
<nav class="navbar navbar-expand-lg brown_panel">
  <a class="navbar-brand" href="manOrders.php">Личный кабинет менеджера</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-4">
      <li class="nav-item">
        <a class="nav-link" href="manOrders.php">Заявки</a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="serviceList.php">Услуги</a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="clientList.php">Клиенты</a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="masterList.php">Мастера</a>
      </li>

      <li class="nav-item ">
        <a class="nav-link" href="reports.php">Отчеты</a>
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