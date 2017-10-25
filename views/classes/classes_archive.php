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
 * @version 0.1.6
 * @since 0.1
 */

global $params, $db;
array_shift($params);

# Get topic name from params
$topicname = rawurldecode(implode('/', $params));

$db->prepare("get_class", "SELECT * FROM classes WHERE topicname = $1");
$result = $db->execute("get_class", [$topicname]);

# If no results, class doesn't exist, redirect
if (pg_num_rows($result) == 0) {
    header('Location: /classes');
    die();
}

$class = pg_fetch_assoc($result);
pg_free_result($result);

# Archive data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    // TODO: delete class
    $_SESSION['delete-success'] = true;
    header('Location: /classes');
    die();
}

include('header.php');
?>

    <div class="page-wrapper">
        <form class="card warning-card" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
            <h4 class="card-header card-title">
                <?= $class['topicname'] ?>
            </h4>
            <div class="card-body">
                You are about to delete class "<?= $class['topicname'] ?>". Are you sure
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