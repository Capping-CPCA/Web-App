<?php
global $params, $db;
$id = $params[1];

$result = $db->query("SELECT * FROM curricula WHERE curriculumid = $1", [$id]);

# If no results, curricula doesn't exist, redirect
if (pg_num_rows($result) == 0) {
    header('Location: /curricula');
    die();
}

$curricula = pg_fetch_assoc($result);
pg_free_result($result);

$topics = $db->query("SELECT * FROM curriculumclasses WHERE curriculumid = $1", [$id]);

include('header.php');
?>
<div style="width: 100%">
    <a href="/back"><button class="cpca btn"><i class="fa fa-arrow-left"></i> Back</button></a>
    <div class="form-wrapper card view-card">
        <h4 class="card-header text-left">
            <?= $curricula['curriculumname'] ?>
            <div class="float-right">
                <a href="/curricula/edit/<?= $id ?>"><button class="btn btn-outline-secondary btn-sm">Edit</button></a>
                <a href="/curricula/delete/<?= $id ?>"><button class="btn btn-outline-danger btn-sm">Delete</button></a>
            </div>
        </h4>
        <div class="card-body d-flex justify-content-center flex-column">
            <h4>Information</h4>
            <div class="d-flex justify-content-center">
                <div class="display-stack">
                    <div class="display-top"><?= $curricula['curriculumtype'] ?></div>
                    <div class="display-split"></div>
                    <div class="display-bottom">Type</div>
                </div>
                <div class="display-stack">
                    <div class="display-top"><?= $curricula['missnumber'] ?></div>
                    <div class="display-split"></div>
                    <div class="display-bottom">Max. Missed Classes</div>
                </div>
            </div>
            <br />
            <h4>Classes</h4>
            <table class="table table-striped table-sm table-hover">
                <tbody>
                    <?php
                    while($class = pg_fetch_assoc($topics)) {
                        ?>
                        <tr>
                            <td class="pl-2 align-middle"><?= $class['topicname'] ?></td>
                            <td class="pr-2 text-right">
                                <a href="/classes/view/<?= $class['topicname'] ?>">
                                    <button class="btn btn-outline-secondary btn-sm">View</button>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <?php
                if (pg_num_rows($topics) == 0) {
                    echo "<p class='text-center text-muted font-italic'>No Classes Found</p>";
                }
                pg_free_result($topics);
                ?>
            </table>
        </div>
    </div>
</div>
<?php
include('footer.php');