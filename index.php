<?php
    session_start();
    require_once "base.php";
    require_once "database.php";

    $login = "";

    // Session & Cookies validation
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        if(isset($_COOKIE["u_l"]) && isset($_COOKIE["u_key"])) {
            if(md5($_COOKIE["u_l"].$secretPhrase) !== $_COOKIE["u_key"]) {
                session_destroy();
                unset($_COOKIE["u_l"]);
                unset($_COOKIE["u_key"]);
            }
            $login = $_COOKIE["u_l"];
        }
    } else {
        $login = $_SESSION["login"];
    }

    $userRoleId = getRole($login);
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Главная</title>
        <link rel="stylesheet" href="css/bootstrap.css">
        <link href="./css/core.css" rel="stylesheet">
        <link href="./css/index.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.css"/>
    </head>
    <body class="preloader-site" data-simplebar>

        <!-- ПРЕЛОАДЕР -->
        <div id="preloader">
            <div class="row">
                <div class="d-flex flex-column min-vh-100 mx-auto justify-content-center">
                    <div class="spinner-grow text-danger" style="width: 3rem; height: 3rem;" role="status">
                        <span class="sr-only">Загрузка...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- КОНТЕЙНЕР ДЛЯ НАВИГАЦИИ И ВКЛАДОК С КОНТЕНТОМ СТРАНИЦЫ -->
        <div class="container-fluid vh-100">
            <div class="row fill">
                <div class="col-md-2">
                    <div class="row">
                        <div class="col-lg-12 mx-2 my-4"><img class="mx-auto d-block" width="80" src="./img/logo.png" alt="ВИ6 ИНТЕГРЕЙШН"></div>
                        <div class="col mx-md-auto mx-lg-auto my-2">
                            <ul class="nav nav-pills mr-md-2 justify-content-center flex-column" id="tabsList" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Главная</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="vacancies-tab" data-toggle="tab" href="#vacancies" role="tab" aria-controls="vacancies" aria-selected="true">Вакансии</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="vacancies-tab" data-toggle="tab" href="#faq" role="tab" aria-controls="faq" aria-selected="true">Процесс найма</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-10 fill">
                    <div class="row fill">
                        <div class="col tab-content m-3 shadow-lg rounded-20 scrollable" id="menuTabs">
                            <div class="row overflow-auto"> <!-- Панель навигации -->
                                <div class="col mt-5 mx-5">
                                    <header class="navbar navbar-light flex-column flex-md-row bd-navbar">
                                        <a class="navbar-brand float-left"><h1 id="cardHeader">Главная</h1></a>
                                            <div class="form-inline">
                                                <div class="form-row">
                                                    <button class="btn btn-outline-primary float-right">Зарегистрироваться</button>
                                                    <button class="btn btn-info float-right ml-2">Войти</button>
                                                </div>
                                            </div>
                                    </header>
                                </div>
                            </div>

                            <!-- ВКЛАДКА ГЛАВНАЯ -->
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <div class="row p-5"> <!-- Панель контента -->
                                    <div class="col-lg-7">
                                        <div class="col rounded-15 box fill" style="color: white; background-color: #8ed2db; min-width: 430px;"> <!-- Карточка -->
                                            <div class="row fill">
                                                <div class="col p-4" style="z-index: 1;">
                                                    <h2><b>Получи должность <br>в V6 ИНТЕГРЕЙШН<br>уже сейчас</b></h2>
                                                    <p>Ответь на несколько вопросов в новом опроснике</p>
                                                    <button class="btn btn-light">Начать</button>
                                                </div>
                                                <div class="col-5 m-0 p-0 rounded-15" style="z-index: 0; background: url('./img/remote-team.svg') no-repeat; background-size: auto 350px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="col rounded-15 box fill" style="color: white; background-color: #82a1e7;"> <!-- Карточка -->
                                            <div class="row fill">
                                                <div class="col p-4 rounded-15" style="background: url('./img/Business.svg') no-repeat; background-size: auto 100%; background-position: calc(100% + 100px) calc(100% + 100px); ">
                                                    <h2><b>Узнай всё о компании V6</b></h2>
                                                    <p>Контакты, партнёры, цели</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <div class="col rounded-15 box fill" style="color: white; background-color: #0c7b93;"> <!-- Карточка -->
                                            <div class="row fill">
                                                <div class="col p-4 rounded-15" style="background: url('./img/problem_solving.svg') no-repeat; background-size: auto 250%; background-position: calc(100% - 5%) calc(100% - 70%); ">
                                                    <h2><b>Наши проекты</b></h2>
                                                    <p>То, чем гордимся мы и то, чем гордятся компании</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ВКЛАДКА ВАКАНСИИ -->
                            <div class="tab-pane fade show" id="vacancies" role="tabpanel" aria-labelledby="vacancies-tab">
                                <div class="row px-5 pt-5 pb-4 d-flex">
                                    <div class="col-lg-7 mx-auto">
                                        <input id="searchInput" class="form-control w-100" type="search" placeholder="Поиск по вакансиям" aria-label="Поиск">
                                    </div>
                                </div>
                                <div class="row px-5 py-4">
                                    <div class="col">
                                        <div class="grid-box fill">
                                            <div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div>
                                            <div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div>
                                            <div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div><div class="rounded-15 p-4" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                                <h3><b>Java-Разработчик</b></h3>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <!-- ВКЛАДКА ПРОЦЕСС НАЙМА -->
                            <div class="tab-pane fade show" id="faq" role="tabpanel" aria-labelledby="faq-tab">
                                <div class="row p-5 d-flex">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.min.js"></script>
        <!--    <script>--><?php //echo 'var userLogin = "'.$login.'";' ?><!--</script>-->
        <script src="./js/core.js"></script>
        <!--<script src="./js/index.js"></script>-->
    </body>
</html>