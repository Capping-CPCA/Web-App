<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Page that displays the current locations.
 *
 * This page displays locations in a grid-like view
 * where users can then view, edit, or delete
 * locations that appear. Superusers can view archived
 * locations and restore them or fully delete them.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.3.2
 * @since 0.1
 */

global $params, $route, $view;

include ('../models/Notification.php');

$pages = ['view', 'edit', 'create', 'delete'];

# Update page title to reflect route
if (!empty($params) && in_array($params[0], $pages)) {
    $newTitle = $params[0];
    $route['title'] .= ' - ' . strtoupper($newTitle[0]) . strtolower(substr($newTitle, 1));
}

# Select page to display
if (!empty($params) && $params[0] == 'view') {
    $view->display('locations/locations_view.php');
} else if (!empty($params) && $params[0] == 'edit') {
    $view->display('locations/locations_modify.php');
} else if (!empty($params) && $params[0] == 'create') {
    $view->display('locations/locations_modify.php');
} else if (!empty($params) && $params[0] == 'delete') {
    $view->display('locations/locations_delete.php');
} else {
    include('header.php');
    global $db;

    $filter = "";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $filter = isset($_POST['filter']) ? '%' . $_POST['filter'] . '%' : '%%';
        $result = $db->query("SELECT * FROM sites WHERE LOWER(sitename) LIKE LOWER($1)".
            " OR LOWER(sitetype::text) LIKE LOWER($1) ORDER BY sitename", [$filter]);
    } else {
        $result = $db->query("SELECT * FROM sites ORDER BY sitename", []);
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
        <div id="location-btn-group" class="input-group">
            <?php if (hasRole(Role::Coordinator)) { ?>
                <a id="new-location-btn" href="/locations/create">
                    <button class="cpca btn"><i class="fa fa-plus"></i> Create Location</button>
                </a>
            <?php } ?>
        </div><br />

        <form id="location-filter" action="/locations" method="post" class="input-group" style="max-width: 500px; width: 100%; margin: 0 auto">
            <input type="text" class="form-control" placeholder="Filter for..." name="filter" value="<?= str_replace('%', '', $filter) ?>">
            <span class="input-group-btn">
                <button class="btn btn-secondary" type="submit">Search</button>
            </span>
        </form>
        <br />
        <div class="d-flex flex-row justify-content-center flex-wrap">
            <?php
            while ($r = pg_fetch_assoc($result)) {
                $sitename = htmlentities($r['sitename']);
                // Adds a zero-width space to break words after a forward slash
                $sitename = str_replace('/', '/&#8203;', $sitename);
                ?>
                <div class="card text-center result-card">
                    <div class="card-body">
                        <h4 class="card-title"><?= $sitename ?></h4>
                        <h6 class="card-subtitle text-muted"><?= $r['sitetype'] ?></h6>
                    </div>
                    <div class="card-footer d-flex flex-row justify-content-center">
                        <a href="/locations/view/<?= urlencode($r['sitename']) ?>">
                            <button class="btn btn-outline-secondary btn-sm ml-2">View</button>
                        </a>
                        <?php if (hasRole(Role::Coordinator)) { ?>
                            <a href="/locations/edit/<?= urlencode($r['sitename']) ?>">
                                <button class="btn btn-outline-secondary btn-sm ml-2">Edit</button>
                            </a>
                            <a href="/locations/delete/<?= urlencode($r['sitename']) ?>">
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
                    <h3 class="display-3 text-secondary" style="font-size: 40px;">No Current Locations.</h3>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
    include('footer.php');
}
?>