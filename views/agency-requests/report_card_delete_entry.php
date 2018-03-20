<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Prompts the user to delete an existing
 * attendance entry.
 *
 * This page displays a prompt to either remove the selected
 * attendance record or to cancel and go back. If the user removes the
 * record then it is removed from the database. This function is for
 * admins only.
 *
 * @author Michelle Crawley
 * @copyright 2017 Marist College
 * @version 0.3.3
 * @since 0.1
 */
global $db, $params;

// Get entry information from previous page
if (array_key_exists('entry-info', $_POST)) {
    $entryInfo = json_decode($_POST['entry-info'], true);
    $_SESSION['entry-info'] = $entryInfo;
// Otherwise get entry information from session variable
} else {
    $entryInfo = $_SESSION['entry-info'];
    unset($_SESSION['entry-info']);
}
$topicName = $entryInfo[1];
$curriculumName = $entryInfo[2];
extract($entryInfo[0]);

// Get people id from params
$peopleid = $params[1];
$result = $db->query("SELECT * FROM people WHERE peopleid = $1", [$peopleid]);
$person = pg_fetch_assoc($result);
extract($person);

// Archive data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $deleteRes = $db->query("DELETE FROM participantclassattendance " .
	                                "WHERE participantid = $1 AND sitename = $2 AND date = $3", [$peopleid, $sitename, $date]);

    // If the delete is successful display a notification on the report-card page
    $note['title'] = ($deleteRes ? 'Success!' : 'Error!');
    $note['msg'] = ($deleteRes ? 'The entry has been deleted.' : 'The entry wasn\'t deleted.');
    $note['type'] = ($deleteRes ? 'success' : 'danger');
    $_SESSION['notification'] = $note;
    header('Location: /report-card/' . $peopleid);
    die();
}
include('header.php');
?>

<div class="page-wrapper">
    <form class="card warning-card" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>">
        <h4 class="card-header card-title">
            Attendance Entry for <?= ucwords($firstname . " " . ($middleinit ?: "") . " " . $lastname); ?>
        </h4>
        <div class="card-body">
            You are about to delete an attendance entry for "<?= ucwords($firstname . " " . ($middleinit ?: "") . " " . $lastname); ?>" with the following details: <br>
            <div class="text-center" style="padding:10px;"><b>Class:</b> <?=$topicName?> <br>
            <b>Curriculum:</b> <?=$curriculumName?> <br>
            <b>Site:</b> <?=$sitename?> <br>
            <b>Date:</b> <?=formatSQLDate($date)?> <br>
            </div>
            Are you sure you want to delete this entry?
        </div>
        <div class="card-footer text-right">
            <button type="button" class="btn btn-light" onclick="goBack()">Cancel</button>
            <button type="submit" name="delete" class="btn btn-danger">Delete</button>
        </div>
    </form>
</div>

<?php
include ('footer.php'); ?>