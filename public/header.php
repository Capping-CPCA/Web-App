<?php
    global $route;
?>

<html>
<head>
    <title><?= 'PEP - '. $route['title'] ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/css/custom.css" />
    <link rel="stylesheet" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />

    <!-- JavaScript -->
    <script type="text/javascript" src="/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/js/popper.min.js"></script>
    <script type="text/javascript" src="/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/js/custom.js"></script>
    <script type="text/javascript" src="/js/search.js"></script>
    <script type="text/javascript" src="/js/livesearch.js"></script>
    <script type="text/javascript" src="/js/clone-form-td.js"></script>
    <script type="text/javascript" src="/js/jquery.mask.min.js"></script>
    <script type="text/javascript" src="/js/form_helpers.js"></script>
</head>
<body>
<div class="non-menu-content d-flex flex-column
                    align-items-stretch align-content-stretch
                    justify-content-start">

    <!-- used to cover main content when menu is open -->
    <div class="menu-cover"></div>

    <nav class="navbar navbar-dark bg-dark">
        <div class="navbar-left d-flex align-items-center">
            <a id="cpca-brand" class="navbar-brand" href="<?= BASEURL.'/'?>">PEP Manager</a>
        </div>
        <?php if (!isset($hideMenu)) { ?>
            <div class="navbar-right text-light">
                <span style="display: inline-block; color: rgba(255,255,255,0.9)"><?= $_SESSION["username"] ?></span>
                <div style="display: inline-block" class="collapsed" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user-circle-o fa-lg" id="accountOptions" aria-hidden="true"
                   style="position: relative; padding: 10px"></i><i class="fa fa-caret-down" aria-hidden="true" style="margin-right:35px;"></i></div>
                <div class="dropdown-menu" aria-labelledby="accountOptions" style="top: auto;">
                    <a class="dropdown-item text-secondary <?= active($route, 'account_settings.php') ?>" href="<?= BASEURL.'/account-settings' ?>"><i class="fa fa-fw fa-cog" aria-hidden="true"></i>Account Settings</a>
                    <a class="dropdown-item text-secondary <?= active($route, 'help.php') ?>" href="<?= BASEURL.'/help' ?>"><i class="fa fa-fw fa-question" aria-hidden="true"></i>Help</a>
                    <div class="dropdown-divider"></div>
                    <a href="/logout" class="dropdown-item text-secondary" id="login-btn"><i class="fa fa-fw fa-sign-out" aria-hidden="true"></i>Log Out</a>
                </div>
            </div>
        <?php } ?>
        <div class="navbar-center d-flex align-items-center justify-content-center">
            <h1 class="display-2" id="page-title"><?= $route['title'] ?></h1>
        </div>
    </nav>
    <div class="d-flex flex-row" id="content-wrapper">
        <?php if (!isset($hideMenu)) require_once('menu.php'); ?>
        <div id="main-content" class="d-flex">