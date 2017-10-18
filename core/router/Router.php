<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Class Router
 *
 * The Router handles all of the URL requests and returns the
 * proper page to be displayed. In addition, the Router stores
 * a history of the pages navigated to. This provides useful tools
 * for navigating back (without having to use the browser back button).
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.1.5
 * @since 0.1
 */
class Router {

    /**
     * Router constructor.
     */
    public function __construct() {
        if (!isset($_SESSION['history']))
            $_SESSION['history'] = [];
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
     * Gets the current url the user is at.
     * @return string | null the current page or null if no current page
     */
    public function currentPage() {
        if (!empty($_SESSION['history']))
            return $_SESSION['history'][count($_SESSION['history']) - 1];
        return null;
    }

    public function lastPage() {
        if (!empty($_SESSION['history']) && count($_SESSION['history']) > 1)
            return $_SESSION['history'][count($_SESSION['history']) - 2];
        return null;
    }

    /**
     * Adds a url to the navigation history
     * @param $url string the url currently visiting
     */
    public function navPush($url) {
        array_push($_SESSION['history'], $url);
    }

    /**
     * Navigates back one page
     * @return string | null the previous page or null if there was no previous page
     */
    public function back() {
        array_pop($_SESSION['history']);
        return $this->currentPage();
    }

    /**
     * Clears the navigation history
     */
    public function clearHistory() {
        unset($_SESSION['history']);
    }

    /**
     * Parses the current URL and returns a matching route. If no route
     * is found the 404 page is returned.
     * @return array|mixed
     */
    public function dispatch() {
        $newUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $currPage = $this->currentPage();
        $lastPage = $this->lastPage();

        if (!startsWith($newUrl, '/js') &&
            !startsWith($newUrl, '/css') &&
            !startsWith($newUrl, '/back') &&
            !startsWith($newUrl, '/favicon.ico')) {

            // This occurs when browser back button is clicked
            if ($lastPage == $newUrl) {
                header('Location: /back');
                die();
            }

            if ($currPage != null) {
                if ($currPage != $newUrl) {
                    $this->navPush($newUrl);
                }
            } else {
                $this->navPush($newUrl);
            }
        }

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