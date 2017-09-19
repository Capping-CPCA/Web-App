<?php

class Router {

    private $routes = [];

    public function __construct() {
    }

    public function add($url, $filename, $title) {
        $this->routes[$url] = ["file" => $filename, "title" => $title];
    }

    public function dispatch() {
        $uri = str_replace(BASEURL . '/', "",
            parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $uri = explode('/', $uri);
        foreach ($this->routes as $url => $route) {
            $url = substr($url, 1);
            if ( in_array($url, $uri) ) {
                return $route;
            }
        }
        return '404.php';
    }

}