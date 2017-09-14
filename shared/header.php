<?php
/**
 * This header file should be included on every page to keep the
 * menu and navigation consistent throughout the application.
 *
 * To change the title in the browser, assign a `$title` variable to
 * a string before including this file. This will make that title customizable.
 *
 * This file should be the first thing included at the top of the page.
 * `include('shared/header.php');`
 */
?>
<html>
<head>
    <title><?php echo (isset($title) ? $title : 'CPCA'); ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="public/css/bootstrap.min.css" />
    <link rel="stylesheet" href="public/css/custom.css" />

    <!-- JavaScript -->
    <script type="text/javascript" src="public/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="public/js/popper.min.js"></script>
    <script type="text/javascript" src="public/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="public/js/custom.js"></script>
</head>
<body>
<?php include('menu.php'); ?>
<div class="non-menu-content d-flex flex-column
            align-items-stretch align-content-stretch
            justify-content-start">

    <!-- used to cover main content when menu is open -->
    <div class="menu-cover" onclick="hideMenu()"></div>

    <nav class="navbar navbar-dark bg-dark">
        <div class="navbar-left d-flex align-items-center">
            <button class="navbar-toggler" type="button" onclick="showMenu()">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a id="cpca-brand" class="navbar-brand" href="#">CPCA</a>
        </div>
        <div class="navbar-right">
            <button type="button" class="btn cpca">Log In</button>
        </div>
        <div class="navbar-center d-flex align-items-center justify-content-center">
            <h1 class="display-2" id="page-title"><?php echo $pageTitle; ?></h1>
        </div>
    </nav>
    <div id="main-content" class="align-self-center">