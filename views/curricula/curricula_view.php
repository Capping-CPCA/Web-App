<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays details about a curriculum.
 *
 * This shows detailed information about the chosen
 * curriculum. Along with curriculum-related information,
 * the related classes are displayed as well for
 * quick access.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.6
 * @since 0.1
 */

global $params, $db;

# Get topic name from params
$id = $params[1];

$result = $db->query("SELECT * FROM curricula WHERE curriculumid = $1", [$id]);

# If no results, curricula doesn't exist, redirect
if (pg_num_rows($result) == 0) {
    header('Location: /curricula');
    die();
}

$curricula = pg_fetch_assoc($result);
pg_free_result($result);

$topics = $db->query("SELECT * FROM curriculumclasses, classes WHERE curriculumid = $1 ".
    "AND curriculumclasses.classid = classes.classid ".
    "AND classes.df = 0 ORDER BY classes.topicname", [$id]);
$curriculaName = $curricula['curriculumname'];
$site = pg_fetch_assoc($db->query("SELECT * FROM sites WHERE sitename = $1", [$curriculaName]));

include('header.php');
?>
<div style="width: 100%">
    <div class="row">
			<div class="col">
				<button class="cpca btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
			</div>
			<div class="col pr-5" align="right">
				<button type="button" class="btn cpca" onclick="window.print()"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
			</div>
		</div>
    <div class="form-wrapper card view-card">
        <h4 class="card-header text-left">
            <?= $curricula['curriculumname'] ?>
            <?php if (hasRole(Role::Coordinator)) { ?>
                <div class="float-right">
                    <a href="/curricula/edit/<?= $id ?>"><button class="btn btn-outline-secondary btn-sm">Edit</button></a>
                    <a href="/curricula/delete/<?= $id ?>"><button class="btn btn-outline-danger btn-sm">Delete</button></a>
                </div>
            <?php } ?>
        </h4>
        <div class="card-body d-flex justify-content-center flex-column">
            <h4>Information</h4>
            <div class="d-flex justify-content-center">
                <div class="display-stack">
                    <div class="display-top"><?= $site['sitetype'] ?></div>
                    <div class="display-split"></div>
                    <div class="display-bottom">Location</div>
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
                                <a href="/classes/view/<?= $class['classid'] ?>">
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