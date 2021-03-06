<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Basic 404 page
 *
 * This page displays a 404 error if a page cannot be found.
 * It does not throw the actual error it just displays that
 * this error occurred to the user.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.1
 * @since 0.1
 */

include('header.php');
?>

<div class="w-100 d-flex flex-row justify-content-center">
    <div class="jumbotron align-self-center text-center" style="max-width: 700px; margin: 0 auto; width: 100%">
        <h1 class="display-3"><i class="fa fa-grav"></i></h1>
        <h1 class="display-3">Error 404</h1>
        <p class="lead">The requested URL <mark><?= $_SERVER['REQUEST_URI'] ?></mark> was lost in space.</p>
        <hr class="my-4">
        <p class="lead">
            <a href="/"><button class="btn btn-secondary btn-lg">Return to Home</button></a>
        </p>
    </div>
</div>

<?php
include('footer.php');