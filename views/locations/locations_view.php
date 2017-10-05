<?php
global $params, $db;
array_shift($params);

# Get topic name from params
$sitename = rawurldecode(implode('/', $params));

$result = $db->query("SELECT * FROM sites WHERE sitename = $1", [$sitename]);

# If no results, class doesn't exist, redirect
if (pg_num_rows($result) == 0) {
    header('Location: /classes');
    die();
}

$site = pg_fetch_assoc($result);
pg_free_result($result);

include('header.php');
?>
    <div style="width: 100%">
        <a href="/back"><button class="cpca btn"><i class="fa fa-arrow-left"></i> Back</button></a>
        <div class="form-wrapper card view-card">
            <h4 class="card-header text-left">
                <?= $site['sitename'] ?>
                <div class="float-right">
                    <a href="/locations/edit/<?= implode('/', $params) ?>"><button class="btn btn-outline-secondary btn-sm">Edit</button></a>
                    <a href="/locations/delete/<?= implode('/', $params) ?>"><button class="btn btn-outline-danger btn-sm">Delete</button></a>
                </div>
            </h4>
            <div class="card-body d-flex justify-content-center flex-column">
                <h4>Information</h4>
                <div class="d-flex justify-content-center">
                    <div class="display-stack">
                        <div class="display-top"><?= $site['programtype'] ?></div>
                        <div class="display-split"></div>
                        <div class="display-bottom">Program Type</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
include('footer.php');