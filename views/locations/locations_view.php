<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays details about a location.
 *
 * This page displays the detailed information
 * about a specific location. From here the user
 * can also edit or delete the entry.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.3.2
 * @since 0.1
 */

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
        <button class="cpca btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
        <div class="form-wrapper card view-card">
            <h4 class="card-header text-left">
                <?= $site['sitename'] ?>
                <?php if (hasRole(Role::Coordinator)) { ?>
                    <div class="float-right">
                        <a href="/locations/edit/<?= implode('/', $params) ?>"><button class="btn btn-outline-secondary btn-sm">Edit</button></a>
                        <a href="/locations/delete/<?= implode('/', $params) ?>"><button class="btn btn-outline-danger btn-sm">Delete</button></a>
                    </div>
                <?php } ?>
            </h4>
            <div class="card-body d-flex justify-content-center flex-column">
                <h4>Information</h4>
                <div class="d-flex justify-content-center">
                    <div class="display-stack">
                        <div class="display-top"><?= $site['sitetype'] ?></div>
                        <div class="display-split"></div>
                        <div class="display-bottom">Location Type</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
include('footer.php');