<?php
require_once "core.php";
require_once "./objects/User.php";

header("Content-Type: application/json; charset=UTF-8");

$validated = validateSessionAPI(true);

$login = (empty($validated['login'])) ? null : $validated['login'];
$CUR_USER_ROLE = (empty($validated['roleId'])) ? null : $validated['roleId'];

if(!empty($login)) {
    $user = new User($login);

    if($user->getEmailVerified() === "1") {
        throwErr("Аккаунт уже активирован, повторная активация невозможна!", "SEND-VERIFY-3", 400);
    }

    $headers = array('From' => 'no-reply@v6services.mcdir.ru',
        'MIME-Version' => '1.0',
        'Content-type' => 'text/html;charset=UTF-8');
    
    if(mail($user->getLogin(),
        "Активация аккаунта в системе тестирования V6 ИНТЕГРЕЙШН",
        createEmailVerificationText($user->getLogin()),
        $headers)) {
        throwSuccess("Активационное письмо успешно отправлено!", 200);
    } else {
        throwErr("Невозможно отправить письмо активации!", "SEND-VERIFY-2", 403);
    }
} else {
    throwErr("Невозможно отправить письмо активации!", "SEND-VERIFY-1", 403);
}

function createEmailVerificationText($_login) {
    return '
        <html lang="ru">
            <head>
                <title>Активация аккаунта</title>
            </head>
            <body>
                <p>Нажмите на <a href="http://v6services.mcdir.ru/emailVerification.php?email='.urlencode($_login).'&code='.md5(urlencode($_login).SECRET_PHRASE).'">ссылку</a> для активации аккаунта и завершения регистрации в сервисе тестирования V6 ИНТЕГРЕЙШН.</p>
            </body>
        </html>
';
}