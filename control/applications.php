<?php
define('__ROOT__', dirname(dirname(__FILE__)));
require_once __ROOT__.'/core.php';
require_once __ROOT__.'/Database.php';
require_once __ROOT__.'/objects/Application.php';

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
            break;
        case "POST":
            break;
    }
}