<?php
require_once 'core.php';
session_start();

if(isset($_SESSION["loggedin"])) {
    unset($_SESSION["loggedin"]);
    session_destroy();
}

setcookie("u_l", null, time() - 3600);
setcookie("u_key", null, time() - 3600);
header("Location: index.php");

