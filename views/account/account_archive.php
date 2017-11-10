<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Prompts the user to remove an employee.
 *
 * This page displays a prompt to either remove the selected
 * employee or to cancel and go back. If the user removes the
 * employee then he/she is "deleted" from the database. In reality
 * this entry is actually archived by checking the delete flag.
 *
 *
 * @author Michelle Crawley
 * @copyright 2017 Marist College
 * @version 0.3.3
 * @since 0.1
 */
global $db, $route, $params, $view;

# Get employee id from the route parameters
$employeeid = $params[1];

# Check if the person logged in has the permission to access this page
if (!(hasRole(Role::Admin))) {
    header('Location: /dashboard');
    die();
} else {
    $result = $db->query("SELECT firstname, middleinit, lastname " .
        "FROM people
        WHERE peopleid = $1", [$employeeid]);
    $person = pg_fetch_assoc($result);
    extract($person);

    # Archive data
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
        $archiveRes = $db->query("UPDATE employees SET df = 1 WHERE employeeid = $1", [$employeeid]);
        $result = $db->query("SELECT facilitatorid FROM facilitators WHERE facilitatorid = $1", [$employeeid]);
        $isFacilitator = pg_fetch_assoc($result);

        # Check if the employee is also a facilitator so we can remove them from that table as well
        if ($isFacilitator) {
            $facilitatorRes = $db->query("UPDATE facilitators SET df = 1 WHERE facilitatorid = $1", [$employeeid]);
        }

        $success = $archiveRes && (($isFacilitator && $facilitatorRes) || !$isFacilitator);

        # If the archive is successful display a notification on the manage-users page
        $note['title'] = ($success ? 'Success!' : 'Error!');
        $note['msg'] = ($success ? 'The user has been deleted.' : 'The user wasn\'t deleted.');
        $note['type'] = ($success ? 'success' : 'danger');
        $_SESSION['notification'] = $note;
        header('Location: /manage-users');
        die();
    }

    include('header.php');
    ?>

    <div class="page-wrapper">
        <form class="card warning-card" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
            <h4 class="card-header card-title">
                <?= $firstname . ' ' . $middleinit . ' ' . $lastname ?>
            </h4>
            <div class="card-body">
                You are about to delete user "<?= $firstname . ' ' . $middleinit . ' ' . $lastname ?>". Are you sure
                you want to delete this user?
            </div>
            <div class="card-footer text-right">
                <button type="button" class="btn btn-light" onclick="goBack()">Cancel</button>
                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>

    <?php
    include('footer.php');
} ?>