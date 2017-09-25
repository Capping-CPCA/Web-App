<?php

require('config.php');

require('core/database/Database.php');
require('core/view/ViewLoader.php');
require('core/view/View.php');

require('core/router/Router.php');

include_once('core/util/helpers.php');

$db = new Database('10.11.12.21', '5432',
    'postgres', 'Password1',
    'New_DB');
$db->connect();

$viewLoader = new ViewLoader(BASEPATH.'/views/');
$view = new View($viewLoader);

$router = new Router();