<?php
    global $route;
?>

<html>
<head>
    <title><?= 'CPCA - '. $route['title'] ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/custom.css" />

    <!-- JavaScript -->
    <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="js/popper.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/custom.js"></script>
</head>
<body>
<div class="non-menu-content d-flex flex-column
                    align-items-stretch align-content-stretch
                    justify-content-start">

    <!-- used to cover main content when menu is open -->
    <div class="menu-cover"></div>

    <nav class="navbar navbar-dark bg-dark">
        <div class="navbar-left d-flex align-items-center">
            <a id="cpca-brand" class="navbar-brand" href="#">CPCA</a>
        </div>
        <?php if (!isset($hideMenu)) { ?>
            <div class="navbar-right">
                <button onclick="window.location = '/logout';" type="button" class="btn cpca" id="login-btn">Log Out</button>
            </div>
        <?php } ?>
        <div class="navbar-center d-flex align-items-center justify-content-center">
            <h1 class="display-2" id="page-title"><?= $route['title'] ?></h1>
        </div>
    </nav>
    <div class="d-flex flex-row" id="content-wrapper">
        <?php if (!isset($hideMenu)) require_once('menu.php'); ?>
        <div id="main-content" class="d-flex">