<?php

require_once('../config.php');
global $params;

/**
 * Looks to see if there are any cookies set during the user'scandir
 * session that may persist into a new session and unsets them.
 *
 */
function unsetCookies(){
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time()-1000);
            setcookie($name, '', time()-1000, '/');
        }
    }  
    
}

/**
 * Just in case the header meta tags do not force the browser to clear it's cache
 * for this site, the resetCache() fucntion will use headers to alert the browser
 * to an expired cache, thus forcing a clear.
 *
 */
function resetCache(){  
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', FALSE);
    header('Pragma: no-cache');
 
}
// Unset all cookies and reset the cache before the destroying of the session
unsetCookies();
resetCache();
session_destroy();

header("Location: " . BASEURL . "/login");
      
