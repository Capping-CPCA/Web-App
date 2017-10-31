<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays a page to create a new account.
 *
 * This page is accessed when a new LDAP account does
 * not have a corresponding entry in the employees table.
 * This will create an employee account with a default
 * role of "NewUser".
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.2
 * @since 0.2
 */

global $params, $db;
include ('../models/Notification.php');

// Make sure LDAP credentials are present
if (!isset($_SESSION['ldaprdn']))
    header('Location: /login');

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['ldaprdn'];
    $fname = valueOrEmpty($_POST, 'firstName');
    $mInit = valueOrEmpty($_POST, 'middleInit');
    $lname = valueOrEmpty($_POST, 'lastName');
    $phone = valueOrEmpty($_POST, 'primaryPhone');

    // Create new employee / person
    $res = $db->query("SELECT employeeinsert(fname := $1::TEXT, lname := $2::TEXT, ".
        "mInit := $3::VARCHAR, em := $4::TEXT, pPhone := $5::TEXT, pLevel := 'New'::PERMISSION)",
        [$fname, $lname, $mInit, $email, $phone]);
    if ($res) {

        // Check for query errors
        $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
        pg_free_result($res);
        if ($state == 0) {

            // Get new employee information to login
            $res = $db->query("SELECT firstname, lastname, permissionlevel, employeeid FROM people, employees WHERE employees.email = $1 ".
                "AND employees.employeeid = people.peopleid", [$email]);
            $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
            if ($state == 0 && pg_num_rows($res) > 0) {

                // Set information in session (simulates login)
                $info = pg_fetch_assoc($res);
                pg_free_result($res);
                $_SESSION['employeeid'] = $info['employeeid'];
                $_SESSION['username'] = $info['firstname'] . ' ' . $info['lastname'];
                $_SESSION['role'] = Role::roleFromPermissionLevel($info['permissionlevel']);
                header('Location: /dashboard');
                die();
            } else {
                pg_free_result($res);
                $error = $state;
            }
        } else {
            $error = $state;
        }
    } else {
        $error = "500"; // Server error
    }
}

# Display Page
$hideMenu = true;
include ('header.php');
?>

    <div class="page-wrapper">
        <div class="jumbotron form-wrapper mb-3">
            <?php
            if ($error != "") {
                $notification = new Notification('Error!',
                    "An error has occurred and your account wasn't created. [$error]", 'danger');
                $notification->display();
            }
            ?>
            <form class="form" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
                <h4>Information</h4>
                <div class="form-group row">
                    <div class="col-sm-5">
                        <label for="employee-firstName" class="">First Name</label>
                        <input type="text" class="form-control" id="employee-firstName" name="firstName" required>
                        <div class="invalid-feedback">
                            Invalid characters found in first name.
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <label for="employee-middleInit" class="">Middle</label>
                        <input type="text" class="form-control" id="employee-middleInit" name="middleInit" required maxlength="1" minlength="1">
                        <div class="invalid-feedback">
                            Invalid characters found in middle initial.
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <label for="employee-lastName" class="">Last Name</label>
                        <input type="text" class="form-control" id="employee-lastName" name="lastName" required>
                        <div class="invalid-feedback">
                            Invalid characters found in last name.
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="employee-primaryPhone" class="">Phone Number</label>
                    <input type="text" class="form-control mask-phone" id="employee-primaryPhone" name="primaryPhone"/>
                    <div class="invalid-feedback">
                        Invalid characters found in phone.
                    </div>
                </div>
                <div class="form-group">
                    <label for="employee-email" class="">Email</label>
                    <input type="text" class="form-control" id="employee-email" name="email" value="<?= $_SESSION['ldaprdn'] ?>" disabled required/>
                    <div class="invalid-feedback">
                        Invalid characters found in phone.
                    </div>
                </div>
                <div class="form-footer submit">
                    <button type="submit" class="btn cpca">Create</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(function() {
            $('.mask-phone').mask('(000) 000-0000');
        });
    </script>

<?php
include ('footer.php');