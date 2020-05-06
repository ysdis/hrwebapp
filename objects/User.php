<?php
require_once '../core.php';

// ERROR_PREFIX 20
// MAX ERR CODE 2021

class User {

    private static $FIRST_NAME_LEN = 100;
    private static $LAST_NAME_LEN = 100;
    private static $MIDDLE_NAME_LEN = 100;
    private static $LOGIN_LEN = 45;
    private static $PASSWORD_LEN = 255;

    private $login;
    private $password;
    private $firstName;
    private $lastName;
    private $middleName;
    private $roleId;
    private $specialtyId;
    private $isActive;

    function __construct($login = null) {
        if(!empty($login)) {
            $this->login = $login;
            $this->findByLogin();
        }
    }

    public function create() {
        if(!$this->isCredentialsValid()) return false;

        $query = "SELECT COUNT(*) FROM users WHERE login = :login;";
        if($result = getRows($query, array(':login' => $this->login))) {
            if((int)$result > 0) {
                throwErr("Пользователь с такой почтой уже существует!", "REGISTER-DUPLICATE", 400);
            }
        }

        $query = "INSERT INTO users (login, password, firstname, lastname, middleName) VALUES (:login, :password, :firstName, :lastName, :middleName);";
        try {
            return execQuery($query, array(
                ':login' => $this->login,
                ':password' => password_hash($this->password, PASSWORD_BCRYPT),
                ':firstName' => $this->firstName,
                ':lastName' => $this->lastName,
                ':middleName' => (empty($this->middleName)) ? '-' : $this->middleName
            ));
        } catch (PDOException $e) {
            throwErr("Невозможно создать пользователя", "2001", 500);
            return false;
        }
    }

    private function isCredentialsValid() {
        if( empty($this->login) ||
            empty($this->firstName) ||
            empty($this->lastName) ||
            empty($this->password)
        ) return false;

        if( strlen($this->firstName) > self::$FIRST_NAME_LEN  ||
            strlen($this->lastName) > self::$LAST_NAME_LEN ||
            strlen($this->middleName) > self::$MIDDLE_NAME_LEN ||
            strlen($this->login) > self::$LOGIN_LEN ||
            strlen($this->password) > self::$PASSWORD_LEN
        ) return false;

        return true;
    }

    public function update($idForUpdate = null) {
//        $values = array();
//        $fnField = "`firstName` = :firstName";
//        $lnField = "`lastName` = :lastName";
//        $cnField = "`company` = :company";
//        $phField = "`phone` = :phone";
//        $eField = "`email` = :email";
//        $pwdField = "`password` = :password";
//
//        // QUERY CREATION
//        if(empty($this->firstName)) {
//            $fnField = '';
//        } else {
//            $values[":firstName"] = $this->firstName;
//        }
//
//        if(empty($this->lastName)) {
//            $lnField = '';
//        } else {
//            if(count($values) > 0) $lnField = ', '.$lnField;
//            $values[":lastName"] = $this->lastName;
//        }
//
//        if(empty($this->company)) {
//            $cnField = '';
//        } else {
//            if(count($values) > 0) $cnField = ', '.$cnField;
//            $values[":company"] = $this->company;
//        }
//
//        if(empty($this->phone)) {
//            $phField = '';
//        } else {
//            if(count($values) > 0) $phField = ', '.$phField;
//            $values[":phone"] = $this->phone;
//        }
//
//        if(empty($this->email)) {
//            $eField = '';
//        } else {
//            if(count($values) > 0) $eField = ', '.$eField;
//            $values[":email"] = $this->email;
//        }
//
//        if(empty($this->password)) {
//            $pwdField = '';
//        } else {
//            if(count($values) > 0) $pwdField = ', '.$pwdField;
//            $values[":password"] = password_hash($this->password, PASSWORD_BCRYPT);
//        }
//
//        if(count($values) > 0) {
//            $query = "UPDATE `{$this->tableName}` SET ".$fnField.$lnField.$cnField.$phField.$eField.$pwdField." WHERE id = :id;";
//            if($stmt = $this->conn->prepare($query)) {
//                $values[":id"] = $idForUpdate;
//
//                if($stmt->execute($values)) {
//                    return ($stmt->rowCount() === 1);
//                } else {
//                    throwErr("Execution error", "2022", 500);
//                }
//            } else {
//                throwErr("Execution error", "2023", 500);
//            }
//        } else {
//            throwErr("Execution error", "2024", 400);
//        }
    }

    public function auth() {

        $isUserActive = getRows("SELECT isActive FROM users WHERE login = :login;", array(':login' => $this->login));

        if($isUserActive === "0") {
            throwErr("Аккаунт заблокирован, по вопросам восстановления аккаунта обращайтесь в администрацию.", "USER_BLOCKED", 403);
        } else if(empty($isUserActive)) {
            return false;
        }

        $query = "SELECT COUNT(*), password FROM users WHERE login = :login;";
        try {
            if($result = getRows($query, array(":login" => $this->login))) {
                return password_verify($this->password, $result['password']);
            } else {
                return false;
            }
        } catch(Exception $exception) {
            throwErr("Error: " . $exception->getMessage(), "2028", 503);
            return false;
        }
    }

    // NOT TESTED
    public function findByLogin($login = null) {
        if(empty($login)) {
            if(empty($this->login)) {
                throwErr("Пользователь не найден.", "2004", 400);
            } else {
                $login = $this->login;
            }
        }

        $result = getRows("SELECT * FROM users WHERE login = :login;", array(':login' => $login));

        if (!empty($result)) {
            $this->login = $result['login'];
            $this->firstName = $result['firstName'];
            $this->lastName = $result['lastName'];
            $this->middleName = $result['middleName'];
            $this->roleId = $result['roleId'];
            $this->isActive = $result['isActive'];
            $this->specialtyId = $result['specialtyId'];
            return true;
        } else {
            throwErr("Пользователь не найден.", "USER_NOT_FOUND", 400);
        }
    }

    //--------------------------------------SETTERS--------------------------------------//

    public function setFirstName($firstName) {
        $this->firstName = htmlspecialchars(strip_tags($firstName));
    }

    public function setMiddleName($firstName) {
        $this->middleName = htmlspecialchars(strip_tags($firstName));
    }

    public function setLastName($lastName) {
        $this->lastName = htmlspecialchars(strip_tags($lastName));
    }

    public function setPassword($password) {
        $this->password = htmlspecialchars(strip_tags($password));
    }

    public function setLogin($login) {
        $this->login = htmlspecialchars(strip_tags($login));
    }

    public function setSpecialtyId($specialtyId) {
        $this->specialtyId = htmlspecialchars($specialtyId);
    }

    public function setRoleId($roleId) {
        $this->roleId = htmlspecialchars($roleId);
    }


    //--------------------------------------GETTERS--------------------------------------//

    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getMiddleName() {
        return $this->middleName;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getIsActive() {
        return $this->isActive;
    }

    public function getRoleId() {
        return $this->roleId;
    }

    public function getSpecialtyId() {
        return $this->specialtyId;
    }
}