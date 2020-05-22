<?php
require_once 'core.php';
require_once './objects/User.php';

$msg = "";
$err = 0;

if(!empty($_GET['code']) && $_GET['email']) {
    if(md5(urlencode($_GET['email']).SECRET_PHRASE) === $_GET['code']) {
        $user = new User($_GET['email']);
        if(getRows("SELECT emailVerified FROM users WHERE login = :LOGIN", array(":LOGIN" => $user->getLogin())) === "1") {
            header("Location: controlPanel.php");
        }
        if($user->verify()) {
            session_start();
            $_SESSION["loggedin"] = true;
            $_SESSION["login"] = $user->getLogin();
            setcookie("u_l", $user->getLogin(), time() + COOKIE_LIFE_TIME);
            setcookie("u_key", createCookieKey($user->getLogin()), time() + COOKIE_LIFE_TIME);
            $msg = "Электронная почта <b>".urldecode($_GET['email'])."</b> успешно подтверждена!<br>Перенаправление в личный кабинет...";
        } else {
            $msg = 'Верификация почты не может быть закончена, повторите попытку позднее!';
            $err = 1;
        }
    } else {
        $msg = 'Неверные активационные данные. Верификация электронной почты невозможна!';
        $err = 2;
    }
} else {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Активация аккаунта</title>
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
                        <h5 class="card-header">Подтверждение почты</h5>
                        <div class="card-body">
                            <div id="errHolderReg"></div>
                            <p class="card-text"><?php echo $msg ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include('./templates/jsImports.php'); ?>

        <script>
            hidePreloader()
            if(<?php echo $err ?> === 0)  {
                setTimeout(function() {
                    window.location.href = "./controlPanel.php"
                }, 3500);
            }
        </script>
    </body>
</html>
