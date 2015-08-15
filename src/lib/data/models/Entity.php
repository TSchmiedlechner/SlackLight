<?php

abstract class Entity extends BaseObject
{

    /**
     * @var integer
     */
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}

?>