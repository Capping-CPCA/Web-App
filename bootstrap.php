<?php

require('config.php');

require('core/database/Database.php');
require('core/view/ViewLoader.php');
require('core/view/View.php');

require('core/router/Router.php');

include_once('core/util/helpers.php');

$db = new Database('10.11.12.21', '5432',
    'postgres', '[actual password]', // replace with actual password
    'New_DB');
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