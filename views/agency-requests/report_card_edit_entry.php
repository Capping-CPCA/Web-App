<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays a page to edit an already recorded attendance
 * entry.
 *
 * This page allows a user to update number of children,
 * comments, zipcode, and if a participant is new on
 * an already recorded attendance entry.
 *
 * @author Michelle Crawley
 * @copyright 2017 Marist College
 * @version 0.7.1
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

$success = true;

// If the submit button is hit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && array_key_exists('submit', $_POST)) {
    $numchildren = isset($_POST['numchildren']) ? $_POST['numchildren'] : "";
    $zipcode = isset($_POST['zipcode']) ? $_POST['zipcode'] : "";
    $comments = isset($_POST['comments']) ? $_POST['comments'] : "";
    $isnew = isset($_POST['isnew']) ? $_POST['isnew'] : $isnew;

    // Update the attendance entry with the new data
    $result = $db->query("UPDATE participantclassattendance SET comments = $1, numchildren = $2, isnew = $3, zipcode = $4 " .
                                "WHERE participantid = $5 AND sitename = $6 AND date = $7", [$comments, $numchildren, $isnew, $zipcode, $peopleid, $sitename, $date]);

    // Set notification message according to success status
    if ($result) {
        $state = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);
        if ($state != 0) {
            $success = false;
            $errorMsg = "The attendance record could not be updated.";
        }
    } else {
        $success = false;
        $errorMsg = "The attendance record could not be updated.";
    }

    // If POST is successful, display a notification on report-card/id page
    if ($success) {
        $note['title'] = 'Success!';
        $note['msg'] = 'The record has been added.';
        $note['type'] = 'success';
        $_SESSION['notification'] = $note;
        header("Location: /report-card/" . $peopleid);
        die();
    }
}

include('header.php');
?>

    <div class="w-100" style="height: fit-content;">
        <?php
        // Displays a notification if modification was not successful
        if ($errorMsg) {
            $notification = new Notification("Error", $errorMsg, "danger");
            $notification->display();
        }
        ?>
        <div class="flex-column">
            <div class="jumbotron form-wrapper mb-3">
                <div id = "alert-box"></div>
                <h6 class="display-4 text-center" style="font-size: 34px">Entry for <?= ucwords($firstname . " " . ($middleinit ?: "") . " " . $lastname); ?></h6>
                <form id="new-report-card-entry" method="post">
                    <div style="margin:15px; font-size: 13pt;">
                        <b>Class:</b> <?=$topicName?> <br>
                        <b>Curriculum:</b> <?=$curriculumName?> <br>
                        <b>Site:</b> <?=$sitename?> <br>
                        <b>Date:</b> <?=formatSQLDate($date)?> <br>
                        <hr>
                        <!-- Number of children under 18 -->
                        <div class="form-group">
                            <label for="num-children-input" class="col-form-label" style="text-align: left;"><b>Number of children under 18</b></label>
                            <input class="form-control" type="number" value="<?=isset($numchildren) ? $numchildren : ""?>" id="numchildren" name="numchildren" placeholder="Please enter number of children...">
                        </div>
                        <!-- Zip code -->
                        <div class="form-group">
                            <label for="zip-input" class="col-form-label" style="text-align: left;"><b>Zip code</b></label>
                            <input class="form-control" type="text" value="<?=isset($zipcode) ? $zipcode : ""?>" id="zipcode" name="zipcode" placeholder="Enter zip code...">
                        </div>
                        <div class="form-group">
                            <label for="comments-input" class="col-form-label" style="text-align: left;"><b>Comments</b></label>
                            <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Enter comments..."><?=isset($comments) ? $comments : ""?></textarea>
                        </div>
                        <div class="form-check mb-2 mr-sm-2 mb-sm-0">
                            <label for="isFacilitator"> Is this a new participant? </label>
                            <div class="btn-group" data-toggle="buttons" style="margin-left: 10px;">
                                <label class="btn btn-secondary" id="is-new">
                                    <input type="radio" name="isnew" value="1" autocomplete="off"> Yes
                                </label>
                                <label class="btn btn-secondary" id="is-not-new">
                                    <input type="radio" name="isnew" value="0" autocomplete="off"> No
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- edit button information -->
                    <div class="form-footer submit">
                        <a href="/report-card/<?=$peopleid?>">
                            <button type="button" class="btn btn-outline-secondary" style="margin-right: 7px;">Cancel</button>
                        </a>
                        <button type="submit" name="submit" class="btn cpca">Update Attendance Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            // If the participant is not new, click no on the "is new?" button
            <?php if ($isnew == 'f') { ?>
            document.getElementById("is-new").click();
            <?php } else { ?>
            document.getElementById("is-not-new").click();
            <?php } ?>

        });
    </script>
<?php
include ('footer.php');
?>