<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays details about a class.
 *
 * This shows class details in subsections. The
 * information about the related curricula are also
 * displayed as well for quick access.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.1.6
 * @since 0.1
 */

global $params, $db;
array_shift($params);

# Get topic name from params
$topicname = rawurldecode(implode('/', $params));

$result = $db->query("SELECT * FROM classes WHERE topicname = $1", [$topicname]);

# If no results, class doesn't exist, redirect
if (pg_num_rows($result) == 0) {
    header('Location: /classes');
    die();
}

$class = pg_fetch_assoc($result);
pg_free_result($result);

$topics = $db->query("SELECT * FROM curriculuminfo WHERE topicname = $1", [$topicname]);

include('header.php');
?>
<div style="width: 100%">
    <button class="cpca btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
    <div class="form-wrapper card view-card">
        <h4 class="card-header text-left">
            <?= $class['topicname'] ?>
            <div class="float-right">
                <a href="/classes/edit/<?= implode('/', $params) ?>"><button class="btn btn-outline-secondary btn-sm">Edit</button></a>
                <a href="/classes/delete/<?= implode('/', $params) ?>"><button class="btn btn-outline-danger btn-sm">Delete</button></a>
            </div>
        </h4>
        <div class="card-body d-flex justify-content-center flex-column">
            <h4>Description</h4>
            <div class="d-flex justify-content-center">
                <?= !empty($class['description']) ? $class['description'] : '<span class="font-italic">No Description</span>' ?>
            </div>
            <br />
            <h4>Curricula</h4>
            <table class="table table-striped table-sm table-hover">
                <tbody>
                <?php
                while($info = pg_fetch_assoc($topics)) {
                    ?>
                    <tr>
                        <td class="pl-2 align-middle"><?= $info['curriculumname'] ?></td>
                        <td class="pr-2 text-right">
                            <a href="/curricula/view/<?= $info['curriculumid'] ?>">
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
                    echo "<p class='text-center text-muted font-italic'>No Curricula Found</p>";
                }
                pg_free_result($topics);
                ?>
            </table>
        </div>
    </div>
</div>
<?php
include('footer.php');