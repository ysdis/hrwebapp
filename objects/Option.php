<?php
require_once dirname(dirname(__FILE__)).'/core.php';
require_once dirname(dirname(__FILE__)).'/Database.php';

class Option {
    protected $CONTENT_LEN = 1000;

    protected $tableName = "options";
    protected $errorMessages = array(
        "itemWasNotFound" => "Вариант ответа не найден.",
        "unableCreateItem" => "Невозможно создать вариант ответа!",
        "unableUpdateItem" => "Невозможно обновить вариант ответа!",
        "unableDeleteItem" => "Невозможно удалить вариант ответа!"
    );

    private $id;
    private $questionId;
    private $content;
    private $isCorrect;

    public function __construct($_id = null) {
        if (!empty($_id)) {
            $this->id = htmlspecialchars(strip_tags($_id));
            $this->find();
        }
    }

    public function find() {
        if (empty($this->id)) {
            throwErr($this->errorMessages["itemWasNotFound"], "ITEM_ID_NOT_GIVEN", 400);
        }
        $result = getRows("SELECT * FROM {$this->tableName} WHERE id = :id;", array(':id' => $this->id));

        if (!empty($result)) {
            $this->questionId = $result['questionId'];
            $this->content = $result['content'];
            $this->isCorrect = $result['isCorrect'];
            return true;
        } else {
            throwErr($this->errorMessages["itemWasNotFound"], "ITEM_NOT_FOUND", 400);
            return false;
        }
    }

    public function create() {
        if(empty($this->content)) return false;
        if((strlen($this->content) > $this->CONTENT_LEN)) return false;

        $query = "INSERT INTO {$this->tableName} (questionId, content, isCorrect) VALUES (:questionId, :content, :isCorrect);";
        try {
            return execQuery($query, array(
                ':questionId' => $this->questionId,
                ':content' => $this->content,
                ':isCorrect' => ($this->isCorrect) ? '1' : '0'
            ), true);
        } catch (PDOException $e) {
            throwErr($this->errorMessages["unableCreateItem"], "UNABLE_CREATE_ITEM", 500);
            return false;
        }
    }

    public function update() {
        if(empty($this->content)) return false;
        if((strlen($this->content) > $this->CONTENT_LEN)) return false;

        $query = "UPDATE {$this->tableName} SET questionId = :questionId, content = :content, isCorrect = :isCorrect WHERE id = :id;";
        try {
            return execQuery($query, array(
                ':id' => $this->id,
                ':questionId' => $this->questionId,
                ':content' => $this->content,
                ':isCorrect' => $this->isCorrect
            ));
        } catch (PDOException $e) {
            throwErr($this->errorMessages["unableUpdateItem"], "UNABLE_UPDATE_ITEM", 500);
            return false;
        }
    }

    public function delete() {
        if (empty($this->id)) return false;

        $query = "DELETE FROM {$this->tableName} WHERE id = :id;";
        try {
            return execQuery($query, array(
                ':id' => $this->id,
            ));
        } catch (PDOException $e) {
            throwErr($this->errorMessages["unableDeleteItem"], "UNABLE_DELETE_ITEM", 500);
            return false;
        }
    }

    //-------------------GETTERS SETTERS-------------------//

    public function getId() {
        return $this->id;
    }

    public function setId($id): void {
        $this->id = htmlspecialchars(strip_tags($id));
    }

    public function getQuestionId() {
        return $this->questionId;
    }

    public function setQuestionId($questionId): void {
        $this->questionId = htmlspecialchars(strip_tags($questionId));
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content): void {
        $this->content = htmlspecialchars(strip_tags($content));
    }

    public function getIsCorrect() {
        return $this->isCorrect;
    }

    public function setIsCorrect($isCorrect): void {
        $this->isCorrect = htmlspecialchars(strip_tags($isCorrect));
    }

}