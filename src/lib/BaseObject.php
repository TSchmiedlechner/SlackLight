<?php

class BaseObject {
    public function __get($name) {
        throw new Exception('Attribute ' . $name . ' is not declared.');
    }

    public function __set($name, $value) {
        throw new Exception('Attribute ' . $name . ' is not declared.');
    }

    public function _call($name, $arguments) {
        throw new Exception('Method ' . $name . ' is not declared.');
    }

    public static function __callStatic($name, $arguments) {
        throw new Exception('Method ' . $name . ' is not declared.');
    }
}

?>