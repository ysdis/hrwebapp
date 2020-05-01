<?php
 error_reporting(E_ALL);
 ini_set('display_startup_errors', 1);
 ini_set('display_errors', 'on');

function throwErr($message, $errCode, $responseCode) {
    http_response_code($responseCode);
    echo json_encode(array("message" => $message, "errorCode" => $errCode));
    die();
}

$cookieLifetime = 3600 * 8;
$secretPhrase = "H#UGdfdy771YGD22YI@**@!G*IS)__#*!!S@@";