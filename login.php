<?php
require_once 'core.php';
require_once 'database.php';
require_once './objects/user.php';

header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"));

if(empty($data)) {
    throwErr("Данные для входа не были переданы!", "LOGIN-2", 400);
} else {
    $user = new User();
    $user->setLogin($data->login);
    $user->setPassword($data->password);

    if($user->auth()) {
        session_start();
        $_SESSION["loggedin"] = true;
        $_SESSION["login"] = $data->login;
        setcookie("u_l", $data->login, time() + COOKIE_LIFE_TIME);
        setcookie("u_key", md5($data->login.SECRET_PHRASE), time() + COOKIE_LIFE_TIME);

        throwSuccess("Вход успешно совершён!", 200);
    } else {
        throwErr("Неверный логин и/или пароль!", "LOGIN-1", 403);
    }
}

