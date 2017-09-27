<?php
authorizedPage();

global $params, $route, $view;

include ('../models/Notification.php');

$pages = ['view', 'edit', 'create', 'archive', 'restore'];

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
} else if (!empty($params) && $params[0] == 'archive') {
    $view->display('locations/locations_archive.php');
} else {
    include('header.php');
    global $db;

    $filter = "";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $filter = isset($_POST['filter']) ? '%' . $_POST['filter'] . '%' : '%%';
        $result = $db->query("SELECT * FROM sites WHERE LOWER(sitename) LIKE LOWER($1)".
            " OR LOWER(programtype::text) LIKE LOWER($1) ORDER BY sitename", [$filter]);
    } else {
        $result = $db->query("SELECT * FROM sites ORDER BY sitename", []);
    }

    ?>
    <div style="width: 100%">
        <?php
        if (isset($_SESSION['archive-success']) && $_SESSION['archive-success']) {
            $notification = new Notification('Success!', 'Location was successfully archived!', 'success');
            $notification->display();
            unset($_SESSION['archive-success']);
        }
        ?>
        <div id="location-btn-group" class="input-group">
            <a id="new-location-btn" href="/locations/create">
                <button class="cpca btn"><i class="fa fa-plus"></i> Create Location</button>
            </a>
            <a id="restore-location-btn" class="ml-3" href="/locations/restore">
                <button class="btn-outline-secondary btn"><i class="fa fa-repeat"></i> Restore</button>
            </a>
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
                ?>
                <div class="card text-center result-card">
                    <div class="card-body">
                        <h4 class="card-title"><?= $r['sitename'] ?></h4>
                        <h6 class="card-subtitle text-muted"><?= $r['programtype'] ?></h6>
                    </div>
                    <div class="card-footer d-flex flex-row justify-content-center">
                        <a href="/locations/view/<?= $r['sitename'] ?>">
                            <button class="btn btn-outline-secondary btn-sm ml-2">View</button>
                        </a>
                        <a href="/locations/edit/<?= $r['sitename'] ?>">
                            <button class="btn btn-outline-secondary btn-sm ml-2">Edit</button>
                        </a>
                        <a href="/locations/archive/<?= $r['sitename'] ?>">
                            <button class="btn btn-outline-danger btn-sm ml-2">Archive</button>
                        </a>
                    </div>
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