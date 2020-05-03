<?php
    session_start();
    require_once "core.php";
    require_once "database.php";

    $login = "";

    // Session & Cookies validation
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        if(isset($_COOKIE["u_l"]) && isset($_COOKIE["u_key"])) {
            if(md5($_COOKIE["u_l"].SECRET_PHRASE) !== $_COOKIE["u_key"]) {
                session_destroy();
                unset($_COOKIE["u_l"]);
                unset($_COOKIE["u_key"]);
            }
            $login = $_COOKIE["u_l"];
        }
    } else {
        $login = $_SESSION["login"];
        $IS_USER_ACTIVE = getRows("SELECT isActive FROM users WHERE login = :login", array(':login' => $login));
        if($IS_USER_ACTIVE === "0") {
            var_dump($IS_USER_ACTIVE);
            header("Location: logout.php");
        }

        $userRoleId = getRole($login);
    }
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Главная</title>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link href="./css/core.css" rel="stylesheet">
        <link href="./css/index.css" rel="stylesheet">
    </head>
    <body class="preloader-site">

        <!-- ПРЕЛОАДЕР -->
        <div id="preloader">
            <div class="row">
                <div class="d-flex flex-column min-vh-100 mx-auto justify-content-center">
                    <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="sr-only">Загрузка...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- МОДАЛЬНОЕ ОКНО ВХОДА В СИСТЕМУ -->
        <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content rounded-15">
                    <form id="loginForm" class="needs-validation" novalidate>
                        <div class="modal-header">
                            <h5 class="modal-title" id="loginModalTitle">Вход</h5>
                        </div>
                        <div class="modal-body table-responsive w-100">
                            <div id="errHolder"></div>
                            <div class="form-group">
                                <label for="userLogin">Логин</label>
                                <input type="text" maxlength="45" class="form-control" id="userLogin" placeholder="Введите e-mail" required>
                                <div class="invalid-feedback">Пожалуйста, заполните это поле</div>
                            </div>
                            <div class="form-group">
                                <label for="userPassword">Пароль</label>
                                <input type="password" maxlength="45" class="form-control" id="userPassword" placeholder="Введите пароль" required>
                                <div class="invalid-feedback">Пожалуйста, заполните это поле</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Назад</button>
                            <button type="submit" class="btn btn-primary">Войти</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- МОДАЛЬНОЕ ОКНО РЕГИСТРАЦИИ В СИСТЕМЕ -->
        <div class="modal fade" id="regModal" tabindex="-1" role="dialog" aria-labelledby="regModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content rounded-15">
                    <form id="regForm" class="needs-validation" novalidate>
                        <div class="modal-header">
                            <h5 class="modal-title" id="loginModalTitle">Регистрация</h5>
                        </div>
                        <div class="modal-body table-responsive w-100">
                            <div id="errHolderReg"></div>
                            <div class="form-group">
                                <label for="regLogin">Логин</label>
                                <input type="text" maxlength="45" class="form-control" id="regLogin" placeholder="Введите ваш e-mail" required>
                                <div class="invalid-feedback">Пожалуйста, заполните это поле</div>
                            </div>
                            <div class="form-group">
                                <label for="regPassword">Пароль</label>
                                <input type="password" maxlength="45" class="form-control" id="regPassword" placeholder="Введите пароль" required>
                                <div class="invalid-feedback feedbackPwdText">Пожалуйста, заполните это поле</div>
                            </div>
                            <div class="form-group">
                                <input type="password" maxlength="45" class="form-control" id="regPasswordRepeat" placeholder="Повторите пароль" required>
                                <div class="invalid-feedback feedbackPwdText">Пожалуйста, заполните это поле</div>
                            </div>
                            <div class="form-group row">
                                <div class="col">
                                    <label for="regLast">Фамилия</label>
                                    <input type="text" maxlength="100" class="form-control" id="regLast" placeholder="Иванов" required>
                                    <div class="invalid-feedback">Пожалуйста, заполните это поле</div>
                                </div>
                                <div class="col">
                                    <label for="regFirst">Имя</label>
                                    <input type="text" maxlength="100" class="form-control" id="regFirst" placeholder="Иван" required>
                                    <div class="invalid-feedback">Пожалуйста, заполните это поле</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="regMiddle">Отчество (при наличии)</label>
                                <input type="text" maxlength="100" class="form-control" id="regMiddle" placeholder="Иванов">
                                <div class="invalid-feedback">Пожалуйста, заполните это поле</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Назад</button>
                            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


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

                <div class="col-md-10 fill"> <!-- КОНТЕНТ -->
                    <div class="row fill">
                        <div class="col tab-content m-3 shadow-lg rounded-20 scrollable" id="menuTabs">
                            <div class="row overflow-auto"> <!-- Панель навигации -->
                                <div class="col mt-5 mx-5">
                                    <header class="navbar navbar-light flex-column flex-md-row bd-navbar">
                                        <a class="navbar-brand float-left"><h1 id="tabHeader" data-toggle="modal" data-target="#regModal">Главная</h1></a>
                                            <div class="form-inline">
                                                <div class="form-row">
                                                    <button id="regBtn" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#regModal">Зарегистрироваться</button>
                                                    <button id="loginBtn" class="btn btn-info float-right ml-2" data-toggle="modal" data-target="#loginModal">Войти</button>
                                                    <button id="logoutBtn" class="btn btn-danger float-right ml-2" style="display: none;">Выйти</button>
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
                                    <div class="col fill text-center">
                                        <div class="rounded-15 p-4 specCard" style="color: white; background-color: #8ed2db;"> <!-- Карточка -->
                                            <h3><b>Java-Разработчик</b></h3>
                                        </div>
                                        <h1>Нет доступных вакансий</h1>
                                        <img class="mx-auto d-block mt-3" style="max-width: 70%" src="./img/web_development.svg">
                                        <div id="vacanciesContainer" class="grid-box fill"></div>
                                    </div>
                                </div>
                            <!-- ВКЛАДКА ПРОЦЕСС НАЙМА -->
                            <div class="tab-pane fade" id="faq" role="tabpanel" aria-labelledby="faq-tab">
                                <div class="row p-5 d-flex">

                                </div>
                            </div>
                        </div>
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
        <script src="./js/index.js"></script>
    </body>
</html>