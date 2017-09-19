<?php

require('../bootstrap.php');
require('../routes.php');

session_start();

$route = $router->dispatch();

function active($route, $file) {
    return $route['file'] == $file ? 'active' : '';
}

$view->display($route['file']);