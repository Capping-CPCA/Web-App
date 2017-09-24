<?php

session_start();

require('../bootstrap.php');
require('../routes.php');

global $router, $view, $db;

$route = $router->dispatch();

function active($route, $url) {
    return $route['url'] == $url ? 'active' : '';
}

try {
    $params = $route['params'];
    $view->display($route['file']);
} catch (Exception $e) {
    include('header.php');
    echo http_response_code();
    echo $e;
    include('footer.php');
}

$db->close();