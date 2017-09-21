<?php

/**
 * Created by PhpStorm.
 * User: John.Grzechowiak1
 * Date: 9/20/2017
 * Time: 9:24 AM
 */
class ViewLoader {

    public function __construct($path) {
        $this->path = $path;
    }

    public function load($viewName) {
        if( file_exists($this->path.$viewName) ) {
            return require_once($this->path.$viewName);
        }
        throw new Exception("View does not exist: ".$this->path.$viewName);
    }

}