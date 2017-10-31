<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Login page for accepting credentials.
 *
 * This page accepts user credentials and matches them against an
 * LDAP server. If the credentials bind to the LDAP server, query
 * the database to get the user's information and then enter the system.
 * This file is where the session variables are set once a user has
 * been validated.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.2
 */

include('../models/Notification.php');

$error = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    global $db;
    $ldap_config = CONFIG['ldap'];

    // Initialize LDAP credentials
    $ldap_usr_dom = $ldap_config['user_domain'];
    $ldaprdn = $_POST['username'];
    if (!endsWith($ldaprdn, $ldap_usr_dom)) {
        $ldaprdn .= $ldap_usr_dom;
    }
    $ldaprdn_no_domain = substr($ldaprdn, 0, strpos($ldaprdn, '@'));
    $ldappass = $_POST['password'];

    // Connect to LDAP server
    $ldapconn = ldap_connect($ldap_config['host'], $ldap_config['port']);
    if ($ldapconn) {

        ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

        // Make sure password and username are not empty
        if (empty($ldaprdn) || empty($ldappass)) {
            $error = true;
        }

        if (!$error) {
            try {
                // Bind credentials to LDAP server
                $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
                if ($ldapbind) {
                    // Query employee for email and info
                    $res = $db->query("SELECT firstname, lastname, permissionlevel, employeeid FROM people, employees WHERE employees.email = $1 ".
                        "AND employees.employeeid = people.peopleid", [$ldaprdn]);

                    // If employee exists, get info and redirect
                    if ($res && pg_num_rows($res) > 0) {

                        $info = pg_fetch_assoc($res);

                        session_regenerate_id();
                        $_SESSION['employeeid'] = $info['employeeid'];
                        $_SESSION['username'] = $info['firstname'].' '.$info['lastname'];
                        $_SESSION['role'] = Role::roleFromPermissionLevel($info['permissionlevel']);

                        ldap_close($ldapconn);
                        pg_free_result($res);
                        header('Location: ' . BASEURL . '/dashboard');
                        die();
                    } else {
                        // Create new employee
                        ldap_close($ldapconn);
                        pg_free_result($res);
                        $_SESSION['ldaprdn'] = $ldaprdn;
                        header("Location: /create-account");
                        die();
                    }
                } else {
                    $error = true;
                }
            } catch (ErrorException $e) {
                $error = true;
            }
        }
        ldap_close($ldapconn);
    } else {
        $error = true;
    }

    // If LDAP doesn't work, check for super user
    if ($error) {
        $res = $db->query("SELECT salt, hashedpassword FROM superusers WHERE username = $1", [$ldaprdn_no_domain]);
        if ($res && pg_num_rows($res) > 0) {
            $info = pg_fetch_assoc($res);
            $salt = $info['salt'];
            $hashed = $info['hashedpassword'];
            // Match passwords
            if (hash('sha256', $ldappass . $salt) == $hashed) {
                $_SESSION['username'] = $ldaprdn_no_domain;
                $_SESSION['role'] = Role::Superuser;
                pg_free_result($res);
                header('Location: ' . BASEURL . '/dashboard');
                die();
            }
        }
    }
}
// If already logged in, redirect
else if (isset($_SESSION['username'])) {
    header('Location: ' . BASEURL . '/dashboard');
    die();
}

$hideMenu = true;
include('header.php');

?>

<div class="page-wrapper">
    <div class="jumbotron form-wrapper">
        <?php
        if ($error) {
            $note = new Notification('Error!', 'Invalid credentials given.', 'danger');
            $note->display();
        }
        ?>
        <form class="form" method="post" action="/login">
            <label for="username">Username</label>
            <input class="form-control username" type="text" name="username" value="<?= isset($_POST['username']) ? $_POST['username'] : '' ?>">
            <label for="password">Password</label>
            <input class="form-control password" type="password" name="password" value="<?= isset($_POST['password']) ? $_POST['password'] : '' ?>">
            <div class="form-footer submit">
                <button type="submit" class="btn cpca">Submit</button>
            </div>
        </form>
    </div>
</div>

<?php

include('footer.php');
