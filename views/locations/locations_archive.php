<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Page that displays archived locations.
 *
 * This page allows a user to restore or fully delete
 * a location from the system. If the class is
 * referenced by attendance, the user will be notified
 * before deletion.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.6
 * @since 0.3.3
 * @deprecated
 */

global $params, $db;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $location = $_POST['location'];
    // Restore
    if (isset($_POST['restore'])) {
        $restoreRes = $db->query("UPDATE sites SET df = 0 WHERE sitename = $1", [$location]);
        if ($restoreRes) {
            $success = true;
            $notificationMsg = 'Location was successfully restored!';
        } else {
            $success = false;
            $notificationMsg = 'Location was not restored!';
        }
    }
    // Delete
    else if (isset($_POST['delete']) && hasRole(Role::Superuser)) {
        $confirmDelete = true;
    }
    // Confirm Delete
    else if (isset($_POST['full-delete']) && hasRole(Role::Superuser)) {
        $fullDeleteRes = $db->query("DELETE FROM sites WHERE sitename = $1", [$location]);
        if ($fullDeleteRes) {
            $state = pg_result_error_field($fullDeleteRes, PGSQL_DIAG_SQLSTATE);
            if ($state == 0) {
                $success = true;
                $notificationMsg = 'Location was successfully removed from the system!';
            } else {
                $success = false;
                $notificationMsg = 'Location was not removed from the system! [' . $state . ']';
            }
        } else {
            $success = false;
            $notificationMsg = 'Location was not removed from the system!';
        }
    }
}

$result = $db->query("SELECT * FROM sites WHERE df = 1 ORDER BY sitename", []);

include('header.php');
?>
    <div style="width: 100%">
        <a href="/locations"><button class="mb-2 cpca btn"><i class="fa fa-arrow-left"></i> Back</button></a>
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
            if (isset($confirmDelete) && $confirmDelete && isset($location)) {
                ?>
                <form class="card warning-card" method="post" action="/locations/archive">
                    <h4 class="card-header card-title">
                        <?= $location ?>
                    </h4>
                    <div class="card-body">
                        You are about to fully delete location "<?= $location ?>". Are you sure
                        you want to fully delete this location?
                    </div>
                    <div class="card-footer text-right">
                        <a href="/locations/archive"><button type="button" class="btn btn-light">Cancel</button></a>
                        <button type="submit" name="full-delete" class="btn btn-danger">Delete</button>
                    </div>
                    <input type="hidden" value="<?= $location ?>" name="location"/>
                </form>
                <?php
            }
            // Show Archived Classes
            else {
                while ($r = pg_fetch_assoc($result)) {
                    ?>
                    <div class="card text-center result-card">
                        <div class="card-body">
                            <h4 class="card-title"><?= $r['sitename'] ?></h4>
                        </div>
                        <div class="card-footer d-flex flex-row justify-content-center">
                            <a>
                                <form class="mb-0" method="POST" action="/locations/archive">
                                    <button class="btn btn-outline-secondary btn-sm ml-2" name="restore">Restore
                                    </button>
                                    <input type="hidden" value="<?= $r['sitename'] ?>" name="location"/>
                                </form>
                            </a>
                            <?php if (hasRole(Role::Superuser)) { ?>
                                <a>
                                    <form class="mb-0" method="POST" action="/locations/archive">
                                        <button class="btn btn-outline-danger btn-sm ml-2" name="delete">Delete</button>
                                        <input type="hidden" value="<?= $r['sitename'] ?>" name="location"/>
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
                        <h3 class="display-3 text-secondary" style="font-size: 40px;">No Locations Archived.</h3>
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
