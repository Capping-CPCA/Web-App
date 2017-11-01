<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays an error message on the page.
 *
 * Using the global $err (error number) and
 * $errst (error message) constants, a message is
 * displayed nicely on the page. If the headers have
 * already been sent, only the message div is displayed.
 * Otherwise the header and footers will be added too.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.3.1
 * @since 0.1
 */

global $err, $errst;
if (!headers_sent()) {
?>

<html>
<head>
    <title>PEP - Error <?= $err ?></title>
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
</head>
<body>

    <div class="non-menu-content d-flex flex-column
                        align-items-stretch align-content-stretch
                        justify-content-start">
        <nav class="navbar navbar-dark bg-dark">
            <div class="navbar-left d-flex align-items-center">
                <a id="cpca-brand" class="navbar-brand" href="#">PEP Manager</a>
            </div>
            <div class="navbar-center d-flex align-items-center justify-content-center">
                <h1 class="display-2" id="page-title">Error!</h1>
            </div>
        </nav>
        <div class="d-flex flex-row" id="content-wrapper">
            <div id="main-content" class="d-flex">
<?php } ?>
                <div class="w-100 d-flex flex-row justify-content-center">
                    <div class="jumbotron align-self-center text-center" style="max-width: 700px; margin: 0 auto; width: 100%">
                        <h1 class="display-3 text-danger"><i class="fa fa-exclamation-triangle"></i></h1>
                        <h1 class="display-3">Uh, oh!</h1>
                        <p class="lead text-left"><?= $errst ?></p>
                        <hr class="my-4">
                        <p>If this error continues to occur, please contact IT.</p>
                        <p class="lead">
                            <a href="/"><button class="btn btn-secondary btn-lg">Return to Home</button></a>
                        </p>
                    </div>
                </div>
<?php if (!headers_sent()) { ?>
            </div>
        </div>
        <nav class="navbar navbar-dark bg-dark">
            <footer>
                Copyright &copy; 2017 Marist College
            </footer>
        </nav>
    </div>
</body>
</html>
<?php } ?>