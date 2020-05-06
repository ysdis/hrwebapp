<?php
require_once dirname(dirname(__FILE__)).'/core.php';
require_once dirname(dirname(__FILE__)).'/Database.php';

class Question {
    protected $TITLE_LEN = 1000;
    protected $IMAGE_LEN = 200;

    protected $tableName = "questions";
    protected $errorMessages = array(
        "itemWasNotFound" => "Вопрос не найден.",
        "unableCreateItem" => "Невозможно создать вопрос!",
        "unableUpdateItem" => "Невозможно обновить вопрос!",
        "unableDeleteItem" => "Невозможно удалить вопрос!"
    );

    private $id;
    private $formId;
    private $title;
    private $image;
    private $questionType;
    private $answerType;
    private $questionCategory;

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
            $this->formId = $result['formId'];
            $this->title = $result['title'];
            $this->image = $result['image'];
            $this->questionType = $result['questionType'];
            $this->answerType = $result['answerType'];
            $this->questionCategory = $result['questionCategory'];
            return true;
        } else {
            throwErr($this->errorMessages["itemWasNotFound"], "ITEM_NOT_FOUND", 400);
            return false;
        }
    }

    public function create() {
        if (empty($this->title)) return false;
        if ((strlen($this->title) > $this->TITLE_LEN) || (strlen($this->image) > $this->IMAGE_LEN)) return false;

        $query = "INSERT INTO {$this->tableName} (formId, title, image, questionType, answerType, questionCategory) VALUES (:formId, :title, :image, :questionType, :answerType, :questionCategory);";
        try {
            return execQuery($query, array(
                ':formId' => $this->formId,
                ':title' => $this->title,
                ':image' => $this->image,
                ':questionType' => $this->questionType,
                ':answerType' => $this->answerType,
                ':questionCategory' => $this->questionCategory
            ), true);
        } catch (PDOException $e) {
            throwErr($this->errorMessages["unableCreateItem"], "UNABLE_CREATE_ITEM", 500);
            return false;
        }
    }

    public function update() {
        if (empty($this->title)) return false;
        if ((strlen($this->title) > $this->TITLE_LEN) || (strlen($this->image) > $this->IMAGE_LEN)) return false;

        $query = "UPDATE {$this->tableName} SET formId = :formId, title = :title, image = :image, questionType = :questionType, answerType = :answerType, questionCategory = :questionCategory WHERE id = :id;";
        try {
            return execQuery($query, array(
                ':id' => $this->id,
                ':formId' => $this->formId,
                ':title' => $this->title,
                ':image' => $this->image,
                ':questionType' => $this->questionType,
                ':answerType' => $this->answerType,
                ':questionCategory' => $this->questionCategory
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

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title): void {
        $this->title = htmlspecialchars(strip_tags($title));
    }

    public function getFormId() {
        return $this->formId;
    }

    public function setFormId($formId): void {
        $this->formId = htmlspecialchars(strip_tags($formId));
    }

    public function getImage() {
        return $this->image;
    }

    public function setImage($image): void {
        $this->image = htmlspecialchars(strip_tags($image));
    }

    public function getQuestionType() {
        return $this->questionType;
    }

    public function setQuestionType($questionType): void {
        $this->questionType = htmlspecialchars(strip_tags($questionType));
    }

    public function getAnswerType() {
        return $this->answerType;
    }

    public function setAnswerType($answerType): void {
        $this->answerType = htmlspecialchars(strip_tags($answerType));
    }

    public function getQuestionCategory() {
        return $this->questionCategory;
    }

    public function setQuestionCategory($questionCategory): void {
        $this->questionCategory = htmlspecialchars(strip_tags($questionCategory));
    }
}