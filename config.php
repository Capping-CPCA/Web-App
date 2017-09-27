<?php

define('BASEPATH', __DIR__);
define('BASEURL', '');

error_reporting(E_ALL);
//error_reporting(0);
// TODO: turn off error reporting in production

function errorHandler($errno, $errstr) {
    global $err, $errst;
    $err = $errno;
    $errst = $errstr;
    require('../views/errors/error.php');
    die();
}

set_error_handler("errorHandler");