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
    requireRole(Role::User | Role::Facilitator | Role::Admin | Role::SuperAdmin);
}

function hasRole($role) {
    return $_SESSION['role'] & $role;
}

/**
 * If the user has the specified role, allow
 * them onto the page.
 * @param $role - the roles to allow
 */
function requireRole($role) {
    if (!hasRole($role)) {
        $loc = BASEURL . "/login";
        if (isset($_SERVER['HTTP_REFERER']))
            $loc = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
        header("Location: $loc");
        die();
    }
}

/**
 * If the user has the specified role, the page is
 * redirected either to the previous page or login.
 * @param $role - the roles to prevent
 */
function preventRole($role) {
    if (!isset($_SESSION['role']) || hasRole($role)) {
        $loc = BASEURL . "/login";
        if (isset($_SERVER['HTTP_REFERER']))
            $loc = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
        header("Location: $loc");
        die();
    }
}

function isValidText($text) {
    return !empty($text) && ctype_print($text);
}

function isValidNumber($num, $min = null, $max = null) {
    $valid = ctype_digit($num);
    if ($valid) {
        if ($min != null)
            $valid = $valid && $num >= $min;
        if ($max != null)
            $valid = $valid && $num <= $max;
    }
    return $valid;
}