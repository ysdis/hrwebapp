<?php
require_once 'database.php';

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

define("APPLICANT", '1');
define("EMPLOYEE", '2');
define("ADMIN", '3');

define('COOKIE_LIFE_TIME', 3600 * 8);
define('SECRET_PHRASE', 'H#UGdfdy771YGD22YI@**@!G*IS)__#*!!S@@');