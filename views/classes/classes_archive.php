<?php

global $params, $db;
array_shift($params);

# Get topic name from params
$topicname = rawurldecode(implode('/', $params));

$db->prepare("get_class", "SELECT * FROM classes WHERE topicname = $1");
$result = $db->execute("get_class", [$topicname]);

# If no results, class doesn't exist, redirect
if (pg_num_rows($result) == 0) {
    header('Location: /classes');
    die();
}

$class = pg_fetch_assoc($result);
pg_free_result($result);

# Archive data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['archive'])) {
    // TODO: archive class
    $_SESSION['archive-success'] = true;
    header('Location: /classes');
    die();
}

include('header.php');
?>

    <div class="page-wrapper">
        <form class="card warning-card" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
            <h4 class="card-header card-title">
                <?= $class['topicname'] ?>
            </h4>
            <div class="card-body">
                You are about to archive class "<?= $class['topicname'] ?>". Are you sure
                you want to archive this class?
            </div>
            <div class="card-footer text-right">
                <a href="/back"><button type="button" class="btn btn-light">Cancel</button></a>
                <button type="submit" name="archive" class="btn btn-danger">Archive</button>
            </div>
        </form>
    </div>

<?php
include('footer.php');