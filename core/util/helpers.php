<?php

function startsWith($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    return $length === 0 ||
        (substr($haystack, -$length) === $needle);
}

function contains($haystack, $needle) {
    return strpos($haystack, $needle) !== false;
}

/**
 * Call this function atop each page that requires
 * authorization
 */
function authorizedPage() {
    if(!isset($_SESSION["username"])) {
        header("Location: " . BASEURL . "/login");
        die();
    }
}