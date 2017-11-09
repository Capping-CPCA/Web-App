<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Initializes necessary classes for system operation.
 *
 * This file will initialize important system classes such as the
 * database, view loader, and router. Files that are initialized or
 * required here can be used throughout the entire application. They
 * should only be classes that the system cannot operate without.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.2
 * @since 0.1
 */
require('config.php');
require('core/database/Database.php');
require('core/database/Role.php');
require('core/view/ViewLoader.php');
require('core/view/View.php');
require('core/router/Router.php');
include_once('core/util/helpers.php');

// Initialize Database
$db = Database::loadFromConfig();
$db->connect();

// Initialize View loading / routing
$viewLoader = new ViewLoader(BASEPATH.'/views/');
$view = new View($viewLoader);
$router = new Router();
