<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Prompts the user to remove a curriculum.
 *
 * This page displays a prompt to either remove the selected
 * curriculum or to cancel and go back. If the user removes the
 * curriculum then it is "deleted" from the database. It reality
 * this entry is actually archived.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.6
 * @since 0.1
 */

global $params, $db;

$id = $params[1];

$db->prepare("get_curriculum","SELECT * FROM curricula WHERE curriculumid = $1");
$db->prepare("get_curr_classes",
    "SELECT * FROM curriculumclasses, classes".
    " WHERE curriculumid = $1".
    " AND classes.classid = curriculumclasses.classid".
    " AND classes.df IS FALSE".
    " ORDER BY topicname");
$result = $db->execute("get_curriculum", [$id]);

# If no results, curricula doesn't exist, redirect
if (pg_num_rows($result) == 0) {
    header('Location: /curricula');
    die();
}

$currRes = $db->execute("get_curr_classes", [$id]);
$canDelete = true;
if (pg_num_rows($currRes) > 0) {
    $canDelete = false;
}

$curricula = pg_fetch_assoc($result);
pg_free_result($result);

# Archive data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete']) && $canDelete) {
    $deleteRes = $db->query("UPDATE curricula SET df = TRUE WHERE curriculumid = $1", [$id]);
    if ($deleteRes) {
        $success = true;
    } else {
        $success = false;
    }
    $note['title'] = ($success ? 'Success!' : 'Error!');
    $note['msg'] = ($success ? 'The curriculum has been deleted.' : 'The curriculum wasn\'t deleted.');
    $note['type'] = ($success ? 'success' : 'danger');
    $_SESSION['notification'] = $note;
    header("Location: /curricula");
    die();
}

include('header.php');
?>

<div class="page-wrapper">
    <form class="card warning-card" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
        <h4 class="card-header card-title">
            <?= $curricula['curriculumname'] ?>
        </h4>
        <div class="card-body">
            <?php
            if ($canDelete) {
                echo "You are about to delete curriculum \"". $curricula['curriculumname'] . "\". Are you sure you want to delete this curriculum?";
            } else {
                echo "This curriculum still has associated classes. Please remove the following classes from the curriculum first:";
                echo '<ul>';
                while ($class = pg_fetch_assoc($currRes)) {
                    $name = $class['topicname'];
                    $classId = $class['classid'];
                    echo "<li><a href='/classes/view/$classId' style='color: #5C629C'>$name</a></li>";
                }
                echo '</ul>';
            }
            ?>
        </div>
        <div class="card-footer text-right">
            <button type="button" class="btn btn-light" onclick="goBack()">Cancel</button>
            <?php if($canDelete) { ?>
                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
            <?php } ?>
        </div>
    </form>
</div>

<?php
include('footer.php');