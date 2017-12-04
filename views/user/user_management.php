<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Page that displays active employees.
 *
 * This page displays employees in a list
 * where administrators or superusers can
 * easily view or delete them. Superusers
 * also have the ability to access the user
 * archives from this page.
 *
 * @author Michelle Crawley
 * @copyright 2017 Marist College
 * @version 0.3.3
 * @since 0.1
 */

include ('../models/Notification.php');
global $db, $view, $route, $params;
$pages = ['restore'];

# Update page title to reflect route
if (!empty($params) && in_array($params[0], $pages)) {
    $newTitle = $params[0];
    $route['title'] .= ' - ' . strtoupper($newTitle[0]) . strtolower(substr($newTitle, 1));
}

# Select page to display
if (!empty($params) && $params[0] == 'restore') {
    $view->display('user/user_restore.php');
} else if (!empty($params) && $params[0] == 'archives'){
    $view->display('user/user_archives.php');
} else {
    $res = $db->query("SELECT firstname, lastname, employeeid FROM people, employees ".
        "WHERE employees.employeeid = people.peopleid AND employees.df = FALSE ORDER BY lastname, firstname", []);
    include('header.php');
    ?>
    <div style="width: 100%">
        <?php
        if (isset($_SESSION['notification'])) {
            $note = $_SESSION['notification'];
            $notification = new Notification($note['title'], $note['msg'], $note['type']);
            $notification->display();
            unset($_SESSION['notification']);
        }
        ?>
        <div id="curriculum-btn-group" class="input-group">
            <?php
            # Make sure the logged in user is a Superuser
            if (hasRole(Role::Superuser)) {
                ?>
                <a id="restore-curriculum-btn" class="ml-3" href="/manage-users/archives">
                    <button class="btn-outline-secondary btn"><i class="fa fa-archive"></i> See Archive</button>
                </a>
            <?php } ?>
        </div>
        <br/>
        <form class="jumbotron form-wrapper mb-3">
            <?php if (pg_num_rows($res) != 0) { ?>
                <h3 class="text-center" style="font-weight: 300;color: #333">Current Users</h3>
                <?php
                # Displays the active users
                while ($user = pg_fetch_assoc($res)) {
                    # Get if user is a superuser
                    $eId = $user['employeeid'];
                    $result = $db->query("SELECT superuserID FROM superusers WHERE superuserid = $1", [$eId]);
                    $isSuperUser = pg_fetch_assoc($result);
                    ?>
                    <div class="card mb-2 user-card">
                        <div class="p-3 card-body d-flex flex-row justify-content-start">
                            <p class="mb-0 align-self-center"
                               style="flex: 1"><?= ucwords($user['lastname'] . ', ' . $user['firstname']); ?></p>
                            <div class="text-right align-middle">
                                <a href="/account-settings/<?= $user['employeeid'] ?>" style="text-decoration: none;">
                                    <button type='button' class="btn outline-cpca btn-sm ml-2">View</button>
                                </a>
                                <?php if (($user['employeeid'] != $_SESSION['employeeid']) &&
                                    (($isSuperUser && hasRole(Role::Superuser)) ||
                                        (!$isSuperUser && hasRole(Role::Admin)))) { ?>
                                    <a href="/account-settings/delete/<?= $user['employeeid'] ?>">
                                        <button type='button' class="btn btn-outline-danger btn-sm ml-2">Delete</button>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else { ?>
            <!-- Displays a pretty message if there are no users -->
                <div class="w-100 d-flex flex-column justify-content-center text-center">
                    <h3 class="display-3 text-secondary" style="font-size: 40px;"><i
                                class="fa fa-exclamation-circle"></i></h3>
                    <h3 class="display-3 text-secondary" style="font-size: 40px;">No Current Users.</h3>
                </div>
            <?php }
            pg_free_result($res);
            ?>
        </form>

    </div>
    <?php
    include('footer.php');
} ?>