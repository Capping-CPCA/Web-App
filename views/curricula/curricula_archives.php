<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Page that displays archived curricula.
 *
 * This page allows a user to restore or fully delete
 * a curriculum from the system. If the curriculum is
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
    $curriculumId = $_POST['id'];
    // Restore
    if (isset($_POST['restore'])) {
        $restoreRes = $db->query("UPDATE curricula SET df = FALSE WHERE curriculumid = $1", [$curriculumId]);
        if ($restoreRes) {
            $success = true;
            $notificationMsg = 'Curriculum was successfully restored!';
        } else {
            $success = false;
            $notificationMsg = 'Curriculum was not restored!';
        }
    }
    // Delete
    else if (isset($_POST['delete']) && hasRole(Role::Superuser)) {
        $confirmDelete = true;
        $res = $db->query("SELECT * FROM classoffering WHERE curriculumid = $1", [$curriculumId]);
        if (pg_num_rows($res) > 0) {
            $warning = true;
        }
        $res = $db->query("SELECT * FROM curriculumclasses WHERE curriculumid = $1", [$curriculumId]);
        if (pg_num_rows($res) > 0) {
            $warning = true;
        }
    }
    // Confirm Delete
    else if (isset($_POST['full-delete']) && hasRole(Role::Superuser)) {
        $errorState = "";
        // Delete from Class Offering
        $res = $db->query("DELETE FROM classoffering ".
            "WHERE curriculumid = $1 ", [$curriculumId]);
        $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
        if ($state != 0) {
            $error = true;
            $errorState .= $state . ":CO ";
        }

        $currRes = pg_fetch_assoc($db->query("SELECT * FROM curricula WHERE curriculumid = $1", [$curriculumId]));
        $currName = $currRes['curriculumname'];
        $res = $db->query("DELETE FROM sites WHERE sitename = $1", [$currName]);
        $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
        if ($state != 0) {
            $error = true;
            $errorState .= $state . ":S ";
        }

        // Delete from Curriculum Classes
        $res = $db->query("DELETE FROM curriculumclasses".
            " WHERE curriculumid = $1", [$curriculumId]);
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
            $fullDeleteRes = $db->query("DELETE FROM curricula WHERE curriculumid = $1", [$curriculumId]);
            if ($fullDeleteRes) {
                $state = pg_result_error_field($fullDeleteRes, PGSQL_DIAG_SQLSTATE);
                if ($state == 0) {
                    $success = true;
                    $notificationMsg = 'Curriculum was successfully removed from the system!';
                } else {
                    $success = false;
                    $notificationMsg = 'Curriculum was not removed from the system! [' . $state . ']';
                }
            } else {
                $success = false;
                $notificationMsg = 'Curriculum was not removed from the system!';
            }
        }
    }
}

$result = $db->query("SELECT * FROM curricula WHERE df IS TRUE ORDER BY curriculumname", []);

include('header.php');
?>

    <div style="width: 100%">
        <a href="/curricula"><button class="mb-2 cpca btn"><i class="fa fa-arrow-left"></i> Back</button></a>
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
            if (isset($confirmDelete) && $confirmDelete && isset($curriculumId)) {
                $curriculum = pg_fetch_assoc($db->query("SELECT * FROM curricula WHERE curriculumid = $1", [$curriculumId]));
                ?>
                <form class="card warning-card" method="post" action="/curricula/archive">
                    <h4 class="card-header card-title">
                        <?= $curriculum['curriculumname'] ?>
                    </h4>
                    <div class="card-body">
                        <?php
                        if(isset($warning)) {
                            echo "This curriculum is currently being used for attendance. Fully deleting this curriculum will also delete the ".
                                "attendance for this curriculum.<br /><br />Are you sure you want to continue?";
                        } else {
                            echo "You are about to fully delete curriculum \"".$curriculum['curriculumname']."\". Are you sure you want to fully delete this curriculum?";
                        }
                        ?>
                    </div>
                    <div class="card-footer text-right">
                        <a href="/curricula/archive"><button type="button" class="btn btn-light">Cancel</button></a>
                        <button type="submit" name="full-delete" class="btn btn-danger">Delete</button>
                    </div>
                    <input type="hidden" value="<?= $curriculumId ?>" name="id"/>
                </form>
                <?php
            }
            else {
                while ($r = pg_fetch_assoc($result)) {
                    ?>
                    <div class="card text-center result-card">
                        <div class="card-body">
                            <h4 class="card-title"><?= $r['curriculumname'] ?></h4>
                        </div>
                        <div class="card-footer d-flex flex-row justify-content-center">
                            <a>
                                <form class="mb-0" method="POST" action="/curricula/archive">
                                    <button class="btn btn-outline-secondary btn-sm ml-2" name="restore">Restore
                                    </button>
                                    <input type="hidden" value="<?= $r['curriculumid'] ?>" name="id"/>
                                </form>
                            </a>
                            <?php if (hasRole(Role::Superuser)) { ?>
                                <a>
                                    <form class="mb-0" method="POST" action="/curricula/archive">
                                        <button class="btn btn-outline-danger btn-sm ml-2" name="delete">Delete</button>
                                        <input type="hidden" value="<?= $r['curriculumid'] ?>" name="id"/>
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
                        <h3 class="display-3 text-secondary" style="font-size: 40px;">No Curricula Archived.</h3>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>

<?php
include('footer.php');
