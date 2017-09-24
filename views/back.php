<?php

global $router;

$newPage = $router->back();
if ($newPage == null) {
    $newPage = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
}

header("Location: $newPage");
die();