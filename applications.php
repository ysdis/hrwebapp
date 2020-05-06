<?php
require_once 'core.php';
require_once 'database.php';
require_once './objects/application.php';

header("Content-Type: application/json; charset=UTF-8");

$validated = validateSessionAPI();

$login = (empty($validated['login'])) ? null : $validated['login'];
$CUR_USER_ROLE = (empty($validated['roleId'])) ? null : $validated['roleId'];

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