<?php
require_once 'EasyTable.php';

class QuestionCategory extends EasyTable {
    protected int $TITLE_LEN = 10;
    protected ?string $tableName = "questionCategories";
    protected ?array $errorMessages = array(
        "itemWasNotFound" => "Категория вопросов не найдена.",
        "unableCreateItem" => "Невозможно создать категорию вопросов!",
        "unableUpdateItem" => "Невозможно обновить категорию вопросов!",
        "unableDeleteItem" => "Невозможно удалить категорию вопросов!"
    );
}