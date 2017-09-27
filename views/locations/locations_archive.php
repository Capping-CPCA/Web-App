<?php

global $params, $db;
array_shift($params);

# Get site name from params
$sitename = rawurldecode(implode('/', $params));

$db->prepare("get_site", "SELECT * FROM sites WHERE sitename = $1");
$result = $db->execute("get_site", [$sitename]);

# If no results, site doesn't exist, redirect
if (pg_num_rows($result) == 0) {
    header('Location: /locations');
    die();
}

$site = pg_fetch_assoc($result);
pg_free_result($result);

# Archive data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['archive'])) {
    // TODO: archive class
    $_SESSION['archive-success'] = true;
    header('Location: /locations');
    die();
}

include('header.php');
?>

    <div class="page-wrapper">
        <form class="card warning-card" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
            <h4 class="card-header card-title">
                <?= $site['sitename'] ?>
            </h4>
            <div class="card-body">
                You are about to archive location "<?= $site['sitename'] ?>". Are you sure
                you want to archive this location?
            </div>
            <div class="card-footer text-right">
                <a href="/back"><button type="button" class="btn btn-light">Cancel</button></a>
                <button type="submit" name="archive" class="btn btn-danger">Archive</button>
            </div>
        </form>
    </div>

<?php
include('footer.php');