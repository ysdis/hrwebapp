<?php
require_once 'core.php';
require_once 'database.php';
require_once './objects/QuestionCategory.php';

header("Content-Type: application/json; charset=UTF-8");

$validated = validateSessionAPI();

$login = (empty($validated['login'])) ? null : $validated['login'];
$CUR_USER_ROLE = (empty($validated['roleId'])) ? null : $validated['roleId'];

$data = json_decode(file_get_contents("php://input"));

if(empty($data) && $_SERVER["REQUEST_METHOD"] !== "GET") {
    throwErr("Данные не были переданы!", "CATEGORY_C-2", 400);
} else {
    switch($_SERVER["REQUEST_METHOD"]) {
        case 'GET':
            if (isset($_GET['id'])) {
                if ($ident = filter_var($_GET['id'], FILTER_SANITIZE_STRING)) {
                    $item = new QuestionCategory($ident, null);
                    echo json_encode(array(
                        "id" => $item->id,
                        "title" => $item->title
                    ), JSON_UNESCAPED_UNICODE);
                    break;
                }
            } else { // GET ALL USERS
                if($CUR_USER_ROLE !== ADMIN) throwErr("Доступ воспрещён!", "PERMISSION_DENIED_GET_ALL_Q_C", 403);
                echo json_encode(getRows('SELECT * FROM questionCategories;'));
            }
            break;
        case "POST":
            $item = new QuestionCategory();
            $item->title = $data->title;

            if($item->create()) {
                throwSuccess("Категория успешно создана!", 201);
            } else {
                throwErr("Категория не была создана!", "CREATE_C", 403);
            }
            break;
        case "PUT":
            $item = new QuestionCategory($data->id);
            $item->title = $data->title;

            if($item->update()) {
                throwSuccess("Категория успешно обновлена!", 201);
            } else {
                throwErr("Категория не была обновлена!", "UPDATE_C", 403);
            }
            break;
        case "DELETE":
            $item = new QuestionCategory($data->id);

            if($item->delete()) {
                throwSuccess("Категория успешно удалена!", 201);
            } else {
                throwErr("Категория не была удалена!", "DELETE_C", 403);
            }
    }
}

