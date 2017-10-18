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
 * @version 0.1.5
 * @since 0.1
 */

session_start();

require('../bootstrap.php');
require('../routes.php');

global $router, $view, $db;

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
    include('header.php');
    echo http_response_code();
    echo $e;
    include('footer.php');
}
$db->close();