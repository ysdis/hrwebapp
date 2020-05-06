<?php
    require_once "core.php";
    require_once "Database.php";

    $validated = validateSessionFRONT();

    $login = (empty($validated['login'])) ? null : $validated['login'];
    $CUR_USER_ROLE = (empty($validated['roleId'])) ? null : $validated['roleId'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="./css/core.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="preloader-site">

<?php include('./templates/preloader.php') ?>

<!-- КОНТЕЙНЕР ДЛЯ НАВИГАЦИИ И ВКЛАДОК С КОНТЕНТОМ СТРАНИЦЫ -->
<div class="container-fluid vh-100">
    <div class="row fill">
        <div class="col-md-2"> <!-- НАВИГАЦИЯ -->
            <div class="row">
                <div class="col-lg-12 mx-2 my-4"><img class="mx-auto d-block" width="80" src="./img/logo.png" alt="ВИ6 ИНТЕГРЕЙШН"></div>
                <div class="col mx-md-auto mx-lg-auto my-2">
                    <ul class="nav nav-pills mr-md-2 justify-content-center flex-column" id="tabsList" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Главная</a>
                        </li>
                        <!-- РАЗНЫЕ ВКЛАДКИ ДЛЯ РАЗНЫХ РОЛЕЙ -->
                        <!-- СОИСКАТЕЛЬ: ГЛАВНАЯ (Список поданных заявок и их статус), ВАКАНСИИ, НАСТРОЙКИ -->
                        <!-- СОТРУДНИК: ГЛАВНАЯ (Список пройденных тестирований и их статус), ТЕСТЫ, НАСТРОЙКИ -->
                        <!-- СОТРУДНИК: ГЛАВНАЯ (Список поданных заявок от соискателей, отсортированный по релевантности со шкалой), ТЕСТЫ (Создание, управление), ВАКАНСИИ (Создание, управление), НАСТРОЙКИ -->
                        <li class="nav-item">
                            <a href="./index.php" class="nav-link" aria-selected="false">На сайт компании</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-10 fill"> <!-- КОНТЕНТ -->
            <div class="row fill">
                <div class="col tab-content m-3 shadow-lg rounded-20 scrollable" id="menuTabs">

                    <?php include('./templates/contentHeader.php'); ?>

                    <!-- ВКЛАДКА ГЛАВНАЯ -->
                    <?php include('./templates/adminHome.php'); ?>

                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.5.0.min.js" integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script src="./js/core.js"></script>

<?php
if(!empty($login)) {
    echo '<script>currentUserLogin = "'.$login.'";</script>';
}
?>

<script src="./js/controlPanel.js"></script>
</body>
</html>