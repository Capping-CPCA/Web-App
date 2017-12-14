<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays all forms belonging to a specific participant.
 * Links to view, edit, or delete those forms are available.
 *
 * @author Michelle Crawley
 * @copyright 2017 Marist College
 * @version 0.7.1
 * @since 0.1
 */

global $db, $params, $route;
$peopleid = $params[0];

# Get participant first name, last name, middle
$result = $db->query("SELECT firstname, lastname, middleinit FROM people WHERE peopleid = $1", [$peopleid]);
$name = pg_fetch_assoc($result);
extract($name);

# Displays the participant's name in the title according to whether they have a middle name
$route['title'] = "Forms Completed for " . ucwords($firstname . " " . ($middleinit ?: "") . " " . $lastname);

/* -- Queries for Form Information --
 *
 * Uses pg_fetch_all() because there can be multiple forms
 *
 */
# Intake information
$result = $db->query("SELECT employeesigneddate, intakeinformationid FROM intakepacketinfo WHERE participantid = $1", [$peopleid]);
$intake = pg_fetch_all($result);

# Agency referral information
$result = $db->query("SELECT employeesigneddate, selfreferralid FROM selfreferralinfo WHERE participantid = $1", [$peopleid]);
$selfReferral = pg_fetch_all($result);

# Self referral information
$result = $db->query("SELECT employeesigneddate, agencyreferralid FROM agencyreferralinfo WHERE participantid = $1", [$peopleid]);
$agencyReferral = pg_fetch_all($result);

include('header.php');
?>
    <div style="width: 100%">
        <button class="cpca btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
        <?php
        // Displays result of edit or delete
        if (isset($_SESSION['notification'])) {
            $note = $_SESSION['notification'];
            $notification = new Notification($note['title'], $note['msg'], $note['type']);
            $notification->display();
            unset($_SESSION['notification']);
        }
        ?>
        <div class="d-flex flex-row justify-content-center flex-wrap">
            <?php if ($intake) {
                foreach ($intake as $i) { ?>
                    <div class="card text-center result-card">
                        <div class="card-body">
                            <h4 class="card-title">Intake</h4>
                            <h6 class="card-subtitle text-muted"> <?= $i['employeesigneddate'] ?></h6>
                        </div>
                        <div class="card-footer d-flex flex-row justify-content-center">
                            <a href="/intake-packet/view/<?= $params[0] . "/" . $i['intakeinformationid']?>">
                                <button class="btn outline-cpca btn-sm" style="margin-right: 5px">View</button>
                            </a>
                            <a href="/intake-packet/edit/<?= $params[0] . "/" . $i['intakeinformationid']?>">
                                <button class="btn btn-outline-secondary btn-sm">Edit</button>
                            </a>
                        </div>
                    </div>
                <?php } } if ($selfReferral) {
                foreach ($selfReferral as $sr) { ?>
                    <div class="card text-center result-card">
                        <div class="card-body">
                            <h4 class="card-title">Initial Contact</h4>
                            <h6 class="card-subtitle text-muted"> <?= $sr['employeesigneddate'] ?></h6>
                        </div>
                        <div class="card-footer d-flex flex-row justify-content-center">
                            <a href="/self-referral-form/view/<?= $params[0] . "/" . $sr['selfreferralid']?>">
                                <button class="btn outline-cpca btn-sm" style="margin-right: 5px">View</button>
                            </a>
                            <a href="/self-referral-form/edit/<?= $params[0] . "/" . $sr['selfreferralid']?>">
                                <button class="btn btn-outline-secondary btn-sm">Edit</button>
                            </a>
                        </div>
                    </div>
                <?php } } if ($agencyReferral) {
                foreach ($agencyReferral as $r) {
                    # Get the contact agency for more details about the referral
                    $result = $db->query("SELECT contactagencymembers.agency " .
                                                "FROM contactagencymembers, contactagencyassociatedwithreferred " .
                                                "WHERE contactagencymembers.contactagencyid = contactagencyassociatedwithreferred.contactagencyid " .
                                                "AND contactagencyassociatedwithreferred.agencyreferralid = $1",
                                                [$r['agencyreferralid']]);
                    $agency = pg_fetch_assoc($result);
                    if ($agency)
                        extract($agency);
                    $referralInfo = $r['employeesigneddate'] . ($agency ? " - $agency" : "");
                    ?>
                    <div class="card text-center result-card">
                        <div class="card-body">
                            <h4 class="card-title">Referral</h4>
                            <h6 class="card-subtitle text-muted" style="margin-bottom: 10px;"> <?= $r['employeesigneddate'] ?></h6>
                            <h6 class="card-subtitle text-muted"> <?= $agency ? "Agency: $agency" : "" ?></h6>
                        </div>
                        <div class="card-footer d-flex flex-row justify-content-center">
                            <a href="/referral-form/view/<?= $params[0] . "/" . $r['agencyreferralid']?>">
                                <button class="btn outline-cpca btn-sm" style="margin-right: 5px">View</button>
                            </a>
                            <a href="/referral-form/edit/<?= $params[0] . "/" . $r['agencyreferralid']?>">
                                <button class="btn btn-outline-secondary btn-sm">Edit</button>
                            </a>
                        </div>
                    </div>
                <?php }
            }
            # If the user has no forms display a message
            if ($intake == false && $selfReferral == false && $agencyReferral == false) {
                ?>
                <div class="w-100 d-flex flex-column justify-content-center text-center">
                    <h3 class="display-3 text-secondary" style="font-size: 40px;"><i
                            class="fa fa-exclamation-circle"></i></h3>
                    <h3 class="display-3 text-secondary" style="font-size: 40px;">No Completed Forms.</h3>
                </div>
            <?php } ?>
        </div>
    </div>
<?php
include('footer.php');
?>