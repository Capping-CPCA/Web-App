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
$sitename = rawurldecode(implode('/', $params));

$db->prepare("get_site", "SELECT * FROM sites WHERE sitename = $1");
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
    // TODO: delete class
    $_SESSION['delete-success'] = true;
    header('Location: /locations');
    die();
}

include('header.php');
?>

    <div class="page-wrapper">
        <form class="card warning-card" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
            <h4 class="card-header card-title">
                <?= $site['sitename'] ?>
            </h4>
            <div class="card-body">
                You are about to delete location "<?= $site['sitename'] ?>". Are you sure
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