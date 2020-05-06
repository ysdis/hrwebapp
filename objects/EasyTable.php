<?php
require_once '../core.php';
require_once '../Database.php';

class EasyTable {
    protected int $TITLE_LEN = 45;

    protected ?string $tableName = null;
    protected ?array $errorMessages = null;

    public ?string $id = null;
    public ?string $title = null;

    public function __construct($_id = null, $_title = null) {
        if(!empty($_id)) {
            $this->id = htmlspecialchars(strip_tags($_id));
            $this->find();
        }
        if(!empty($_title)) {
            $this->title = htmlspecialchars(strip_tags($_title));
        }
    }

    public function find() {
        if(empty($this->id)) {
            throwErr($this->errorMessages["itemWasNotFound"], "ITEM_ID_NOT_GIVEN", 400);
        }
        $result = getRows("SELECT * FROM {$this->tableName} WHERE id = :id;", array(':id' => $this->id));

        if (!empty($result)) {
            $this->title = $result['title'];
            return true;
        } else {
            throwErr($this->errorMessages["itemWasNotFound"], "ITEM_NOT_FOUND", 400);
            return false;
        }
    }

    public function create() {
        if(empty($this->title)) return false;
        if(strlen($this->title) > $this->TITLE_LEN) return false;

        $query = "INSERT INTO {$this->tableName} (title) VALUES (:title);";
        try {
            return execQuery($query, array(
                ':title' => $this->title,
            ));
        } catch (PDOException $e) {
            throwErr($this->errorMessages["unableCreateItem"], "UNABLE_CREATE_ITEM", 500);
            return false;
        }
    }

    public function update() {
        if(empty($this->title)) return false;
        if(strlen($this->title) > $this->TITLE_LEN) return false;

        $query = "UPDATE {$this->tableName} SET title = :title WHERE id = :id;";
        try {
            return execQuery($query, array(
                ':title' => $this->title,
                ':id' => $this->id
            ));
        } catch (PDOException $e) {
            throwErr($this->errorMessages["unableUpdateItem"], "UNABLE_UPDATE_ITEM", 500);
            return false;
        }
    }

    public function delete() {
        if(empty($this->id)) return false;

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
}