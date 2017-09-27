<?php
authorizedPage();

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
    $view->display('classes/classes_view.php');
} else if (!empty($params) && $params[0] == 'edit') {
    $view->display('classes/classes_modify.php');
} else if (!empty($params) && $params[0] == 'create') {
    $view->display('classes/classes_modify.php');
} else if (!empty($params) && $params[0] == 'archive') {
    $view->display('classes/classes_archive.php');
} else {
    include('header.php');
    global $db;

    $filter = "";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $filter = isset($_POST['filter']) ? '%' . $_POST['filter'] . '%' : '%%';
        $result = $db->query("SELECT * FROM classes WHERE LOWER(topicname) LIKE LOWER($1)".
            " OR LOWER(description) LIKE LOWER($1) ORDER BY topicname", [$filter]);
    } else {
        $result = $db->query("SELECT * FROM classes ORDER BY topicname", []);
    }

    ?>
    <div style="width: 100%">
        <?php
        if (isset($_SESSION['archive-success']) && $_SESSION['archive-success']) {
            $notification = new Notification('Success!', 'Class was successfully archived!', 'success');
            $notification->display();
            unset($_SESSION['archive-success']);
        }
        ?>
        <div id="classes-btn-group" class="input-group">
            <a id="new-class-btn" href="/classes/create">
                <button class="cpca btn"><i class="fa fa-plus"></i> Create Class</button>
            </a>
            <a id="restore-class-btn" class="ml-3" href="/classes/restore">
                <button class="btn-outline-secondary btn"><i class="fa fa-repeat"></i> Restore</button>
            </a>
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
                ?>
                <div class="card text-center result-card">
                    <div class="card-body">
                        <h4 class="card-title"><?= $r['topicname'] ?></h4>
                        <h6 class="card-subtitle text-muted"><?= $r['description'] ?></h6>
                    </div>
                    <div class="card-footer d-flex flex-row justify-content-center">
                        <a href="/classes/view/<?= $r['topicname'] ?>">
                            <button class="btn btn-outline-secondary btn-sm ml-2">View</button>
                        </a>
                        <a href="/classes/edit/<?= $r['topicname'] ?>">
                            <button class="btn btn-outline-secondary btn-sm ml-2">Edit</button>
                        </a>
                        <a href="/classes/archive/<?= $r['topicname'] ?>">
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