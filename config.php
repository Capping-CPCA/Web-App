<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Configures global constants and other system functions
 * that will not be changed once started.
 *
 * This file is useful for initializing constants that are used
 * throughout the application but need a common place to be stored.
 * While similar to the bootstrap.php file, this file does not contain
 * class configuration, just variable constants.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.3.1
 * @since 0.1.3
 */

error_reporting(0);

function errorHandler($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        global $err, $errst;
        $err = $severity;
        $errst = 'Error occurred in <mark>' . basename($file) . '</mark> on <b>line ' . $line . '</b>.<br />' . $message;
        require('../views/errors/error.php');
        die();
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}

set_error_handler("errorHandler");

define('BASEPATH', __DIR__);
define('BASEURL', '');
define('VERSION', exec('git describe --tags --abbrev=0') ?: 'Unversioned');
define('CONFIG', parse_ini_file('../config.ini', true));
define('PERMISSIONS', parse_ini_file('../page_permissions.ini', true));