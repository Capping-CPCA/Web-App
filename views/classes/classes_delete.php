<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Prompts the user to remove a class.
 *
 * This page displays a prompt to either remove the selected
 * class or to cancel and go back. If the user removes the
 * class then it is "deleted" from the database. It reality
 * this entry is actually archived.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.6
 * @since 0.1
 */

global $params, $db;

$id = $params[1];

$db->prepare("get_class", "SELECT * FROM classes WHERE classid = $1");
$result = $db->execute("get_class", [$id]);

# If no results, class doesn't exist, redirect
if (pg_num_rows($result) == 0) {
    header('Location: /classes');
    die();
}

$class = pg_fetch_assoc($result);
pg_free_result($result);

# Archive data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $deleteRes = $db->query("UPDATE classes SET df = TRUE WHERE classid = $1", [$id]);
    if ($deleteRes) {
        $success = true;
    } else {
        $success = false;
    }
    $note['title'] = ($success ? 'Success!' : 'Error!');
    $note['msg'] = ($success ? 'The class has been deleted.' : 'The class wasn\'t deleted.');
    $note['type'] = ($success ? 'success' : 'danger');
    $_SESSION['notification'] = $note;
    header("Location: /classes");
    die();
}

include('header.php');
?>

    <div class="page-wrapper">
        <form class="card warning-card" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
            <h4 class="card-header card-title">
                <?= htmlentities($class['topicname']) ?>
            </h4>
            <div class="card-body">
                You are about to delete class "<?= htmlentities($class['topicname']) ?>". Are you sure
                you want to delete this class?
            </div>
            <div class="card-footer text-right">
                <button type="button" class="btn btn-light" onclick="goBack()">Cancel</button>
                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>

<?php
include('footer.php');