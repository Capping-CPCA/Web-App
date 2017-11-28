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
 * @version [version number]
 * @since [initial version number]
 */

global $db;
require ('attendance_utilities.php');

$success = true;
$errorMsg = "";

//kill the page if we don't have any information
if(!isset($_SESSION['serializedInfo']) && !isset($_SESSION['attendance-info'])) {
    header("Location: /new-class");
    die(); //slow and painful
}

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

{
    /*
    //TODO: PHP input validation
    $result_curriculum = $db->no_param_query("SELECT c.curriculumname FROM curricula c ORDER BY c.curriculumname ASC;");

    $result_classes = $db->no_param_query("SELECT cc.topicname from curriculumclasses cc ORDER BY cc.curriculumid;");

    $result_sites = $db->no_param_query("select s.sitename from sites s;");

    $result_languages = $db->no_param_query("select lang from languages;");

    $result_facilitators = $db->no_param_query("select peop.firstname, peop.middleinit, peop.lastname, peop.peopleid " .
        "from people peop, employees emp, facilitators f " .
        "where peop.peopleid = emp.employeeid " .
        "and emp.employeeid = f.facilitatorid " .
        "order by peop.lastname asc;"
    );

    */

}



$timestamp = makeTimestamp($selected_date, $selected_time);

$serializedInfo = $_SESSION['serializedInfo'];
$attendanceInfo = deserializeParticipantMatrix($serializedInfo);

//loop through and search for unregistered participants
for($i = 0; $i < count($attendanceInfo); $i++) {
    if($attendanceInfo[$i]['firstClass'] && $attendanceInfo[$i]['present']){ //first class ever and present
        //run function to insert them into the system

        //peopleInsert
        //TODO: for next release implement Vallie's custom participant search
        $peopleInsertQuery = "SELECT peopleinsert( " .
            "fname := \"{$attendanceInfo[$i]['fn']}\"::text, " .
            "lname := \"{$attendanceInfo[$i]['ln']}\"::text, " .
            "minit := \"{$attendanceInfo[$i]['mi']}\"::varchar " .
            ");";

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
            //TODO: change default based on requirements
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
//loop through attendanceInfo and insert records into the database

//create one classOffering entry
$classOfferingQuery =
    "INSERT INTO classoffering(classid, curriculumid, date, sitename, lang) " .
    "VALUES ( " .
    "{$selected_topic_id}, " .
    "{$selected_curr_num}, " .
    "'{$timestamp}', " . //declared above
    "'{$selected_site}', " .
    "'{$selected_lang}'" .
    ");";

$res = $db -> no_param_query($classOfferingQuery);
$state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
if ($res && $state != 0) {
    $success = false;
    $errorMsg .= "Could not create class offering [$state]. ";
}


//create one facilitatorClassAttendance entry
$facilitatorClassAttendanceQuery =
    "INSERT INTO facilitatorclassattendance( " .
    "classid, ".
    "curriculumid, ".
    "date, " .
    "facilitatorId, " .
    "siteName " .
    ") " .

    "VALUES (" .
    "{$selected_topic_id}, " .
    "{$selected_curr_num}, " .
    "'{$timestamp}', " .
    "{$selected_facilitator}, " .
    "'{$selected_site}' " .
    ");";

$res = $db -> no_param_query($facilitatorClassAttendanceQuery);
$state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
if ($res && $state != 0) {
    $success = false;
    $errorMsg .= "Could not create facilitator class attendance [$state]. ";
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

        $insertClassAttendanceQuery =
            "INSERT INTO participantclassattendance( " .
            "classid, " .
            "curriculumid, " .
            "date, ".
            "participantId, " .
            "siteName, ".
            "comments, ".
            "numChildren, ".
            "isNew, " .
            "zipCode " .
            ") " .
            "VALUES(" .
            "{$selected_topic_id}, ".
            "{$selected_curr_num}, ".
            "'{$timestamp}', ".
            "{$attendanceInfo[$i]['pid']}, " .
            "'{$selected_site}', " .
            "'{$attendanceInfo[$i]['comments']}', " .
            " {$numChildrenValue}, " .
            "'{$tfString}',  ".
            "{$zipCodeValue} " .
            "); ";

        $res = $db->no_param_query($insertClassAttendanceQuery);
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
                if(isset($_SESSION['serializedInfo'])) {
                    unset($_SESSION['serializedInfo']);
                }

                if(isset($_SESSION['attendance-info'])) {
                    unset($_SESSION['attendance-info']);
                }
                ?>


            </div>
        </div>
    </div>
<?php
include('footer.php');
?>