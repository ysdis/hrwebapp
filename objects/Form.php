<?php
require_once '../core.php';
require_once '../Database.php';

class Form {
    protected int $TITLE_LEN = 144;

    protected string $tableName = "forms";
    protected array $errorMessages = array(
        "itemWasNotFound" => "Форма не найдена.",
        "unableCreateItem" => "Невозможно создать форму!",
        "unableUpdateItem" => "Невозможно обновить форму!",
        "unableDeleteItem" => "Невозможно удалить форму!"
    );

    private $id;
    private $title;
    private $specialtyId;
    private $typeId;
    private $isActive;

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
            $this->title = $result['title'];
            $this->specialtyId = $result['specialtyId'];
            $this->typeId = $result['typeId'];
            $this->isActive = $result['isActive'];
            return true;
        } else {
            throwErr($this->errorMessages["itemWasNotFound"], "ITEM_NOT_FOUND", 400);
            return false;
        }
    }

    public function create() {
        if (empty($this->title)) return false;
        if (strlen($this->title) > $this->TITLE_LEN) return false;

        $query = "INSERT INTO {$this->tableName} (title, specialtyId, typeId) VALUES (:title, :specialtyId, :typeId);";
        try {
            return execQuery($query, array(
                ':title' => $this->title,
                ':specialtyId' => $this->specialtyId,
                ':typeId' => $this->typeId
            ), true);
        } catch (PDOException $e) {
            throwErr($this->errorMessages["unableCreateItem"], "UNABLE_CREATE_ITEM", 500);
            return false;
        }
    }

    public function update() {
        if (empty($this->title)) return false;
        if (strlen($this->title) > $this->TITLE_LEN) return false;

        $query = "UPDATE {$this->tableName} SET title = :title, specialtyId = :specialtyId, typeId = :typeId WHERE id = :id;";
        try {
            return execQuery($query, array(
                ':title' => $this->title,
                ':specialtyId' => $this->specialtyId,
                ':typeId' => $this->typeId,
                ':id' => $this->id
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

    public function getSpecialtyId() {
        return $this->specialtyId;
    }

    public function setSpecialtyId($specialtyId): void {
        $this->specialtyId = htmlspecialchars(strip_tags($specialtyId));
    }

    public function getTypeId() {
        return $this->typeId;
    }

    public function setTypeId($typeId): void {
        $this->typeId = htmlspecialchars(strip_tags($typeId));
    }

    public function getIsActive() {
        return $this->isActive;
    }

    public function setIsActive($isActive): void {
        $this->isActive = htmlspecialchars(strip_tags($isActive));
    }
}