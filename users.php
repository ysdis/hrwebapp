<?php
require_once 'core.php';
require_once 'database.php';
require_once './objects/user.php';

header("Content-Type: application/json; charset=UTF-8");

$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if(strcasecmp($contentType, 'application/json') != 0 && $_SERVER["REQUEST_METHOD"] !== "GET") {
    throwErr('Тип контента должен быть application/json', "USERS-0", 400);
}

$login = "";
$CUR_USER_ROLE = 0;

// Session & Cookies validation
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    if(isset($_COOKIE["u_l"]) && isset($_COOKIE["u_key"])) {
        if(md5($_COOKIE["u_l"].SECRET_PHRASE) !== $_COOKIE["u_key"]) {
            session_destroy();
            unset($_COOKIE["u_l"]);
            unset($_COOKIE["u_key"]);
            throwErr("Обнаружена ошибка, попробуйте зайти в систему заново..", "COOKIE-VIOLATION", 403);
        } else {
            $login = $_COOKIE["u_l"];
        }
    }
} else {
    $login = $_SESSION["login"];
}

if(!empty($login)) {
    $CUR_USER_ROLE = getRole($login);
}

$data = json_decode(file_get_contents("php://input"));

if(empty($data) && $_SERVER["REQUEST_METHOD"] !== "GET") {
    throwErr("Данные не были переданы!", "USERS-2", 400);
} else {
    switch($_SERVER["REQUEST_METHOD"]) {
        case 'GET':
            if (isset($_GET['login'])) { // GET USER BY ID
                if ($userLogin = filter_var($_GET['login'], FILTER_SANITIZE_STRING)) {
                    if ($login !== $userLogin) {
                        if ($CUR_USER_ROLE !== ADMIN) {
                            throwErr("Обнаружена ошибка, попробуйте зайти в систему заново..", "3000-PERMISSION-DENIED", 403);
                        }
                    }

                    $user = new User($userLogin);
                    echo json_encode(array(
                        "login" => $user->getLogin(),
                        "lastName" => $user->getLastName(),
                        "firstName" => $user->getFirstName(),
                        "middleName" => $user->getMiddleName(),
                        "roleId" => $user->getRoleId(),
                        "isActive" => $user->getIsActive(),
                        "specialtyId" => $user->getSpecialtyId()
                    ), JSON_UNESCAPED_UNICODE);
                    break;
                }
            } else { // GET ALL USERS
                if($CUR_USER_ROLE !== ADMIN) throwErr("Доступ воспрещён!", "PERMISSION_DENIED_USERS_GET", 403);
                echo json_encode(getRows(
                    'SELECT *
                        FROM
                            users;'));
                // TODO: Необходимо доделать
            }
            break;
        case "POST":
            $user = new User();
            $user->setLogin($data->login);
            $user->setPassword($data->password);
            $user->setLastName($data->lastName);
            $user->setFirstName($data->firstName);
            $user->setMiddleName($data->middleName);

            if($user->create()) {
                session_start();
                $_SESSION["loggedin"] = true;
                $_SESSION["login"] = $data->login;
                setcookie("u_l", $data->login, time() + COOKIE_LIFE_TIME);
                setcookie("u_key", md5($data->login.SECRET_PHRASE), time() + COOKIE_LIFE_TIME);

                throwSuccess("Пользователь успешно зарегистрирован!", 201);
            } else {
                throwErr("Регистрация провалилась!", "REGISTER-1", 403);
            }
    }
}

