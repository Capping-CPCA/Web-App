<?php

require('../bootstrap.php');
require('../routes.php');

session_start();

$route = $router->dispatch();

function active($route, $file) {
    return $route['file'] == $file ? 'active' : '';
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