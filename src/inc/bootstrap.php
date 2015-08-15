<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');


spl_autoload_register(function ($classname) {
    $searchFile = $classname . '.php';

    $dir =  dirname(dirname(__FILE__)) . '/lib';

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $filename) {
        if ($filename->isDir()) continue;
        if(basename($filename) === $searchFile)
            require_once($filename);
    }
});

?>