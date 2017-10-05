<?php

global $params, $db;
$id = $params[1];
$db->prepare("get_curriculum","SELECT * FROM curricula WHERE curriculumid = $1");
$result = $db->execute("get_curriculum", [$id]);

# If no results, curricula doesn't exist, redirect
if (pg_num_rows($result) == 0) {
    header('Location: /curricula');
    die();
}

$curricula = pg_fetch_assoc($result);
pg_free_result($result);

# Archive data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    // TODO: delete curricula
    $_SESSION['delete-success'] = true;
    header('Location: /curricula');
    die();
}

include('header.php');
?>

<div class="page-wrapper">
    <form class="card warning-card" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
        <h4 class="card-header card-title">
            <?= $curricula['curriculumname'] ?>
            <small class="card-subtitle text-muted">(<?= $curricula['curriculumtype'] ?>)</small>
        </h4>
        <div class="card-body">
            You are about to delete curriculum "<?= $curricula['curriculumname'] ?>". Are you sure
            you want to delete this curriculum?
        </div>
        <div class="card-footer text-right">
            <a href="/back"><button type="button" class="btn btn-light">Cancel</button></a>
            <button type="submit" name="delete" class="btn btn-danger">Delete</button>
        </div>
    </form>
</div>

<?php
include('footer.php');