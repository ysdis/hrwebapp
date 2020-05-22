<?php
    require_once "core.php";

    $validated = validateSessionFRONT(true);

    $login = (empty($validated['login'])) ? null : $validated['login'];
    $CUR_USER_ROLE = (empty($validated['roleId'])) ? null : $validated['roleId'];

?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Доступ воспрещён</title>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link href="./css/core.css" rel="stylesheet">
    </head>
    <body class="preloader-site">

        <?php include('./templates/preloader.php') ?>
        <div class="container-fluid h-100">
            <div class="row h-100">
                <div class="col-12 col-md-7 d-flex flex-column min-vh-100 mx-auto justify-content-center">
                    <div class="card rounded-15 my-auto">
                        <h5 class="card-header">Электронная почта ожидает подтверждения!</h5>
                        <div class="card-body">
                            <div id="errHolderReg"></div>
                            <p class="card-text">Пока электронная почта не подтверждена воспользоваться аккаунтом невозможно!</p>
                            <a href="./index.php" class="btn btn-primary">На главную</a>
                            <a href="#" id="sendAgain" class="btn btn-success">Отправить код повторно</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include('./templates/jsImports.php'); ?>

        <script>
            showPreloader()
            $('#sendAgain').on('click', function () {
                ajax(
                    "./sendVerificationEmail.php",
                    "GET",
                    null,
                    function () {
                        $('#sendAgain').attr('enabled', false)
                        showAlert('Электронное сообщение с ссылкой для активации успешно отправлено! Проверьте вашу почту.', 'success', 0, $('#errHolderReg'), true);
                        hidePreloader();
                    },
                    function (data) {
                        if(data.errorCode === "SEND-VERIFY-3") {
                            window.location.href = "./controlPanel.php";
                        }
                        showAlert(data.message, 'danger', 0, $('#errHolderReg'), true);
                        hidePreloader();
                    }
                )
            })
        </script>
    </body>
</html>
