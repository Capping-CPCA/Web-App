<?php

class Router {

    private $routes = [];

    public function __construct() {
    }

    public function add($url, $filename, $title) {
        $this->routes[$url] = ["file" => $filename, "title" => $title];
    }

    public function dispatch() {
        $uri = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), BASEURL . '/');
        $uri = explode('/', $uri);
        foreach ($this->routes as $url => $route) {
            $url = substr($url, 1); // start at 1 to skip '/' in the '/page'
            if ($url == $uri[0]) {
                array_shift($uri);
                $route['params'] = $uri;
                return $route;
            }
        }
        return ["file" => '404.php', "title" => 'Page not found!'];
    }

}