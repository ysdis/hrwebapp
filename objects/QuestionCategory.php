<?php
require_once 'EasyTable.php';

class QuestionCategory extends EasyTable {
    const TITLE_LEN = 45;
    protected $tableName = "questionCategories";
    protected $errorMessages = array(
        "itemWasNotFound" => "Категория вопросов не найдена.",
        "unableCreateItem" => "Невозможно создать категорию вопросов!",
        "unableUpdateItem" => "Невозможно обновить категорию вопросов!",
        "unableDeleteItem" => "Невозможно удалить категорию вопросов!"
    );
}