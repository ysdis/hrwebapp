<?php
    require_once "core.php";
    require_once "Database.php";

    $validated = validateSessionFRONT();

    $login = (empty($validated['login'])) ? null : $validated['login'];
    $CUR_USER_ROLE = (empty($validated['roleId'])) ? null : $validated['roleId'];

    if($CUR_USER_ROLE !== ADMIN) {
        header('Location: ./controlPanel.php');
    }
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создание новой формы</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="./css/core.css" rel="stylesheet">
    <link href="./css/formBuilder.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="preloader-site">

<?php include('./templates/preloader.php') ?>

<?php include('./templates/acceptModal.php') ?>

<!-- КОНТЕЙНЕР ДЛЯ НАВИГАЦИИ И ВКЛАДОК С КОНТЕНТОМ СТРАНИЦЫ -->
<div class="container-fluid vh-100">
    <div class="row fill">
        <div class="col m-3 shadow-lg rounded-20 scrollable">
            <div class="row px-3 pt-3"> <!-- КОНТЕНТ -->
                <div class="col-12 rounded-15 shadow" style="color: white; background: linear-gradient(25deg, #4b6cb7 0%, #182848 100%);"> <!-- ЗАГОЛОВОК -->
                    <div class="row p-4">
                        <div class="col-1 px-0 my-auto">
                            <a style="color: white" href="./controlPanel.php"><i  class="zoom fa fa-angle-left fa-3x"></i></a>
                        </div>
                        <div class="col-11 my-auto text-center">
                            <input id="formNameInput" class="form-control rounded-10" type="text" maxlength="144" style="font-size: 1.5rem" placeholder="Новая форма"></input>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row px-3 pt-3">
                <div class="col-12 p-3 rounded-15 shadow"> <!-- СПИСОК СОЗДАННЫХ ВОПРОСОВ -->
                    <div class="row">
                        <div class="col-12">
                            <h4>Настройки</h4>
                        </div>
                        <div class="col-lg-4 col-md-12">
                            <label for="specialtySelect">Для специальности:</label>
                            <?php echo createSelect("SELECT id, name FROM specialties WHERE isOpen = 1;", "id", "name", "specialtySelect") ?>
                        </div>
                        <div class="col-lg-4 col-md-12 mt-3 mt-lg-0">
                            <label for="formTypeSelect">Тип формы:</label>
                            <?php echo createSelect("SELECT id, title FROM formTypes;", "id", "title", "formTypeSelect") ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row p-3">
                <div id="questionLinksHolder" class="col-lg-2 col-md-3 p-3 rounded-15 shadow"> <!-- СПИСОК СОЗДАННЫХ ВОПРОСОВ -->
                    <div class="row px-4">
                        <div id="addQuestionBtn" class="col-12 text-center p-3 my-2 rounded-15 border border-info">
                            <span>Добавить вопрос</span>
                        </div>
                        <div id="saveFormBtn" class="col-12 p-2 my-2 text-center border border-success rounded-15">
                            <span>Сохранить форму</span>
                        </div>
                    </div>
                </div>
                <div id="questionsCardsHolder" class="col-lg-10 col-md-9 pt-3 pt-md-0 pl-md-3 px-0 grid-box-fill-parent"> <!-- СПИСОК СОЗДАННЫХ ВОПРОСОВ -->
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.5.0.min.js" integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script defer src="./js/all.js"></script>
<script src="./js/core.js"></script>
<script src="./js/formBuilder.js"></script>

<?php
if(!empty($login)) {
    echo '<script>currentUserLogin = "'.$login.'";</script>';
}
?>
</body>
</html>