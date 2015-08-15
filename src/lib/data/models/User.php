<?php

class User extends Entity implements JsonSerializable
{

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $mail;

    /**
     * @var string
     */
    private $passwordHash;

    public function __construct($id, $userName, $firstName, $lastName, $mail, $passwordHash) {
        parent::__construct($id);

        $this->userName = $userName;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->mail = $mail;
        $this->passwordHash = $passwordHash;
    }

    /**
     * @return string
     */
    public function getUserName() {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName($userName) {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getMail() {
        return $this->mail;
    }

    /**
     * @param string $mail
     */
    public function setMail($mail) {
        $this->mail = $mail;
    }

    /**
     * @return string
     */
    public function getPasswordHash() {
        return $this->passwordHash;
    }

    /**
     * @param string $passwordHash
     */
    public function setPasswordHash($passwordHash) {
        $this->passwordHash = $passwordHash;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize() {
        return ["id" => $this->getId(), 'username' => $this->userName, 'firstName' => $this->firstName, 'lastName' => $this->lastName, 'mail' => $this->mail];
    }
}

?>