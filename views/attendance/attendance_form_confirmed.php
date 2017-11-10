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


authorizedPage();

global $db;
require ('attendance_utilities.php');
include('header.php');

$success = false;
//kill the page if we don't have any information
if(!isset($_SESSION['serializedInfo'])) {
    die(); //slow and painful
}

$selected_class = $_POST['classes'];
$selected_curr = $_POST['curr'];
$selected_date = $_POST['date-input'];
$selected_time = $_POST['time-input'];
$selected_site = $_POST['site'];
$selected_lang = $_POST['lang'];
$selected_facilitator = $_POST['facilitator'];

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

//get the corresponding curriculumId from the curriculum name
$curr_result = $db->no_param_query("select cur.curriculumid id from curricula cur where cur.curriculumname = '"
    . escape_apostrophe($selected_curr) . "';");
$selected_curr_num = pg_fetch_result($curr_result, 'id');

$serializedInfo = $_SESSION['serializedInfo'];
$attendanceInfo = deserializeParticipantMatrix($serializedInfo);

//loop through and search for unregistered participants
for($i = 0; $i < count($attendanceInfo); $i++) {
    if($attendanceInfo[$i]['firstClass'] && $attendanceInfo[$i]['present']){ //first class ever and present
        //run function to insert them into the system

        //peopleInsert
        //TODO: for next release implement Vallie's custom participant search
        $peopleInsertQuery = "SELECT peopleinsert( " .
          "fname := '{$attendanceInfo[$i]['fn']}'::text, " .
          "lname := '{$attendanceInfo[$i]['ln']}'::text, " .
          "minit := '{$attendanceInfo[$i]['mi']}'::varchar " .
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

        $db->no_param_query($createParticipantQuery);
    }
}
//loop through attendanceInfo and insert records into the database

//create one classOffering entry
$db -> no_param_query(
        "SELECT createClassOffering( " .
        "offeringTopicName := '{$selected_class}'::text, " .
        "offeringTopicDescription := NULL::text, " .
        "offeringTopicDate := '{$timestamp}'::timestamp, " .
        "offeringSiteName := '{$selected_site}'::text, " .
        "offeringLanguage := '{$selected_lang}'::text, " .
        "offeringCurriculumId := {$selected_curr_num}::int) "
);

//create one facilitatorClassAttendance entry
$db -> no_param_query(
    "INSERT INTO facilitatorclassattendance( " .
    "topicName, date, siteName, facilitatorId ) " .
    "VALUES ('{$selected_class}', '{$timestamp}', '{$selected_site}', {$selected_facilitator});"
);


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
            "topicname, " .
            "date, ".
            "participantId, " .
            "comments, ".
            "numChildren, ".
            "isNew, " .
            "zipCode, " .
            "siteName ".
            ") " .
            "VALUES(" .
            "'{$selected_class}', ".
            "'{$timestamp}', ".
            "{$attendanceInfo[$i]['pid']}, " .
            "'{$attendanceInfo[$i]['comments']}', " .
            " {$numChildrenValue}, " .
            "'{$tfString}',  ".
            "{$zipCodeValue}, " .
            "'{$selected_site}'" .
            "); ";

        $db->no_param_query($insertClassAttendanceQuery);
    }

}

$success = true;

?>

    <div class="container">

        <div class="card">
            <div class="card-block p-2">
                <?php
                if($success){
                    echo "<h4 class=\"card-title\" style=\"text-align: center;\"><i class=\"fa fa-thumbs-up\" aria-hidden=\"true\" style=\"color:green;\"></i> Success!</h4>";
                    echo "<h6 class=\"card-subtitle mb-2 text-muted\" style=\"text-align: center;\">Attendance Submitted Successfully. Good job!</h6>";
                    echo "<button type=\"button\" class=\"btn btn-primary\" onclick=\"location.href = '/record-attendance'\" style='margin-top: 10px;'>Back To Dashboard</button>";
                } else{
                    echo "<h4 class=\"card-title\" style=\"text-align: center;\"><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\" style='color: red;'></i> Error</h4>";
                    echo "<h6 class=\"card-subtitle mb-2 text-muted\" style=\"text-align: center;\">There was an error inputting the form. Please " .
                        " try again or contact your system administrator</h6>";
                }

                //unset previous class session information
                if(isset($_SESSION['serializedInfo'])) {
                    unset($_SESSION['serializedInfo']);
                }
                ?>


            </div>
        </div>
    </div>
<?php
include('footer.php');
?>