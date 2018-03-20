<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays a page to enter a new attendance entry.
 *
 * This page allows a user to record details about an
 * individual participant's attendance for a specific
 * class offering. These details include number of children,
 * zipcode, comments, and if the participant is new. If a
 * class offering exists already, the participant will be
 * added to that attendance. Otherwise, a new class offering
 * will be generated.
 *
 * @author Michelle Crawley
 * @copyright 2017 Marist College
 * @version 0.7.1
 * @since 0.1
 */
global $db, $params;
include ('../models/Notification.php');
include($_SERVER['DOCUMENT_ROOT'].'/../views/attendance/attendance_utilities.php');

// Get class offering info
if (!array_key_exists('submit', $_POST))
    $_SESSION['attendance-info'] = $_POST;

$attendanceInfo = $_SESSION['attendance-info'];
extract($attendanceInfo);

// Get people id from params
$peopleid = rawurldecode(implode('/', $params));
$result = $db->query("SELECT * FROM people WHERE peopleid = $1", [$peopleid]);
$person = pg_fetch_assoc($result);
extract($person);

$result = $db->query("SELECT * FROM participants WHERE participantid = $1", [$peopleid]);
$participant = pg_fetch_assoc($result);
extract($participant);

// Get information from past attendance records
$resultClassPastAttendance = $db->no_param_query(
    "select * from classattendancedetails " .
    "where participantid = {$peopleid} " .
    "order by date desc; " // Order by date desc because we want the most recent info
);

if (pg_num_rows($resultClassPastAttendance)) {
    $row = pg_fetch_assoc($resultClassPastAttendance); // Get the most recent row
    extract($row);
}

$success = true;

// If the submit button is hit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && array_key_exists('submit', $_POST)) {
    $numchildren = isset($_POST['numchildren']) ? $_POST['numchildren'] : "";
    $zipcode = isset($_POST['zipcode']) ? $_POST['zipcode'] : "";
    $comments = isset($_POST['comments']) ? $_POST['comments'] : "";
    $isnew = isset($_POST['isnew']) ? $_POST['isnew'] : $isnew;

    // Get topicid, curriculumid, and make timestamp from class offering date
    $topicid = $attendanceInfo['topic-id'];
    $timestamp = makeTimestamp($attendanceInfo['date-input'], $attendanceInfo['time-input']);
    $currid = $attendanceInfo['curr-id'];

    // Uses the attendanceInsert stored procedure
    $result = $db->query("SELECT attendanceInsert(
        attendanceparticipantid := $1::INT,
        attendantage := $2::INT,
        attendanceparticipantrace := $3::RACE,
        attendanceparticipantsex := $4::SEX,
        attendancesite := $5::TEXT,
        attendancefacilitatorid := $6::INT,
        attendanceclassid := $7::INT,
        attendancedate := $8::TIMESTAMP,
        attendancecurriculumid := $9::INT,
        attendancecomments := $10::TEXT,
        attendancenumchildren := $11::INT,
        isattendancenew := $12::BOOLEAN,
        attendanceparticipantzipcode := $13::VARCHAR(5),
        classofferinglang := $14::TEXT)",
        [$peopleid,
            calculate_age($dateofbirth),
            $race,
            $sex,
            $site,
            $facilitator,
            $topicid,
            $timestamp,
            $currid,
            $comments,
            $numchildren,
            $isnew,
            $zipcode,
            $lang]);

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
    if (isset($errorMsg)) {
        $notification = new Notification("Error", $errorMsg, "danger");
        $notification->display();
    }
    ?>
    <div class="flex-column">
        <div class="jumbotron form-wrapper mb-3">
            <div id = "alert-box"></div>
            <h2 class="display-4 text-center" style="font-size: 34px"><?= ucwords($firstname . " " . ($middleinit ?: "") . " " . $lastname); ?></h2>
            <form id="new-report-card-entry" method="post">
                <div style="margin:15px; font-size: 13pt;">
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
                        <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Enter comments..."></textarea>
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
                    <button type="submit" name="submit" class="btn cpca">Create Attendance Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(function () {
        // If the participant is not new, click no on the "is new?" button
        <?php if (isset($isnew)) { ?>
            document.getElementById("is-not-new").click();
        <?php } else { ?>
            document.getElementById("is-new").click();
        <?php } ?>

    });
</script>
<?php
include ('footer.php');
?>