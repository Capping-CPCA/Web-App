<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Page that submits the attendance
 *
 *
 * Validates and submits all of the information for the class as well as adds new participants if indicated
 *
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version 1.1
 * @since 0.7
 */

global $db;
require ('attendance_utilities.php');
require ('shared_queries.php');

$success = true;
$errorMsg = "";
$duplicate = false;

//kill the page if we don't have any information
if(!isset($_SESSION['serializedInfo']) && !isset($_SESSION['attendance-info'])) {
    header("Location: /new-class");
    die(); //slow and painful
}

if (array_key_exists('duplicate', $_POST)) {
    $duplicate = true;
}

//get the class information
$attendanceInfo = $_SESSION['attendance-info'];

$selected_class = $attendanceInfo['classes'];
$selected_curr = $attendanceInfo['curr'];
$selected_date = $attendanceInfo['date-input'];
$selected_time = $attendanceInfo['time-input'];
$selected_site = $attendanceInfo['site'];
$selected_lang = $attendanceInfo['lang'];
$selected_facilitator = $attendanceInfo['facilitator'];
$selected_topic_id = $attendanceInfo['topic-id'];
$selected_curr_num = $attendanceInfo['curr-id'];

//PHP Input Validation - final check for class information input in the DB
{
    //run queries from new class page in case data changes
    $result_curriculum = $db->execute('shared_query_curriculum',[]);
    $result_classes = $db->execute('shared_query_classes',[]);
    $result_sites = $db->execute('shared_query_sites',[]);
    $result_languages = $db->execute('shared_query_languages',[]);
    $result_facilitators = $db->execute('shared_query_facilitators',[]);


    //check that all class info validations are true
    if(!(
            validateCurriculum($result_curriculum, $selected_curr_num) &&
            validateClass($result_classes, $selected_topic_id) &&
            validateSite($result_sites, $selected_site) &&
            validateLanguage($result_languages, $selected_lang) &&
            validateFacilitator($result_facilitators, $selected_facilitator)
    )){
        //if failed validation, die (either DB changed and invalidated class or malicious intent involved)
        die();
    }

}

$escaped_site_name = escape_apostrophe($selected_site);
$timestamp = makeTimestamp($selected_date, $selected_time);

//validate class offering - ensure there is no duplicate class offering
{
    if ($duplicate != true) {
        $duplicateClassOfferingQuery = "SELECT * FROM ClassOffering " .
            "WHERE date = '{$timestamp}' and siteName = '{$escaped_site_name}'; ";
        $resultsClassOfferingMatch = $db->no_param_query($duplicateClassOfferingQuery);
        //matched a class so we have a duplicate class
        if (pg_num_rows($resultsClassOfferingMatch) > 0) {
            include('header.php');
            echo "<div class=\"container\">";
            echo "<div class=\"card\">";
            echo "<div class=\"card-block p-2\" align=\"center\">";
            echo "<h4>Error: Duplicate Class Offering.</h4>";
            $errorTime = formatSQLDateShort($timestamp);
            echo "<p>There is already a class recorded at the site {$selected_site} at the time {$errorTime}. If this is a mistake, please edit the class information. Otherwise, click continue if you are adding more participants.</p>";
            echo "<div><a href=\"/edit-class-info\" style=\"display: inline-block; margin-right: 10px;\"><button type=\"button\" class=\"btn btn-outline-secondary\">Edit Class Information</button></a>";
            echo "<form action=\"/attendance-form-confirmed\" method=\"post\" style=\"display: inline-block;\"><input type=\"hidden\" name=\"duplicate\"><button type=\"submit\" class=\"btn cpca\">Continue</button></form></div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
            include('footer.php');
            die();
        }
    }
}

//get the attendance information from the session
$serializedInfo = $_SESSION['serializedInfo'];
$attendanceInfo = deserializeParticipantMatrix($serializedInfo);

//loop through and search for unregistered participants
for($i = 0; $i < count($attendanceInfo); $i++) {
    if($attendanceInfo[$i]['firstClass'] && $attendanceInfo[$i]['present']){ //first class ever and present
        //run function to insert them into the system

        //peopleInsert

        $fname = $attendanceInfo[$i]['fn'];
        $lname = $attendanceInfo[$i]['ln'];
        $minit = $attendanceInfo[$i]['mi'];

        //escape apostrophes and trim
        $fname = sanitizeString($db->conn, $fname);
        $lname = sanitizeString($db->conn, $lname);
        $minit = sanitizeString($db->conn, $minit);

        $peopleInsertQuery =
            "SELECT peopleinsert( " .
            "fname := '{$fname}'::text, " .
            "lname := '{$lname}'::text ";
            if(!empty($minit)){
                $peopleInsertQuery .= ", minit := '{$minit}'::varchar ";
            }
            $peopleInsertQuery .= ");";

        $resultInsert = $db->no_param_query($peopleInsertQuery);

        //update row information with those values
        $personId = pg_fetch_result($resultInsert, 'peopleinsert');
        $attendanceInfo[$i]['pid'] = $personId;

        //this converts the age that was converted in attendance form so it can be converted again in the proc
        $age = calculate_age($attendanceInfo[$i]['dob']);

        $createParticipantQuery =
            "SELECT createOutOfHouseParticipant( ".
            "outOfHouseParticipantId := {$personId}::int, " .
            "participantAge   := {$age}::int, " .
            "participantRace   := '{$attendanceInfo[$i]['race']}'::race, " .
            "participantSex    := '{$attendanceInfo[$i]['sex']}'::sex, " .
            "eID := 1::int " .
            "); ";

        $res = $db->no_param_query($createParticipantQuery);
        $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
        if ($res && $state != 0) {
            $success = false;
            $errorMsg .= "Could not create participant [$state]. ";
        }
    }
}

if ($duplicate != true) {
    // check if we edited the class details
    if (isset($_SESSION['old-info'])) {
        $oldInfo = $_SESSION['old-info'];
        unset($_SESSION['old-info']);
        $oldEscapedSiteName = escape_apostrophe($oldInfo['site']);
        $oldTimestamp = makeTimestamp($oldInfo['date-input'], $oldInfo['time-input']);
        $checkForOldClass = "SELECT * FROM ClassOffering " .
            "WHERE date = '{$oldTimestamp}' and siteName = '{$oldEscapedSiteName}'; ";
        $oldClassExists = $db->no_param_query($checkForOldClass);
    }

    // check if we are updating a class
    if (isset($oldClassExists) && pg_num_rows($oldClassExists) > 0) {
        $res = $db->query("SELECT classOfferingUpdate(
            date := $1::TIMESTAMP,
            siteName := $2::TEXT,
            newClassID := $3::INT,
            newCurriculumID := $4::INT,
            newDate := $5::TIMESTAMP,
            newSiteName := $6::TEXT,
            newLang := $7::TEXT)",
            [makeTimestamp($oldInfo['date-input'], $oldInfo['time-input']),
                $oldInfo['site'],
                $selected_topic_id,
                $selected_curr_num,
                $timestamp,
                $escaped_site_name,
                $selected_lang]);
    } else {
        //create one classOffering entry
        $classOfferingQuery =
            "INSERT INTO classoffering(ClassID, CurriculumID, date, siteName, lang) " .
            "VALUES ( " .
            "{$selected_topic_id}, " .
            "{$selected_curr_num}, " .
            "'{$timestamp}', " . //declared above
            "'{$escaped_site_name}', " .
            "'{$selected_lang}'" .
            ");";
        $res = $db->no_param_query($classOfferingQuery);
    }
    $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
    if ($res && $state != 0) {
        $success = false;
        $errorMsg .= "Could not create class offering [$state]. ";
    }

    if (!isset($oldClassExists) && !pg_num_rows($oldClassExists) > 0) {
        //create one facilitatorClassAttendance entry
        $facilitatorClassAttendanceQuery =
            "INSERT INTO facilitatorclassattendance( " .
            "date, " .
            "facilitatorID, " .
            "siteName " .
            ") " .

            "VALUES (" .
            "'{$timestamp}', " .
            "{$selected_facilitator}, " .
            "'{$escaped_site_name}' " .
            ");";

        $res = $db->no_param_query($facilitatorClassAttendanceQuery);
        $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
        if ($res && $state != 0) {
            $success = false;
            $errorMsg .= "Could not create facilitator class attendance [$state]. ";
        }
    }
}

//loop through participants and create many participantClassAttendance entries
for($i = 0; $i < count($attendanceInfo); $i++) {

    if($attendanceInfo[$i]['present']) {
        $tfString = ($attendanceInfo[$i]['isNew'] ? "true" : "false");

        //adjust query to put default fields in for numChildren and zip if empty
        $numChildrenValue = $attendanceInfo[$i]['numChildren'];
        if(is_null($numChildrenValue)){
            $numChildrenValue = "0";
        }
        $zipCodeValue = $attendanceInfo[$i]['zip'];
        if(is_null($zipCodeValue)){
            $zipCodeValue = "12601";
        }

        //sanitize string for DB input
        $escaped_comments = sanitizeString($db->conn, $attendanceInfo[$i]['comments']);

        // If class existed already, check if participant attendance existed
        if ($duplicate == true || (isset($oldClassExists) && pg_num_rows($oldClassExists) > 0)) {
            $id = $attendanceInfo[$i]['pid'];
            $checkForParticipantRecord = "SELECT * FROM participantclassattendance " .
                "WHERE date = '{$timestamp}' and siteName = '{$escaped_site_name}' and participantid = '{$id}'; ";
            $recordExists = $db->no_param_query($checkForParticipantRecord);
        }
        if (isset($recordExists) && pg_num_rows($recordExists) > 0) {
            $res = $db->query("UPDATE participantclassattendance SET comments = $1, numchildren = $2, isnew = $3, zipcode = $4 " .
                "WHERE participantid = $5 AND sitename = $6 AND date = $7", [$escaped_comments, $numChildrenValue, $tfString, $zipCodeValue, $attendanceInfo[$i]['pid'], $escaped_site_name, $timestamp]);
        } else {
            $insertClassAttendanceQuery =
                "INSERT INTO participantclassattendance( " .
                "date, " .
                "participantID, " .
                "siteName, " .
                "comments, " .
                "numChildren, " .
                "isNew, " .
                "zipCode " .
                ") " .
                "VALUES(" .
                "'{$timestamp}', " .
                "{$attendanceInfo[$i]['pid']}, " .
                "'{$escaped_site_name}', " .
                "'{$escaped_comments}', " .
                " {$numChildrenValue}, " .
                "'{$tfString}',  " .
                "{$zipCodeValue} " .
                "); ";
            $res = $db->no_param_query($insertClassAttendanceQuery);
        }
        $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
        if ($res && $state != 0) {
            $success = false;
            $errorMsg .= "Could not create participant class attendance [$state]. ";
        }
    }

}

include('header.php');

?>
    <div class="container">

        <div class="card">
            <div class="card-block p-2">
                <div class="modal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Warning: Duplicate Class Offering</h5>
                            </div>
                            <div class="modal-body">
                                <p>There is already a class recorded at the site {$selected_site} at the time {$errorTime}. If this is a mistake, please edit the class information. Otherwise, if you are trying to add more participants, please click continue.</p>
                                <a href='/edit-class-info'><button type="button" class="btn btn-outline-secondary">Edit Class Information</button></a>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-cpca" data-dismiss="modal">Continue</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if($success){
                    echo "<h4 class=\"card-title\" style=\"text-align: center;\"><i class=\"fa fa-thumbs-up\" aria-hidden=\"true\" style=\"color:green;\"></i> Success!</h4>";
                    echo "<h6 class=\"card-subtitle mb-2 text-muted\" style=\"text-align: center;\">Attendance has been submitted!</h6>";

                    echo "<div class=\"d-flex justify-content-center\">";
                    echo "<button type=\"button\" class=\"btn cpca\" onclick=\"location.href = '/attendance'\" style='margin-top: 10px;'>Back To Dashboard</button>";
                    echo "</div>";
                } else{
                    echo "<h4 class=\"card-title\" style=\"text-align: center;\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\" style='color: red;'></i> Error</h4>";
                    echo "<h6 class=\"card-subtitle mb-2 text-muted\" style=\"text-align: center;\">There was an error inputting the form. Please " .
                        " try again or contact your system administrator</h6>";
                    echo $errorMsg;
                }

                //unset previous class session information
                unset($_SESSION['serializedInfo']);
                unset($_SESSION['attendance-info']);
                ?>


            </div>
        </div>
    </div>
<?php
include('footer.php');
?>