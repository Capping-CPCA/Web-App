<?php

require('config.php');

require('core/database/Database.php');
require('core/view/ViewLoader.php');
require('core/view/View.php');

require('core/router/Router.php');

include_once('core/util/helpers.php');

$db = Database::loadFromConfig("../core/database/db_config.ini");
$db->connect();

$viewLoader = new ViewLoader(BASEPATH.'/views/');
$view = new View($viewLoader);

$router = new Router();

class Role {
    const User = 1;
    const Facilitator = 2;
    const Admin = 4;
    const SuperAdmin = 8;
}