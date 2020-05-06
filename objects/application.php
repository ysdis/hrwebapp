<?php

class application {
    private $id;
    private $applicantLogin;
    private $formId;
    private $score;
    private $date;
    private $statusId;
    private $lastModified;

    public function __construct($_id = null) {
        if(!empty($_id)) {
            $this->download($_id);
        }
    }

    public function create() {
        $result = execQuery("INSERT INTO applications(applicantLogin, formId) VALUES(:applicant, :formId)",
            array(":applicant" => $this->applicantLogin, "formId" => $this->formId),
            true);
        if($result['rowCount'] > 0) {
            return $result['lastInsertId'];
        } else {
            throwErr("Невозможно добавить заявку", "APPLICATION-WAS-NOT-SUBMITTED", 500);
        }
    }

    public function download($_id) {
        $result = getRows("SELECT * FROM applications WHERE id = :id;", array(":id" => $_id));

        if(!empty($result)) {
            $this->applicantLogin = $result['applicantLogin'];
            $this->formId = $result['formId'];
            $this->score = $result['score'];
            $this->date = $result['date'];
            $this->statusId = $result['statusId'];
            $this->lastModified = $result['lastModified'];
            return true;
        } else {
            throwErr("Заявка не найдена.", "APPLICATION_NOT_FOUND", 400);
        }
    }

    public function calculateScore() {
        
    }

    public function setStatus($newStatusId) {
        $result = execQuery("UPDATE applications SET statusId = :statusId WHERE id = :id;",
            array(":statusId" => $newStatusId, ":id" => $this->id));
        if($result['rowCount'] > 0) {
            return true;
        } else {
            throwErr("Невозможно обновить заявку", "APPLICATION-WAS-NOT-SUBMITTED", 500);
        }
    }

    // SETTERS AND GETTERS

    /**
     * @return mixed
     */
    public function getApplicantLogin()
    {
        return $this->applicantLogin;
    }

    /**
     * @param mixed $applicantLogin
     */
    public function setApplicantLogin($applicantLogin): void
    {
        $this->applicantLogin = htmlspecialchars($applicantLogin);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = htmlspecialchars($id);
    }

    /**
     * @return mixed
     */
    public function getFormId()
    {
        return $this->formId;
    }

    /**
     * @param mixed $formId
     */
    public function setFormId($formId): void
    {
        $this->formId = htmlspecialchars($formId);
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param mixed $score
     */
    public function setScore($score): void
    {
        $this->score = htmlspecialchars($score);
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @return mixed
     */
    public function getStatusId()
    {
        return $this->statusId;
    }
}