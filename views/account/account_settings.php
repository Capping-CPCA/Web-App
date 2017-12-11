<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays a page to allow class editing.
 *
 * This page provides various sections to allow an
 * admin to edit details about a class. Once the form
 * is filled out, if there are any errors, they will
 * be displayed upon submission.
 *
 * @author Michelle Crawley
 * @copyright 2017 Marist College
 * @version 0.3.3
 * @since 0.1
 */

include ('../models/Notification.php');
global $db, $view, $route, $params;
$pages = ['modify', 'delete'];

# Update page title to reflect route
if (!empty($params) && in_array($params[0], $pages)) {
    $newTitle = $params[0];
    $route['title'] .= ' - ' . strtoupper($newTitle[0]) . strtolower(substr($newTitle, 1));
}

# Select page to display
if (!empty($params) && $params[0] == 'modify') {
    $view->display('account/account_modify.php');
} else if (!empty($params) && $params[0] == 'delete') {
    $view->display('account/account_archive.php');
} else {
    include('header.php');
    $employeeid = $params[0];
    if (($_SESSION['employeeid'] != $employeeid) && (!(hasRole(Role::Admin)))) {
        header('Location: /dashboard');
        die();
    } else {
        # Get if user is a superuser
        $result = $db->query("SELECT superuserID FROM superusers WHERE superuserid = $1", [$employeeid]);
        $isSuperUser = pg_fetch_assoc($result);

        $result = $db->query("SELECT people.firstName, people.middleInit, people.lastName, employees.email, employees.primaryPhone, employees.permissionLevel " .
            "FROM employees " .
            "LEFT JOIN people ON employees.employeeid = people.peopleid
             WHERE employees.employeeid=$1", [$employeeid]);
        $employee = pg_fetch_assoc($result);
        extract($employee);

        $result = $db->query("SELECT facilitatorid " . "FROM facilitators WHERE facilitatorid = $1 AND df = 0", [$employeeid]);
        $isFacilitator = pg_fetch_assoc($result);
        $employee['isFacilitator'] = $isFacilitator;
        if ($isFacilitator) {
            $result = $db->query("SELECT lang " . "FROM facilitatorlanguage " . "WHERE facilitatorid = $1 AND level = 'PRIMARY'", [$employeeid]);
            $primaryLang = pg_fetch_assoc($result);
            $result = $db->query("SELECT lang " . "FROM facilitatorlanguage " . "WHERE facilitatorid = $1 AND level = 'SECONDARY'", [$employeeid]);
            $secondaryLang = pg_fetch_all($result);
            $employee['primaryLang'] = $primaryLang;
            $employee['secondaryLang'] = $secondaryLang;
        }
        $employeeJSON = json_encode($employee);
        ?>
        <div class="w-100" style="height: fit-content;">
            <?php
            if (isset($_SESSION['notification'])) {
                $note = $_SESSION['notification'];
                $notification = new Notification($note['title'], $note['msg'], $note['type']);
                $notification->display();
                unset($_SESSION['notification']);
            }
            ?>
            <button class="cpca btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
            <div class="card" style="max-width: 700px; width: 100%; margin: 0 auto;">
                <div class="card-header">
                    <h4 class="modal-title" style="float: left;"><?= $firstname." ".$middleinit." ".$lastname ?></h4>
                    <div class="float-right" style="display: inline!important;">
                        <div class="float-right">
                            <?php if (($isSuperUser && hasRole(Role::Superuser))  ||  (!$isSuperUser && hasRole(Role::Admin))) { ?>
                                <a href="/account-settings/modify/<?= implode('/', $params) ?>"><button class="btn btn-outline-secondary btn-sm">Edit</button></a>
                            <?php if ($employeeid != $_SESSION['employeeid']) { ?>
                                <a href="/account-settings/delete/<?= implode('/', $params) ?>"><button class="btn btn-outline-danger btn-sm">Delete</button></a>
                            <?php } } ?>
                        </div>
                    </div>
                    </h4>
                </div>
                <div class="card-body">
                    <h4 class="thin-title">Information</h4>
                    <hr>
                    <div class="pl-3">
                        <?php if ($isFacilitator) {?>
                            <p class="account_languages"><b>Language(s): </b> <?=$primaryLang['lang']?> (Primary)<?php if ($secondaryLang) { foreach ($secondaryLang as $lang) { ?>, <?= $lang['lang']?> (Secondary) <?php } } ?></p>
                        <?php } ?>
                        <p class="is_facilitator"><b>Facilitator:</b> <?= $isFacilitator ? 'Yes' : 'No' ?></p>
                        <p class="account_permission"><b>Type: </b><?= $permissionlevel ?></p>
                        <p class="participant_contact"><b>Contact: </b></p>
                        <div class="d-flex justify-content-center">
                            <div class="display-stack">
                                <div class="display-top"><?= $email ?></div>
                                <div class="display-split"></div>
                                <div class="display-bottom">Email Address</div>
                            </div>
                            <?php if ($primaryphone) { ?>
                            <div class="display-stack">
                                <div class="display-top"><?= prettyPrintPhone($primaryphone)?></div>
                                <div class="display-split"></div>
                                <div class="display-bottom">Primary Phone</div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>

        <?php
        include('footer.php');
    }
} ?>