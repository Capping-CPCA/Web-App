<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * The main page that handles route display
 *
 * This file attempts to navigate to a page and display
 * it's contents. The route is first checked for permissions
 * and if the user doesn't satisfy the requirements, is
 * redirected. If any errors occur they will be displayed on
 * the page.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.3.1
 * @since 0.1
 */



require('../bootstrap.php');
require('../routes.php');

global $router, $view, $db;

// Only create a session if there is not already one there
if (session_status() === PHP_SESSION_NONE){
    session_start();
}
# checks if url is the current one
function active($url) {
    global $route;
    return $route['url'] == $url ? 'active' : '';
}

#
# Try to navigate to and display the page
#

$route = $router->dispatch();

try {
    authorize($route['fulluri']);
    $params = $route['params'];
    $view->display($route['file']);
} catch (Exception $e) {
    global $err, $errst;
    $err = $e->getCode();
    $errst = $e->getMessage();
    require('../views/errors/error.php');
}
$db->close();