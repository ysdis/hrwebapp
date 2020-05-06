<?php
require_once 'Database.php';

error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 'on');

function throwErr($message, $errCode, $responseCode) {
    http_response_code($responseCode);
    echo json_encode(array("message" => $message, "errorCode" => $errCode), JSON_UNESCAPED_UNICODE);
    die();
}

function throwSuccess($message, $responseCode) {
    http_response_code($responseCode);
    echo json_encode(array("message" => $message), JSON_UNESCAPED_UNICODE);
    die();
}

function validateSessionAPI() { // Session & Cookies validation

    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if(strcasecmp($contentType, 'application/json') != 0 && $_SERVER["REQUEST_METHOD"] !== "GET") {
        throwErr('Тип контента должен быть application/json', "USERS-0", 400);
    }

    $login = "";
    session_start();
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        if(isset($_COOKIE["u_l"]) && isset($_COOKIE["u_key"])) {
            if(md5($_COOKIE["u_l"].SECRET_PHRASE) !== $_COOKIE["u_key"]) {
                session_destroy();
                unset($_COOKIE["u_l"]);
                unset($_COOKIE["u_key"]);
                throwErr("Обнаружена ошибка, попробуйте зайти в систему заново..", "SPEC-COOKIE-VIOLATION", 403);
            } else {
                $login = $_COOKIE["u_l"];
            }
        }
    } else {
        $login = $_SESSION["login"];
    }

    if(!empty($login)) {
        return array("roleId" => getRole($login), "login" => $login);
    }

    return null;
}

function validateSessionFRONT() { // Session & Cookies validation
    session_start();
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        if(isset($_COOKIE["u_l"]) && isset($_COOKIE["u_key"])) {
            if(md5($_COOKIE["u_l"].SECRET_PHRASE) !== $_COOKIE["u_key"]) {
                session_destroy();
                unset($_COOKIE["u_l"]);
                unset($_COOKIE["u_key"]);
                header("Location: logout.php");
                exit;
            }
            return array("login" => $_COOKIE["u_l"], "roleId" => getRole($_COOKIE["u_l"]));
        } else {
            if($_SERVER['PHP_SELF'] !== "/hrwebapp/index.php" && $_SERVER['PHP_SELF'] !== "index.php") {
                header("location: index.php");
                exit;
            }
        }
    } else {
        $IS_USER_ACTIVE = getRows("SELECT isActive FROM users WHERE login = :login", array(':login' => $_SESSION["login"]));
        if($IS_USER_ACTIVE === "0") {
            var_dump($IS_USER_ACTIVE);
            header("Location: logout.php");
            exit;
        }
        return array("login" => $_SESSION["login"], "roleId" => getRole($_SESSION["login"]));
    }
}

define("APPLICANT", '1');
define("EMPLOYEE", '2');
define("ADMIN", '3');

define('COOKIE_LIFE_TIME', 3600 * 8);
define('SECRET_PHRASE', 'H#UGdfdy771YGD22YI@**@!G*IS)__#*!!S@@');