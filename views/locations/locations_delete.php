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
 * @version 0.6
 * @since 0.1
 * @deprecated
 */

global $params, $db;
array_shift($params);

# Get site name from params
$sitename = rawurldecode(implode('/', $params));

$db->prepare("get_site", "SELECT TRUE WHERE $1 IN (SELECT unnest(enum_range(NULL::programtype))::text as type);");
$result = $db->execute("get_site", [$sitename]);

# If no results, site doesn't exist, redirect
if (pg_num_rows($result) == 0) {
    header('Location: /locations');
    die();
}

$site = pg_fetch_assoc($result);
pg_free_result($result);

# Archive data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    // TODO: Stored procedure delete enum value
    $deleteRes = $db->query("DELETE FROM pg_enum WHERE enumtypid = 'programtype'::regtype AND enumlabel = $1", [$sitename]);
    if ($deleteRes) {
        $success = true;
    } else {
        $success = false;
    }
    $note['title'] = ($success ? 'Success!' : 'Error!');
    $note['msg'] = ($success ? 'The location has been deleted.' : 'The location wasn\'t deleted.');
    $note['type'] = ($success ? 'success' : 'danger');
    $_SESSION['notification'] = $note;
    header("Location: /locations");
    die();
}

include('header.php');
?>

    <div class="page-wrapper">
        <form class="card warning-card" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
            <h4 class="card-header card-title">
                <?= $sitename ?>
            </h4>
            <div class="card-body">
                You are about to delete location "<?= $sitename ?>". Are you sure
                you want to delete this location?
            </div>
            <div class="card-footer text-right">
                <button type="button" class="btn btn-light" onclick="goBack()">Cancel</button>
                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>

<?php
include('footer.php');