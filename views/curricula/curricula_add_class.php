<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays a page to allow class additions to curricula.
 *
 * This page provides various sections to allow an
 * admin to edit details about a curriculum's classes.
 * Once the form is filled out, if there are any errors, they will
 * be displayed upon submission.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 1.0
 * @since 0.3.3
 */

global $params, $db;

$id = $params[1];

$db->prepare("get_curr_classes", "SELECT * FROM classes, curriculumclasses ".
    "WHERE curriculumid = $1 AND classes.classid = curriculumclasses.classid ".
    "AND classes.df IS FALSE");
$db->prepare("get_other_classes",
    "SELECT * FROM classes WHERE classid NOT IN (" .
    "SELECT classid FROM curriculumclasses WHERE curriculumid = $1" .
    ") AND df IS FALSE ORDER BY topicname");

$topics = $db->execute("get_curr_classes", [$id]);
$allTopics = $db->execute("get_other_classes", [$id]);

$curriculum = pg_fetch_assoc($db->query("SELECT * FROM curricula WHERE curriculumid = $1", [$id]));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $classId = isset($_POST['class']) ? $_POST['class'] : '';

    // remove class
    if (isset($_POST['remove'])) {
        $res = $db->query("DELETE FROM curriculumclasses WHERE classid = $1 AND curriculumid = $2",
            [$classId, $id]);
        if ($res) {
            $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
            if ($state == 0) {
                $note['title'] = 'Success!';
                $note['msg'] = 'Class was successfully removed from the curriculum.';
                $note['type'] = 'success';
                # Refresh display
                $topics = $db->execute("get_curr_classes", [$id]);
                $allTopics = $db->execute("get_other_classes", [$id]);
            } else if ($state == "23503") { // foreign key error
                if (!hasRole(Role::Superuser)) {
                    $note['title'] = 'Error!';
                    $note['msg'] = "You do not have permission to remove this class.";
                    $note['type'] = 'danger';
                } else {
                    $confirmDelete = true;
                }
            } else {
                $note['title'] = 'Error!';
                $note['msg'] = "Class wasn't removed from the curriculum. [$state]";
                $note['type'] = 'danger';
            }
        }
    }
    // remove class with foreign key issue
    else if (isset($_POST['full-delete']) && hasRole(Role::Superuser)) {
        $errorState = "";
        // Delete from Class Offering
        $res = $db->query("DELETE FROM classoffering ".
            "WHERE classid = $1 AND curriculumid = $2 ", [$classId, $id]);
        $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
        if ($state != 0) {
            $error = true;
            $errorState .= $state . ":CO";
        }

        // Error deleting connections
        if (isset($error)) {
            str_replace(" ", ", ", $errorState);
            $note['title'] = 'Error!';
            $note['msg'] = "The class could not be fully deleted [$errorState]";
            $note['type'] = 'danger';
        }
        // Remove actual curriculum-class connection
        else {
            $res = $db->query("DELETE FROM curriculumclasses WHERE classid = $1 AND curriculumid = $2",
                [$classId, $id]);
            if ($res) {
                $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
                if ($state == 0) {
                    $note['title'] = 'Success!';
                    $note['msg'] = 'Class was successfully removed from the curriculum.';
                    $note['type'] = 'success';
                    # Refresh display
                    $topics = $db->execute("get_curr_classes", [$id]);
                    $allTopics = $db->execute("get_other_classes", [$id]);
                } else {
                    $note['title'] = 'Error!';
                    $note['msg'] = "Class wasn't removed from the curriculum. [$state]";
                    $note['type'] = 'danger';
                }
            }
        }
    }
    // add class
    else {
        if (empty($classId)) {
            $note['title'] = 'Error!';
            $note['msg'] = "Please select a class.";
            $note['type'] = 'danger';
        } else {
            $res = $db->query("INSERT INTO curriculumclasses VALUES ($1, $2)", [$classId, $id]);
            if ($res) {
                $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
                if ($state == 0) {
                    $note['title'] = 'Success!';
                    $note['msg'] = 'Class was successfully added to the curriculum.';
                    $note['type'] = 'success';
                    # Refresh display
                    $topics = $db->execute("get_curr_classes", [$id]);
                    $allTopics = $db->execute("get_other_classes", [$id]);
                } else {
                    $note['title'] = 'Error!';
                    $note['msg'] = "Class wasn't added to the curriculum. [$state]";
                    $note['type'] = 'danger';
                }
            }
        }
    }
}

include('header.php');
?>

<div class="page-wrapper">
    <?php if(isset($confirmDelete) && isset($classId)) {
        $class = pg_fetch_assoc($db->query("SELECT * FROM classes WHERE classid = $1", [$classId]));
        ?>

        <!-- Confirms remove class when class is referenced by attendance -->
        <form class="card warning-card" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
            <h4 class="card-header card-title">
                <?= $curriculum['curriculumname'] . ' - ' . $class['topicname'] ?>
            </h4>
            <div class="card-body">
                The class "<?= $class['topicname'] ?>" is currently being used for attendance. Removing this class
                from curriculum "<?= $curriculum['curriculumname'] ?>" will also remove any attendance for this
                class in the curriculum.
                <br /><br />
                Are you sure you want to continue?
            </div>
            <div class="card-footer text-right">
                <a href="<?= $_SERVER['REQUEST_URI'] ?>"><button type="button" class="btn btn-light">No, Cancel</button></a>
                <button type="submit" name="full-delete" class="btn btn-danger">Yes, Remove</button>
            </div>
            <input type="hidden" value="<?= $classId ?>" name="class"/>
        </form>

    <?php } else { ?>

        <a href="/curricula"><button class="cpca btn"><i class="fa fa-arrow-left"></i> Back</button></a>
        <div class="jumbotron form-wrapper mb-3">
            <?php
            if (isset($note)) {
                $notification = new Notification($note['title'], $note['msg'], $note['type']);
                $notification->display();
            }
            ?>
            <h2 class="display-4 text-center" style="font-size: 34px"><?= $curriculum['curriculumname'] ?></h2>
            <form class="form" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>" novalidate>
                <h4>Add New Class</h4>
                <select id="class-selector" class="form-control" name="class" required>
                    <option value="" disabled selected>Select a Class</option>
                    <?php
                    while ($t = pg_fetch_assoc($allTopics)) {
                        ?>
                        <option value="<?= $t['classid'] ?>"><?= $t['topicname'] ?></option>
                        <?php
                    }
                    pg_free_result($allTopics);
                    ?>
                </select>
                <div class="form-footer submit">
                    <button type="submit" class="btn cpca">Add Class</button>
                </div>
            </form>
            <h4>Current Classes <small class="text-muted">(Total: <?= pg_num_rows($topics) ?>)</small></h4>
            <table class="table table-hover table-responsive table-striped table-sm">
                <tbody>
                <?php
                while($class = pg_fetch_assoc($topics)) {
                    ?>
                    <tr>
                        <td class="align-middle">
                            <span><?= $class['topicname'] ?></span>
                        </td>
                        <td class="text-right">
                            <form class="mb-0" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
                                <input type="hidden" name="class" value="<?= $class['classid'] ?>" />
                                <button type="submit" class="btn btn-outline-danger btn-sm" name="remove">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }

                if (pg_num_rows($topics) == 0) {
                    echo '<tr><i>No classes assigned to curriculum.</i></tr>';
                }
                pg_free_result($topics);
                ?>
                </tbody>
            </table>
        </div>

    <?php } ?>
</div>

<?php
include('footer.php');
?>
