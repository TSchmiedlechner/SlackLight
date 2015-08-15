<?php

class Post extends Entity implements JsonSerializable
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var bool
     */
    private $favorite;

    /**
     * @var bool
     */
    private $unread;

    /**
     * @var int
     */
    private $channelId;

    /**
     * @var datetime
     */
    private $datetime;

    /**
     * @param $id integer
     * @param $title string
     * @param $text string
     * @param $userId integer
     * @param $channelId integer
     * @param $datetime datetime
     * @param $isFavorite bool
     * @param $isUnread bool
     */
    public function __construct($id, $title, $text, $userId, $channelId, $datetime, $isFavorite = null, $isUnread = null) {
        parent::__construct($id);

        $this->title = $title;
        $this->text = $text;
        $this->userId = $userId;
        $this->channelId = $channelId;
        $this->datetime = $datetime;
        $this->favorite = $isFavorite;
        $this->unread = $isUnread;

    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text) {
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getChannelId() {
        return $this->channelId;
    }

    /**
     * @param int $channelId
     */
    public function setChannelId($channelId) {
        $this->channelId = $channelId;
    }

    /**
     * @return datetime
     */
    public function getDatetime() {
        return $this->datetime;
    }

    /**
     * @param datetime $datetime
     */
    public function setDatetime($datetime) {
        $this->datetime = $datetime;
    }

    /**
     * @return boolean
     */
    public function isFavorite() {
        return $this->favorite;
    }

    /**
     * @param boolean $favorite
     */
    public function setFavorite($favorite) {
        $this->favorite = $favorite;
    }

    /**
     * @return boolean
     */
    public function isUnread() {
        return $this->unread;
    }

    /**
     * @param boolean $unread
     */
    public function setUnread($unread) {
        $this->unread = $unread;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize() {
        $user = DataManager::getUserById($this->userId);
        return [
            'id' => $this->getId(), 'title' => $this->title, 'text' => $this->text, 'user' => $user,
            'threadId' => $this->channelId, "datetime" => $this->datetime, 'isFavorite' => intval($this->favorite),
            'isUnread' => intval($this->unread)
        ];
    }
}

?>