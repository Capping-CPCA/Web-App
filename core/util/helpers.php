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
 * @version 0.3.2
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
    requireRole(Role::NewUser);
}

function hasRole($role) {
    return isset($_SESSION['role']) && (($_SESSION['role'] & $role) == $role);
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

/**
 * Takes in a string of length 10 and returns a formatted phone number.
 * @param $string
 * @return string
 */
function prettyPrintPhone($string) {
    return '('.substr($string, 0, 3).') '.substr($string, 3, 3).'-'.substr($string,6);
}
/**
 * Takes in an object and casts it to a string type
 * return array copy of the object that can be referenced in array['index'] format
 * @param $object object The object that needs to be converted
 * @return array the new array cast of the object
 * @return $array the new array cast of the object
**/
function toString($object){
	$array = (array) $object;
	return $array;
}

function phoneStrToNum($phoneNum) {
    return str_replace(['(',')',' ','-'], '', $phoneNum);
}
/**
 * Checks to see if there are any duplicate PID's in the db
 * then returns a pid if already existing
 * @param $db database object
 * @param $pers_firstname string first name of participant in form input field
 * @param $pers_lastname string last name of participant in form input field
 * @param $pers_middlein string initial of middle name of participant in form input field
 * @return string participant id of new/duplicate participant
 **/
function checkForDuplicates($db, $pers_firstname,$pers_lastname,$pers_middlein){
    if($_POST['selectedID'] != ""){
        return $pIDResult = $_POST['selectedID'];
    }else{
        $pIDResult = $db->query("SELECT PeopleInsert(
                                   fName := $1::TEXT,
                                   lName := $2::TEXT,
                                   mInit := $3::VARCHAR
                                   );", [$pers_firstname, $pers_lastname, $pers_middlein]);
        return $pIDResult = pg_fetch_result($pIDResult, 0);
    }  
}

/**
 * Every time sessionTimeout is called, the start time stored in login will
 * be checked against the current system time and converted into minutes.
 * Should the elapsed time between the start time and current system
 * time exceed 60 minutes, the browser will force a logout.
**/
function sessionTimeout(){
    if(isset($_SESSION['SESSION_START'])){
        // Create a new check time every time the user goes to a new page
        $_SESSION['CHECK_TIME'] = time();
        
        // Set timeout time in minutes
        $timeoutTime = 60;
        
        // Find total elapsed time and convert to minutes
        $elapsedTime = ($_SESSION['CHECK_TIME'] - $_SESSION['SESSION_START']) / 60 % 60;
        
        // After the elapsed time reaches a limit, force destory the session and set the timeout flag to true
        if($elapsedTime >= $timeoutTime){
            session_destroy();
            session_start();
            
            // Extra check for avoiding overwriting sessions
            if (session_status() === PHP_SESSION_NONE){
                $_SESSION['timeout'] = true;
            }else{
                $_SESSION['timeout'] = true;
            } 
            // Redirect user to the login page
            header("Location: " . BASEURL . "/login");       
        }
    }
         
}



