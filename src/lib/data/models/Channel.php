<?php

class Channel extends Entity {

    /**
     * @var string
     */
    private $name;

    /**
     * @param $id integer
     * @param $name string
     */
    public function __construct($id, $name) {
        parent::__construct($id);

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }
}

?>