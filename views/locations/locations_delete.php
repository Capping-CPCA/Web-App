<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Prompts the user to remove a location.
 *
 * This page displays a prompt to either remove the selected
 * location or to cancel and go back. If the user removes the
 * location then it is "deleted" from the database. It reality
 * this entry is actually archived.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.1.6
 * @since 0.1
 */

global $params, $db;
array_shift($params);

# Get site name from params
$sitename = urldecode(rawurldecode(implode('/', $params)));

$db->prepare("get_site", "SELECT * FROM sites WHERE sitename = $1");
$result = $db->execute("get_site", [$sitename]);

# If no results, site doesn't exist, redirect
if (pg_num_rows($result) == 0) {
    header('Location: /locations');
    die();
}

$site = pg_fetch_assoc($result);
pg_free_result($result);

$notConnected = pg_fetch_result($db->query("SELECT TRUE WHERE $1 NOT IN (SELECT sitename FROM classoffering)", [$sitename]), 0);

# Archive data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $canDelete = $notConnected || (hasRole(Role::Superuser));

    if ($canDelete) {
        $deleteRes = $db->query("DELETE FROM participantclassattendance WHERE sitename = $1", [$sitename]);
        $success = $deleteRes && pg_result_error_field($deleteRes, PGSQL_DIAG_SQLSTATE) == 0;

        if ($success) {
            $deleteRes = $db->query("DELETE FROM facilitatorclassattendance WHERE sitename = $1", [$sitename]);
            $success = $deleteRes && pg_result_error_field($deleteRes, PGSQL_DIAG_SQLSTATE) == 0;
        }

        if ($success) {
            $deleteRes = $db->query("DELETE FROM classoffering WHERE sitename = $1", [$sitename]);
            $success = $deleteRes && pg_result_error_field($deleteRes, PGSQL_DIAG_SQLSTATE) == 0;
        }

        if ($success) {
            $deleteRes = $db->query("DELETE FROM sites WHERE sitename = $1", [$sitename]);
            $success = $deleteRes && pg_result_error_field($deleteRes, PGSQL_DIAG_SQLSTATE) == 0;
        }

        if ($success) {
            $id = $site['addressid'];
            $deleteRes = $db->query("DELETE FROM addresses WHERE addressid = $1", [$id]);
            $success = $deleteRes && pg_result_error_field($deleteRes, PGSQL_DIAG_SQLSTATE) == 0;
        }

        $note['title'] = ($success ? 'Success!' : 'Error!');
        $note['msg'] = ($success ? 'The location has been deleted.' : 'The location wasn\'t deleted.');
        $note['type'] = ($success ? 'success' : 'danger');
        $_SESSION['notification'] = $note;
        header("Location: /locations");
        die();
    }
}

include('header.php');
?>

    <div class="page-wrapper">
        <form class="card warning-card" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
            <h4 class="card-header card-title">
                <?= htmlentities($site['sitename']) ?>
            </h4>
            <div class="card-body">
                <?php
                if ($notConnected) {
                    echo "You are about to delete location \"" . htmlentities($site['sitename']) . "\". Are you sure ".
                        "you want to delete this location?";
                } else if (hasRole(Role::Superuser)) {
                    echo "This location is currently being used for attendance. Fully deleting this location will also delete the ".
                        "attendance for this location.<br /><br />Are you sure you want to continue?";
                } else {
                    echo "You do not have permission to delete this location.";
                }
                ?>
            </div>
            <div class="card-footer text-right">
                <button type="button" class="btn btn-light" onclick="goBack()">Cancel</button>
                <?php if ($notConnected || hasRole(Role::Superuser)) { ?>
                    <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                <?php } ?>
            </div>
        </form>
    </div>

<?php
include('footer.php');