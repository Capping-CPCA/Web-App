<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * View loader class to display pages.
 *
 * This class uses a simple load function to require a PHP
 * page if it exists. If the page does not exists, a 404 error
 * is thrown.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.1
 * @since 0.1
 */
class ViewLoader {

    /**
     * ViewLoader constructor.
     * @param $path string the base path for all views
     */
    public function __construct($path) {
        $this->path = $path;
    }

    /**
     * Loads the given view based on its relative path to the
     * base view folder.
     * @param $viewName string the view to display
     * @return mixed content at the given path
     * @throws Exception 404 if view doesn't exist
     */
    public function load($viewName) {
        if( file_exists($this->path.$viewName) ) {
            return require_once($this->path.$viewName);
        }
        http_response_code(404);
        throw new Exception("View does not exist: ".$this->path.$viewName);
    }

}