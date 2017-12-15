<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Page that displays the current classes.
 *
 * This page displays classes in a grid-like view
 * where users can then view, edit, or delete
 * classes that appear. Superusers can view archived
 * classes and restore them or fully delete them.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.6
 * @since 0.1
 */

global $params, $route, $view;

include ('../models/Notification.php');

$pages = ['view','edit','create','delete','archive'];

# Update page title to reflect route
if (!empty($params) && in_array($params[0], $pages)) {
    $newTitle = $params[0];
    $route['title'] .= ' - ' . strtoupper($newTitle[0]) . strtolower(substr($newTitle, 1));
}

# Select page to display
if (!empty($params) && $params[0] == 'view') {
    $view->display('classes/classes_view.php');
} else if (!empty($params) && $params[0] == 'edit') {
    $view->display('classes/classes_modify.php');
} else if (!empty($params) && $params[0] == 'create') {
    $view->display('classes/classes_modify.php');
} else if (!empty($params) && $params[0] == 'delete') {
    $view->display('classes/classes_delete.php');
} else if (!empty($params) && $params[0] == 'archive') {
    $view->display('classes/classes_archives.php');
} else {
    include('header.php');
    global $db;

    $filter = "";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $filter = isset($_POST['filter']) ? '%' . $_POST['filter'] . '%' : '%%';
        $result = $db->query("SELECT * FROM classes WHERE df IS FALSE AND LOWER(topicname) LIKE LOWER($1)".
            " OR LOWER(description) LIKE LOWER($1) ORDER BY topicname", [$filter]);
    } else {
        $result = $db->query("SELECT * FROM classes WHERE df IS FALSE ORDER BY topicname", []);
    }

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
        <div id="classes-btn-group" class="input-group">
            <?php if (hasRole(Role::Coordinator)) { ?>
                <a id="new-class-btn" href="/classes/create">
                    <button class="cpca btn"><i class="fa fa-plus"></i> Create Class</button>
                </a>
            <?php
            }
            if (hasRole(Role::Admin)) {
            ?>
                <a id="restore-class-btn" class="ml-3" href="/classes/archive">
                    <button class="btn-outline-secondary btn"><i class="fa fa-archive"></i> See Archive</button>
                </a>
            <?php } ?>
        </div><br />

        <form id="class-filter" action="/classes" method="post" class="input-group" style="max-width: 500px; width: 100%; margin: 0 auto">
            <input type="text" class="form-control" placeholder="Filter for..." name="filter" value="<?= str_replace('%', '', $filter) ?>">
            <span class="input-group-btn">
                <button class="btn btn-secondary" type="submit">Search</button>
            </span>
        </form>
        <br />
        <div class="d-flex flex-row justify-content-center flex-wrap">
            <?php
            while ($r = pg_fetch_assoc($result)) {
                $topicname = htmlentities($r['topicname']);
                // Adds a zero-width space to break words after a forward slash
                $topicname = str_replace('/', '/&#8203;', $topicname);
                ?>
                <div class="card text-center result-card">
                    <div class="card-body">
                        <h4 class="card-title"><?= $topicname ?></h4>
                        <h6 class="card-subtitle text-muted"><?= htmlentities($r['description']) ?></h6>
                    </div>
                    <div class="card-footer d-flex flex-row justify-content-center">
                        <a href="/classes/view/<?= $r['classid'] ?>">
                            <button class="btn btn-outline-secondary btn-sm ml-2">View</button>
                        </a>
                        <?php if (hasRole(Role::Coordinator)) { ?>
                            <a href="/classes/edit/<?= $r['classid'] ?>">
                                <button class="btn btn-outline-secondary btn-sm ml-2">Edit</button>
                            </a>
                            <a href="/classes/delete/<?= $r['classid'] ?>">
                                <button class="btn btn-outline-danger btn-sm ml-2">Delete</button>
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
                    <h3 class="display-3 text-secondary" style="font-size: 40px;">No Current Classes.</h3>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <script>
        $(function() {
            showTutorial('classes');
        });
    </script>

    <?php
    include('footer.php');
}
?>