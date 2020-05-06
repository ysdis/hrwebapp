<?php
    require_once "core.php";

    class Database {
        private $servername = "localhost";
        private $username = "root"; // id11586009_toor
        private $password = ""; // XYwU1oDj7\HFR#oF
        private $dbname = "hrwebapp";
        
        public $conn = null;

        function __construct() {
            $this->conn = $this->getConnection();
        }

        public function getConnection() {
            if($this->conn == null) {
                try {
                    $this->conn = new PDO("mysql:host=" . $this->servername . ";dbname=" . $this->dbname, $this->username, $this->password);
                    $this->conn->exec("set names utf8");
                } catch(PDOException $exception) {
                    $this->conn = null;
                    die("Connection error: " . $exception->getMessage());
                }
            }
            
            return $this->conn;
        }
    }

    function getRole($_login) {
        try {
            if($stmt = (new Database)->conn->prepare("SELECT roleId FROM users WHERE login = :login;")) {
                if($stmt->execute(array(":login" => $_login))) {
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        return (empty($result['roleId'])) ? null : $result['roleId'];
                } else {
                    return -1; // Something happened wrong
                }
            } else {
                return -2; // !Error!
            }
        } catch(Exception $exception) {
            die("Error: " . $exception->getMessage());
        }
    }

    // REFRESHED VERSION OF GETTING ROWS FUNCTION v2.1
    function getRows($query, $args = null, $conn = null) {
        $list = null;
        if(empty($conn)) {
            $conn = (new Database())->conn;
        }

        if($stmt = $conn->prepare($query)) {
            if($stmt->execute($args)) {
                if($stmt->rowCount() === 1) { // RETURN ONE ROW AS ASSOC ARRAY
                    if($stmt->columnCount() === 1) { // RETURN VALUE WHEN ONE COLUMN AND ONE ROW
                        foreach ($stmt->fetch(PDO::FETCH_ASSOC) as $key => $value)
                            return $value;
                    } else {
                        $list = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
                } else if($stmt->rowCount() > 1) {
                    $list = array();
                    if($stmt->columnCount() === 1) { // RETURN INDEXED ARRAY IF ONE COLUMN
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            foreach ($row as $key => $value)
                                array_push($list, $value);
                        }
                    } else { // RETURN ARRAY OF ASSOC ARRAYS
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            array_push($list, $row);
                        }
                    }
                }
            } else {
                throwErr("Execution error", "6002", 503);
            }
        } else {
            throwErr("Execution error", "6003", 503);
        }
        return $list;
    }

    // Executes queries like UPDATE, INSERT, DELETE. Returns -1 when an error occurs otherwise affected rows
    // $query - SQL query
    // $argsvals - array with params names (:id, :login etc.) next to their values
    function execQuery($query, $argsvals, $returnLastInsertId = false) {
        $conn = (new Database)->conn;
        $stmt = $conn->prepare($query);
        if($stmt->execute($argsvals)) {
            if($returnLastInsertId) {
                $lastInsertId = getRows("SELECT LAST_INSERT_ID();", null, $conn);
                return array("rowCount" => $stmt->rowCount(), "lastInsertId" => $lastInsertId);
            }
            return $stmt->rowCount();
        } else {
            throwErr("Execution error", "6004", 503);
        }
        return false;
    }

    // CREATE <SELECT> INPUT FROM QUERY. PASS COLUMNS AS 'id' and 'name' if they have different names
    // v2.0
    function createSelect($query, $valueColumn, $displayColumn, $elementId, $withEmptyState = false, $emptyStateText = "Все") {
        $options = getRows($query);
        $html = '<select id="'.$elementId.'" class="form-control">';
        if($withEmptyState) $html .= '<option value="0" selected>'.$emptyStateText.'</option>';
        for($i = 0; $i < count($options); $i++) {
            $opt = $options[$i];
            $html .= '<option value="'.$opt[$valueColumn].'" '.(($i === 0 && !$withEmptyState) ? 'selected' : '').'>'.$opt[$displayColumn].'</option>';
        }
        $html .= '</select>';
        return $html;
    }