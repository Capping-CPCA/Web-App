<?php

require('config.php');

require('core/view/ViewLoader.php');
require('core/view/View.php');

require('core/router/Router.php');

include_once('core/util/helpers.php');

$viewLoader = new ViewLoader(BASEPATH.'/views/');
$view = new View($viewLoader);

$router = new Router();