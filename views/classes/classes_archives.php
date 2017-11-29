<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Page that displays archived classes.
 *
 * This page allows a user to restore or fully delete
 * a class from the system. If the class is
 * referenced by attendance, the user will be notified
 * before deletion.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 1.0
 * @since 0.3.3
 */

global $params, $db;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class = $_POST['class'];
    // Restore
    if (isset($_POST['restore'])) {
        $restoreRes = $db->query("UPDATE classes SET df = FALSE WHERE classid = $1", [$class]);
        if ($restoreRes) {
            $success = true;
            $notificationMsg = 'Class was successfully restored!';
        } else {
            $success = false;
            $notificationMsg = 'Class was not restored!';
        }
    }
    // Delete
    else if (isset($_POST['delete']) && hasRole(Role::Superuser)) {
        $confirmDelete = true;
        $res = $db->query("SELECT * FROM classoffering WHERE classid = $1", [$class]);
        if (pg_num_rows($res) > 0) {
            $warning = true;
        }
        $res = $db->query("SELECT * FROM curriculumclasses WHERE classid = $1", [$class]);
        if (pg_num_rows($res) > 0) {
            $warning = true;
        }
    }
    // Confirm Delete
    else if (isset($_POST['full-delete']) && hasRole(Role::Superuser)) {
        $errorState = "";
        // Delete from Class Offering
        $res = $db->query("DELETE FROM classoffering ".
            "WHERE classid = $1 ", [$class]);
        $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
        if ($state != 0) {
            $error = true;
            $errorState .= $state . ":CO ";
        }
        // Delete from Curriculum Classes
        $res = $db->query("DELETE FROM curriculumclasses".
            " WHERE classid = $1", [$class]);
        $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
        if ($state != 0) {
            $error = true;
            $errorState .= $state . ":CC";
        }

        // Error deleting connections
        if (isset($error)) {
            str_replace(" ", ", ", trim($errorState));
            $note['title'] = 'Error!';
            $note['msg'] = "The class could not be fully deleted [$errorState]";
            $note['type'] = 'danger';
        }
        // Remove actual curriculum-class connection
        else {
            $fullDeleteRes = $db->query("DELETE FROM classes WHERE classid = $1", [$class]);
            if ($fullDeleteRes) {
                $state = pg_result_error_field($fullDeleteRes, PGSQL_DIAG_SQLSTATE);
                if ($state == 0) {
                    $success = true;
                    $notificationMsg = 'Class was successfully removed from the system!';
                } else {
                    $success = false;
                    $notificationMsg = 'Class was not removed from the system! [' . $state . ']';
                }
            } else {
                $success = false;
                $notificationMsg = 'Class was not removed from the system!';
            }
        }
    }
}

$result = $db->query("SELECT * FROM classes WHERE df IS TRUE ORDER BY topicname", []);

include('header.php');
?>
    <div style="width: 100%">
        <a href="/classes"><button class="mb-2 cpca btn"><i class="fa fa-arrow-left"></i> Back</button></a>
        <?php
        if (isset($success) && isset($notificationMsg)) {
            if ($success)
                $notification = new Notification('Success!', $notificationMsg, 'success');
            else
                $notification = new Notification('Error!', $notificationMsg, 'danger');
            $notification->display();
        }
        ?>
        <div class="d-flex flex-row justify-content-center flex-wrap">
            <?php
            // Confirm Full Delete
            if (isset($confirmDelete) && $confirmDelete && isset($class)) {

                $c = pg_fetch_assoc($db->query("SELECT topicname FROM classes WHERE classid = $1", [$class]));
                $className = $c['topicname'];

                ?>
                <form class="card warning-card" method="post" action="/classes/archive">
                    <h4 class="card-header card-title">
                        <?= $className ?>
                    </h4>
                    <div class="card-body">
                        <?php
                        if(isset($warning)) {
                            echo "This class is currently being used for attendance. Fully deleting this class will also delete the ".
                                "attendance for this class.<br /><br />Are you sure you want to continue?";
                        } else {
                            echo "You are about to fully delete class \"$className\". Are you sure you want to fully delete this class?";
                        }
                        ?>
                    </div>
                    <div class="card-footer text-right">
                        <a href="/classes/archive"><button type="button" class="btn btn-light">Cancel</button></a>
                        <button type="submit" name="full-delete" class="btn btn-danger">Delete</button>
                    </div>
                    <input type="hidden" value="<?= $class ?>" name="class"/>
                </form>
                <?php
            }
            // Show Archived Classes
            else {
                while ($r = pg_fetch_assoc($result)) {
                    ?>
                    <div class="card text-center result-card">
                        <div class="card-body">
                            <h4 class="card-title"><?= $r['topicname'] ?></h4>
                            <h6 class="card-subtitle text-muted"><?= $r['description'] ?></h6>
                        </div>
                        <div class="card-footer d-flex flex-row justify-content-center">
                            <a>
                                <form class="mb-0" method="POST" action="/classes/archive">
                                    <button class="btn btn-outline-secondary btn-sm ml-2" name="restore">Restore
                                    </button>
                                    <input type="hidden" value="<?= $r['classid'] ?>" name="class"/>
                                </form>
                            </a>
                            <?php if (hasRole(Role::Superuser)) { ?>
                                <a>
                                    <form class="mb-0" method="POST" action="/classes/archive">
                                        <button class="btn btn-outline-danger btn-sm ml-2" name="delete">Delete</button>
                                        <input type="hidden" value="<?= $r['classid'] ?>" name="class"/>
                                    </form>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                }
                if (pg_num_rows($result) == 0) {
                    ?>
                    <div class="w-100 d-flex flex-column justify-content-center text-center">
                        <h3 class="display-3 text-secondary" style="font-size: 40px;"><i
                                    class="fa fa-exclamation-circle"></i></h3>
                        <h3 class="display-3 text-secondary" style="font-size: 40px;">No Classes Archived.</h3>
                    </div>
                    <?php
                }
            }

            pg_free_result($result);
            ?>
        </div>
    </div>
<?php
include('footer.php');
