<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Prompts the user to restore an employee.
 *
 * This page displays a prompt to either restore the selected
 * employee or to cancel and go back. If the user restores the
 * employee then he/she is "added" back into the database. In
 * reality, we are only unchecking the delete flag.
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
$result = $db->query("SELECT firstname, middleinit, lastname " .
    "FROM people
    WHERE peopleid = $1", [$employeeid]);
$person = pg_fetch_assoc($result);
extract($person);

# Restore data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['restore'])) {
    $restoreRes = $db->query("UPDATE employees SET df = 0 WHERE employeeid = $1", [$employeeid]);

    if ($restoreRes) {
        $success = true;
    } else {
        $success = false;
    }

    # If the restore is successful display a notification on the manage-users page
    $note['title'] = ($success ? 'Success!' : 'Error!');
    $note['msg'] = ($success ? 'The user has been restored.' : 'The user wasn\'t restored.');
    $note['type'] = ($success ? 'success' : 'danger');
    $_SESSION['notification'] = $note;
    header('Location: /manage-users');
    die();
}

include('header.php');
?>
    <div class="page-wrapper">
        <form class="card cpca-card" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
            <h4 class="card-header card-title">
                <?= $firstname . ' ' . $middleinit . ' ' . $lastname ?>
            </h4>
            <div class="card-body">
                You are about to restore user "<?= $firstname . ' ' . $middleinit . ' ' . $lastname ?>". Are you sure
                you want to restore this user?
            </div>
            <div class="card-footer text-right">
                <button type="button" class="btn btn-light" onclick="goBack()">Cancel</button>
                <button type="submit" name="restore" class="btn cpca">Restore</button>
            </div>
        </form>
    </div>

<?php
include ('footer.php');