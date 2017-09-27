<?php
authorizedPage();
requireRole(Role::Admin | Role::SuperAdmin);

global $params, $route, $view;

include ('../models/Notification.php');

$pages = ['view','edit','create','archive','restore'];

# Update page title to reflect route
if (!empty($params) && in_array($params[0], $pages)) {
    $newTitle = $params[0];
    $route['title'] .= ' - ' . strtoupper($newTitle[0]) . strtolower(substr($newTitle, 1));
}

# Select page to display
if (!empty($params) && $params[0] == 'view') {
    $view->display('curricula/curricula_view.php');
} else if (!empty($params) && $params[0] == 'edit') {
    $view->display('curricula/curricula_modify.php');
} else if (!empty($params) && $params[0] == 'create') {
    $view->display('curricula/curricula_modify.php');
} else if (!empty($params) && $params[0] == 'archive') {
    $view->display('curricula/curricula_archive.php');
} else {
    include('header.php');
    global $db;

    $filter = "";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $filter = isset($_POST['filter']) ? "%" . $_POST['filter'] . "%" : "%%";
        $result = $db->query("SELECT * FROM curricula WHERE LOWER(curriculumname::text) LIKE LOWER($1) " .
            "OR LOWER(curriculumtype::text) LIKE LOWER($1) ORDER BY curriculumname", [$filter]);
    } else {
        $result = $db->query("SELECT * FROM curricula ORDER BY curriculumname", []);
    }

    ?>
    <div style="width: 100%">
        <?php
        if (isset($_SESSION['archive-success']) && $_SESSION['archive-success']) {
            $notification = new Notification('Success!', 'Curriculum was successfully archived!', 'success');
            $notification->display();
            unset($_SESSION['archive-success']);
        }
        ?>
        <div id="curriculum-btn-group" class="input-group">
            <a id="new-curriculum-btn" href="/curricula/create">
                <button class="cpca btn"><i class="fa fa-plus"></i> Create Curriculum</button>
            </a>
            <a id="restore-curriculum-btn" class="ml-3" href="/curricula/restore">
                <button class="btn-outline-secondary btn"><i class="fa fa-repeat"></i> Restore</button>
            </a>
        </div><br />

        <form id="curriculum-filter" action="/curricula" method="post" class="input-group" style="max-width: 500px; width: 100%; margin: 0 auto">
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
                        <h4 class="card-title"><?= $r['curriculumname'] ?></h4>
                        <h6 class="card-subtitle text-muted"><?= $r['curriculumtype'] ?></h6>
                    </div>
                    <div class="card-footer d-flex flex-row justify-content-center">
                        <a href="/curricula/view/<?= $r['curriculumid'] ?>">
                            <button class="btn btn-outline-secondary btn-sm ml-2">View</button>
                        </a>
                        <a href="/curricula/edit/<?= $r['curriculumid'] ?>">
                            <button class="btn btn-outline-secondary btn-sm ml-2">Edit</button>
                        </a>
                        <a href="/curricula/archive/<?= $r['curriculumid'] ?>">
                            <button class="btn btn-outline-danger btn-sm ml-2">Archive</button>
                        </a>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <script>
        $(function() {
            showTutorial('curriculum');
        });
    </script>

    <?php
    include('footer.php');
}
?>