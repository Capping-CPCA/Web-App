<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * A collection of PHP helper functions.
 *
 * These functions are generic functions that can be used
 * anywhere in the application to make development easier.
 * Add a function to this file if it is being used in
 * more than one file.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.3.1
 * @since 0.1
 */

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
 * Compares the given URL with permission settings from the
 * page_permissions.ini file.
 * @param $url string the url navigating to
 */
function authorize($url) {
    $permission_config = PERMISSIONS['permissions'];
    $urlArr = explode("/", ltrim($url, "/"));
    $per = 'DENY *';

    # try to match the url with a configuration
    if (isset($permission_config[$url])) {
        $per = $permission_config[$url];
    } else {
        # Look for wildcard (*) matches
        $newPerm = '';
        foreach ($permission_config as $r => $perm) {
            $routeArr = explode("/", ltrim($r, "/"));

            # Only match if permission url is the same size as navigated url
            if (count($routeArr) == count($urlArr)) {
                $match = true;
                # Match each part of the URL with the permission route
                for ($i = 0; $i < count($routeArr); $i++) {
                    if ($routeArr[$i] != '*' && $routeArr[$i] != '**' &&
                        $routeArr[$i] != $urlArr[$i]) {
                        $match = false;
                        break;
                    }
                }
                # Set new permission if there was a match
                if ($match) {
                    $newPerm = $perm;
                    break;
                }
            } else if (count($routeArr) < count($urlArr)) {
                $match = true;
                # Match each part of the URL with the permission route
                for ($i = 0; $i < count($urlArr); $i++) {

                    # Route not long enough
                    if (!isset($routeArr[$i])) {
                        $match = false;
                        break;
                    }

                    # Matches with everything after this point, break
                    if ($routeArr[$i] == '**') {
                        break;
                    }

                    # Doesn't match
                    if ($routeArr[$i] != $urlArr[$i]) {
                        $match = false;
                        break;
                    }
                }
                # Set new permission if there was a match
                if ($match) {
                    $newPerm = $perm;
                    break;
                }
            }
        }
        if (empty($newPerm)) {
            echo 'WARNING: No page permission defined for ' . $url;
        } else {
            $per = $newPerm;
        }
    }

    # once a page permission is found, verify authorization
    $opts = explode(' ', $per);
    $type = $opts[0]; # either ALLOW or DENY
    array_shift($opts);
    $roles = $opts; # roles to verify
    if ($type == 'ALLOW') {
        if ($roles[0] == '*') {             # allows all connections
            return;
        } else if ($roles[0] == 'auth') {   # allows authorized connections
            authorizedPage();
        } else {                            # allows the listed roles
            $allowRoles = 0;
            foreach ($roles as $r) {
                $allowRoles |= Role::roleFromPermissionLevel($r);
            }
            requireRole($allowRoles);
        }
    } else if ($type == 'DENY') {           # denies the listed roles
        if ($roles[0] == '*') {
            notAuthorized();
        } else {
            $denyRoles = 0;
            foreach ($roles as $r) {
                $denyRoles |= Role::roleFromPermissionLevel($r);
            }
            preventRole($denyRoles);
        }
    } else {
        throw new InvalidArgumentException("Neither 'ALLOW' nor 'DENY' was specified in permission for '$url'");
    }
}

/**
 * Called if the page at least requires the user to be logged
 * in and authenticated.
 */
function authorizedPage() {
    requireRole(Role::NewUser | Role::Facilitator | Role::Admin | Role::Superuser | Role::Coordinator);
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] & $role;
}

/**
 * If the user has the specified role, allow
 * them onto the page.
 * @param $role - the roles to allow
 */
function requireRole($role) {
    if (!isset($_SESSION['username']) || !hasRole($role)) {
        notAuthorized();
    }
}

/**
 * If the user has the specified role, the page is
 * redirected either to the previous page or login.
 * @param $role - the roles to prevent
 */
function preventRole($role) {
    if (!isset($_SESSION['role']) || hasRole($role)) {
        notAuthorized();
    }
}

function notAuthorized() {
    $loc = BASEURL . "/login";
    if (isset($_SERVER['HTTP_REFERER']))
        $loc = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
    header("Location: $loc");
    die();
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

/**
 * Prints an array in a nice format (for debugging)
 * @param $arr array the array to print
 */
function prettyPrint($arr) {
    print("<pre>".print_r($arr,true)."</pre>");
}

/**
 * Gets the value of an array if it exists, otherwise
 * return an empty string.
 * @param $arr array The associative array to search
 * @param $key string The key of the array to find
 * @return string the value at the key in the array or an
 *   empty string if the key doesn't exist.
 */
function valueOrEmpty($arr, $key) {
    return isset($arr[$key]) ? $arr[$key] : '';
}