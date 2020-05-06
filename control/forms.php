<?php
define('__ROOT__', dirname(dirname(__FILE__)));
require_once __ROOT__.'/core.php';
require_once __ROOT__.'/Database.php';
require_once __ROOT__.'/objects/Form.php';
require_once __ROOT__.'/objects/Question.php';
require_once __ROOT__.'/objects/Option.php';

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
            break;
        case "POST":
            $form = new Form();
            $form->setTitle(htmlspecialchars(strip_tags($data->title)));
            $form->setSpecialtyId(htmlspecialchars(strip_tags($data->specialtyId)));
            $form->setTypeId(htmlspecialchars(strip_tags($data->typeId)));

            if($formCreationResult = $form->create()) { // Создаём форму
                $questions = $data->questions;

                foreach($questions as $item) { // Проходим по всем вопросам
                    $question = new Question();
                    $question->setFormId($formCreationResult["lastInsertId"]);

                    $question->setTitle($item->title);
                    $question->setQuestionType($item->questionType);
                    $question->setAnswerType($item->answerType);
                    $question->setQuestionCategory($item->questionCategory);
                    if(!empty($item->image)) {
                        $question->setImage($item->image);
                    }

                    if($questionCreationResult = $question->create()) { // Создаём вопрос
                        $options = $item->options;
                        foreach($options as $row) { // Проходим по всем вариантам ответа
                            $option = new Option();
                            $option->setQuestionId($questionCreationResult["lastInsertId"]);
                            $option->setContent($row->content);
                            if(!empty($row->isCorrect)) {
                                $option->setIsCorrect($row->isCorrect);
                            }
                            if($optionId = $option->create()) {
                                continue;
                            } else {
                                throwErr("Ошибка создания варианта ответа!", "ERROR_CREATE_O", 500);
                            }
                        }
                    } else {
                        throwErr("Ошибка создания вопроса!", "ERROR_CREATE_Q", 500);
                    }
                }
            } else {
                throwErr("Ошибка создания формы!", "ERROR_CREATE_F", 500);
            }
            throwSuccess("Загрузка формы успешно завершена!", 201);
            break;
        case "PUT":
        case "DELETE":
            break;
    }
}

