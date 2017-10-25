<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Class Router
 *
 * The Router handles all of the URL requests and returns the
 * proper page to be displayed. The returned route provides
 * information about the URI, params, title, and file.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.1.6
 * @since 0.1
 */
class Router {

    /**
     * Router constructor.
     */
    public function __construct() {
        $this->routes = [];
    }

    /**
     * Adds a route to be handled by the router
     * @param $url string the url associated with the route
     * @param $filename string the path to the page's filename (relative to /views)
     * @param $title string displayed on the top of the page when routed to
     */
    public function add($url, $filename, $title) {
        $this->routes[$url] = ["file" => $filename, "title" => $title];
    }

    /**
     * Parses the current URL and returns a matching route. If no route
     * is found the 404 page is returned.
     * @return array|mixed
     */
    public function dispatch() {
        $uri = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), BASEURL . '/');
        $uri = explode('/', $uri);
        foreach ($this->routes as $url => $route) {
            $url = substr($url, 1); // start at 1 to skip '/' in the '/page'
            if ($url == $uri[0]) {
                array_shift($uri);
                $route['params'] = $uri;
                $route['url'] = BASEURL."/".$url;

                $route['fulluri'] = $route['url'];
                if (count($uri) > 0)
                    $route['fulluri'] .= "/".implode("/",$uri);
                $route['fulluri'] = rtrim($route['fulluri'], "/");

                return $route;
            }
        }
        return ["file" => 'errors/404.php', "title" => 'Page not found!', "params" => '', "url" => '', "fulluri" => '/error404'];
    }

}