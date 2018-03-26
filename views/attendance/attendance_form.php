<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Where the attendance is input.
 *
 * Shows possible participants in the class automatically and provides attendance options for them.
 * Also contains a participant lookup tool for people already in the system.
 * Contains a participant entry tool for those not in the system yet.
 *
 * @author Scott Hansen
 * @author Vallie Joseph - participant search tool
 * @copyright 2017 Marist College
 * @version 1.1
 * @since 0.7
 */

global $db;

include ('../models/Notification.php');

require ("attendance_utilities.php");
require ("shared_queries.php");

//if attendance has already started being recorded, grab that information from the session information
$pageInformation = isset($_SESSION['serializedInfo']) ? deserializeParticipantMatrix($_SESSION['serializedInfo']) : array();

//participant's number to edit from edit participant page
if(isset($_SESSION['edit-participant-details-num'])) {
    unset($_SESSION['edit-participant-details-num']);
}

/* Begin set class details */
// Attendance has already started being recorded and we are not coming from edit class information page
if (isset($_SESSION['attendance-info'])) {
    $attendanceInfo = $_SESSION['attendance-info'];
    $selected_class = $attendanceInfo['classes'];
    $selected_curr = $attendanceInfo['curr'];
    $selected_date = $attendanceInfo['date-input'];
    $selected_time = $attendanceInfo['time-input'];
    $selected_site = $attendanceInfo['site'];
    $selected_lang = $attendanceInfo['lang'];
    $selected_facilitator = $attendanceInfo['facilitator'];
    $selected_topic_id = $attendanceInfo['topic-id'];
    $selected_curr_id = $attendanceInfo['curr-id'];
}
// Coming from new class page or edit class page - get attendance info from POST
else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_class = $_POST['classes'];
    $selected_curr = $_POST['curr'];
    $selected_date = $_POST['date-input'];
    $selected_time = $_POST['time-input'];
    $selected_site = $_POST['site'];
    $selected_lang = $_POST['lang'];
    $selected_facilitator = $_POST['facilitator'];
    $selected_topic_id = $_POST['topic-id'];
    $selected_curr_id = $_POST['curr-id'];

    $_SESSION['attendance-info'] = $_POST;
}
// No attendance info, go back to new class
else {
    header("Location: /new-class");
    die();
}
/* End set class details */

//boolean used to ensure we do have a duplicate person on our form
//   (i.e. someone refreshes the page after submitting a new person with no intake packet)
$duplicatePerson = false;

/* Begin Set Page Information */
//if we have previous information passed to us from lookup form or add person form,
//  then display this information instead of db information
if(isset($_SESSION['serializedInfo'])) {
    //from edit participant page
    if(isset($_POST['fromConfirmEditParticipant'])){
        $selected_edit_num = $_POST['editButton'];
        $changed_num_children = $_POST['num-children-input-change'];
        $changed_zip = $_POST['zip-input-change'];

        //edit person's number of children and zip code
        $pageInformation[$selected_edit_num]['numChildren'] = $changed_num_children;
        $pageInformation[$selected_edit_num]['zip'] = $changed_zip;

        //update person in session variable for page information
        $_SESSION['serializedInfo'] = serializeParticipantMatrix($pageInformation);
    }
    //from participant search tool (on this page)
    else if(isset($_POST['lookupId'])){
        $lookupId = $_POST['pidLookup'];

        //lookup most recent info from DB --either from class info or from intake packet
        $resultClassPastAttendance = $db->no_param_query(
            "select * from classattendancedetails " .
            "where participantid = {$lookupId} " .
            "order by date desc; " //date desc because we want the most recent info
        );

        $fn = $mi = $ln = $dob = $race = $zip = $nc = $isNew = $sex = null;
        //found past attendance history
        if(pg_num_rows($resultClassPastAttendance)){
            $row = pg_fetch_assoc($resultClassPastAttendance); //most recent row
            $fn = $row['firstname'];
            $mi = $row['middleinit'];
            $ln = $row['lastname'];
            $dob = $row['dateofbirth'];
            $race = $row['race'];
            $zip = $row['zipcode'];
            $nc = $row['numchildren'];
            $isNew = false;
            $sex = $row['sex'];
        } else{ //we didn't find that information find name and other info
            //grab info from intake packet
            $resultPersonLookup = $db->query(
                "SELECT pe.firstname, pe.middleinit, pe.lastname, pa.dateofbirth, pa.race, pa.sex " .
                "FROM people pe, participants pa " .
                "WHERE pe.peopleid = pa.participantid " .
                "AND pe.peopleid = $1;", [$lookupId]);
            $row = pg_fetch_assoc($resultPersonLookup);
            $fn = $row['firstname'];
            $mi = $row['middleinit'];
            $ln = $row['lastname'];
            $dob = $row['dateofbirth'];
            $race = $row['race'];
            $isNew = true;
            $sex = $row['sex'];
        }

        //look for duplicates on page
        for($k = 0; $k < count($pageInformation); $k++){
            if($pageInformation[$k]['pid'] == $lookupId){
                //found a duplicate person
                $duplicatePerson = true;
                $successAddingPerson = false;
            }
        }

        //add person to pageInformation
        if(!$duplicatePerson){
            $pageInformation[] = array(
                "pid"           => $lookupId,
                "fn"            => $fn,
                "mi"            => $mi,
                "ln"            => $ln,
                "dob"           => $dob,
                "zip"           => $zip,
                "numChildren"   => $nc,
                "race"          => $race,
                "comments"      => null,
                "present"       => true,
                "isNew"         => $isNew, //isNew field from DB
                //people who haven't completed the intake forms and just filled out info in the "no intake form" section
                "firstClass"    => false,
                "sex"           => $sex
            );
        }

        //update our posted information
        updateSessionClassInformation();

    }
    //we're coming from the current page and posting new participant info
    else if(isset($_POST['fromAddPerson']) && $_POST['fromAddPerson'] == 1){ //value is changed when click to add person
        $firstTimeSubmitting = true;

        $fn = $_POST['new-person-first'];
        $mi = $_POST['new-person-middle'];
        $ln = $_POST['new-person-last'];
        $race = $_POST['race-select'];
        $ageInput = $_POST['age-input'];
        $numC = $_POST['num-children-input'];
        $zipInput = $_POST['zip-input'];
        $sexInput = $_POST['sex-select'];

        //ensure there are no duplicates
        $countP = count($pageInformation);
        for($j = 0; $j < $countP; $j++){

            //if all of these match, we don't have a new person
            if(
                $pageInformation[$j]['fn'] == $fn &&
                $pageInformation[$j]['mi'] == $mi &&
                $pageInformation[$j]['ln'] == $ln &&
                $pageInformation[$j]['race'] == $race &&
                $pageInformation[$j]['dob'] == date_subtraction((string) $ageInput . " years") &&
                $pageInformation[$j]['numChildren'] == $numC &&
                $pageInformation[$j]['zip'] == $zipInput &&
                $pageInformation[$j]['sex'] == $sexInput
            )
            {
                $firstTimeSubmitting = false;
                $duplicatePerson = true;
            }
        }

        if($firstTimeSubmitting){

            //update our posted information
            updateSessionClassInformation();
            $pageInformation = deserializeParticipantMatrix($_SESSION['serializedInfo']);

            //validate input
            if(
                validateName($fn) &&
                validateMiddle($mi) &&
                validateName($ln) &&
                validateRace($race) &&
                validateSex($sexInput) &&
                validateAge($ageInput) &&
                validateNumChildren($numC) &&
                validateZip($zipInput)
            )
            {
                //add to participantMatrix
                $pageInformation[] = array(
                    "pid"           => 0, //will be changed later from function
                    "fn"            => $fn,
                    "mi"            => $mi,
                    "ln"            => $ln,
                    "dob"           => date_subtraction((string) $ageInput . " years"),
                    "zip"           => $zipInput,
                    "numChildren"   => $numC,
                    "race"          => $race,
                    "comments"      => null,
                    "present"       => true,
                    "isNew"         => true, //isNew field from DB
                    //people who haven't completed the intake forms and just filled out info in the "no intake form" section
                    "firstClass"    => true,
                    "sex"           => $sexInput
                );

                $successAddingPerson = true;
            } else {
                $successAddingPerson = false;
            }
        }
    }
}
//no session information set, populate class attendance recommendations
//else grab information from the db and format it into the associative array format
else {
    if (isset($_SESSION['old-info'])) {
        $oldInfo = $_SESSION['old-info'];

        // Make timestamp into correct format
        $timestamp = makeTimestamp($oldInfo['date-input'], $oldInfo['time-input']);
        $classDate = new DateTime($timestamp);
        $dateTime = $classDate->format('Y-m-d H:i:s');

        $fullQuery = "SELECT * FROM classattendancedetails " .
            "WHERE curriculumid = $1 " .
            "AND sitename = $2 " .
            "AND date = $3";

        //query the view
        $get_participants = $db->query($fullQuery,
            [$oldInfo['curr-id'], $oldInfo['site'], $dateTime]);
    } else {
        //how many weeks ago do we want in our participant recommendations
        $threeWeeksAgo = date_subtraction('22 days');

        //participant recommendations for attendance form
        $fullQuery = "SELECT * FROM classattendancedetails " .
            "WHERE curriculumid = $1 " .
            "AND sitename = $2 " .
            "AND facilitatorid = $3 " .
            "AND date >= $4 " .
            "ORDER BY date DESC;";

        //query the view
        $get_participants = $db->query($fullQuery,
            [$selected_curr_id, $selected_site, $selected_facilitator, $threeWeeksAgo]);
    }
    $addedPIDs = array();

    //look through the records to see if this person was added before
    //grabs the most up to date information about the zip and number of children
    while($row = pg_fetch_assoc($get_participants)){

        $pid = $row['participantid'];

        $alreadyAdded = false;
        for($j = 0; $j < count($addedPIDs); $j++){
            if($addedPIDs[$j] == $pid) $alreadyAdded = true;
        }

        if(!$alreadyAdded){
            $addedPIDs[] = $pid;
            //not added yet, so let's add
            $pageInformation[] = array(
                "pid"           => $pid,
                "fn"            => $row['firstname'],
                "mi"            => $row['middleinit'],
                "ln"            => $row['lastname'],
                "dob"           => $row['dateofbirth'],
                "zip"           => $row['zipcode'],
                "numChildren"   => $row['numchildren'],
                "race"          => null,
                "comments"      => isset($row['comments']) ? $row['comments'] : null,
                "present"       => false,
                "isNew"         => isset($row['isnew']) ? $row['isnew'] : false, //isNew field from DB
                //people who haven't completed the intake forms and just filled out info in the "no intake form" section
                "firstClass"    => false,
                "sex"           => $row['sex']
            );
        }

    }


}

/* End set page information */

//enums for no intake packet editing
$get_races = $db->execute('shared_query_race_enum',[]);
$get_sexes = $db->execute('shared_query_sex_enum',[]);

//Format the time and date for displaying class information
$convert_date = DateTime::createFromFormat('Y-m-d', $selected_date);
$display_date = $convert_date->format('l, F jS');

$convert_time = DateTime::createFromFormat('h:i A', $selected_time);
$display_time = $convert_time->format('g:i A');


include('header.php');

?>
    <div class="container">
        <form action="" method="post" id="whole-page-form">
            <div class="flex-column">
                <!-- Default container contents -->
                <h3 class="text-center"><?= "$selected_site: $selected_class" ?></h3>
                <h6 class="text-center text-secondary" style="font-weight: 200;"><?= "Class Time: $display_time - $display_date" ?></h6>
                <div class="flex-row">
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-link" style="text-align: center;" onclick="oldInfoDetails()">Edit Class Details</button>
                    </div>
                </div>
                <br />
                <?php

                if(isset($successAddingPerson)){
                    if ($successAddingPerson) {
                        $notification = new Notification('Success!', 'Participant added to list.', 'success');
                        $notification->display();
                    } else {
                        $notification = new Notification('Error!', 'Participant not added to list.', 'danger');
                        $notification->display();
                    }
                }
                if($duplicatePerson){
                    $notification = new Notification('Warning!', 'Duplicate person or page has been refreshed.', 'warning');
                    $notification->display();
                }

                ?>

                <div id="insert-comment-alert-here"></div>

                <!-- Table -->
                <h4 class="mb-3" style="font-weight: 200;">Current Participants</h4>
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="class-list" style="border: 1px solid #EEE;">
                        <thead>
                        <tr>
                            <th class="text-center">Present</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Zip</th>
                            <th>Children Under 18</th>
                            <th>Comments</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="attendance-table-body">

                        <?php
                        //loop through page information and populate table
                        for($i = 0; $i < count($pageInformation); $i++) {
                            //field names - unique field names for individuals which are checked upon post
                            $presentName =      (string) $i . "-" . "check";
                            $commentName =      (string) $i . "-" . "comment";

                            echo "<tr class=\"m-0\" id=\"{$i}\">";
                            echo "<td class='text-center'>";
                            echo "<label class='custom-control custom-checkbox mr-0 pl-3'>";
                            //checkbox checked option
                            $checked = null;
                            $pageInformation[$i]['present'] ? $checked = "checked=\"checked\"" : $checked = "";
                            echo "<input type='checkbox' class='custom-control-input' {$checked} name='{$presentName}'>";
                            echo "<span class='custom-control-indicator'></span>";
                            echo "</label>";
                            echo "</td>";
                            echo "<td>{$pageInformation[$i]['fn']} {$pageInformation[$i]['mi']} {$pageInformation[$i]['ln']}</td>";

                            $age = calculate_age($pageInformation[$i]['dob']);
                            echo "<td>{$age}</td>";
                            echo "<td>{$pageInformation[$i]['zip']}</td>";
                            echo "<td>{$pageInformation[$i]['numChildren']}</td>";
                            echo "<td>";
                            echo "<div class='form-group'>";
                            //pre-fill comment if exists
                            $comment = null;
                            (is_null($pageInformation[$i]['comments'])) ? $comment = "" : $comment = $pageInformation[$i]['comments'];
                            echo "<textarea class='form-control' type='textarea' rows='2' placeholder='enter comments here...' name='{$commentName}'>{$comment}</textarea>";
                            echo "</div>";
                            echo "</td>";
                            echo "<td>";
                            echo "<button class='btn btn-outline-secondary' onclick='editPerson({$i})'>Edit</button>";
                            echo "</td>";
                            echo "</tr>";
                        }

                        if (count($pageInformation) == 0) {
                            echo "<tr><td colspan='7' class='text-center text-muted'><i>No current participants</i></td></tr>";
                        }

                        $_SESSION['serializedInfo'] = serializeParticipantMatrix($pageInformation);
                        ?>
                        </tbody>
                    </table>
                    <!-- /Table -->
                </div>

                <!-- helps identify if we've just added a person -->
                <input type="hidden" id="fromAddPerson" name="fromAddPerson" value="0" />

                <!-- edit button information to know which person we selected -->
                <input type="hidden" id="editButton" name="editButton" value="" />
            </div>

            <br />
            <div class="flex-column">
                <h4 class="mb-3" style="font-weight: 200;">Add New Participants</h4>
                <div id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="card">
                        <div class="card-header" role="tab" id="headingOne">
                            <h5 class="card-title mb-0">
                                <a class="form-header" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    Search for Person
                                </a>
                            </h5>
                        </div>

                        <div id="collapseOne" class="collapse show" role="tabpanel" aria-labelledby="headingOne">
                            <div class="card-body" style="padding: 10px;">
                                <p>
                                    If a person is not shown here but has already filled out the intake packet,
                                    please search for them here.
                                </p>
                                <div class="input-group" style="max-width: 700px; width: 100%; margin: 0 auto">
                                    <input type="text" class="form-control search-participants" name="searchquery"  placeholder="Enter a participant's name...">
                                    <span class="input-group-btn">
                                    <button type="button" class="btn cpca active-search">Search</button>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" role="tab" id="headingTwo">
                            <h5 class="mb-0">
                                <a class="collapsed form-header" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    No Intake Form
                                </a>
                            </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
                            <div class="card-body" style="padding: 10px">
                                <div class="form-group row" style="margin-left: 10px">
                                    <p>If a person has not filled out intake forms, please enter their information below.</p>
                                </div>
                                <div id = "alert-box"></div>
                                <!-- first -->
                                <div class="form-group row">
                                    <label for="new-person-first" class="col-3 col-form-label">First <span style="color:red; display:inline">*</span></label>
                                    <div class="col-9">
                                        <input class="form-control" type="text" value="" id="new-person-first" name="new-person-first" placeholder="enter first name...">
                                    </div>
                                </div>
                                <!-- middle initial -->
                                <div class="form-group row">
                                    <label for="new-person-middle" class="col-3 col-form-label">Middle Initial</label>
                                    <div class="col-9">
                                        <input class="form-control" type="text" value="" id="new-person-middle" name="new-person-middle" placeholder="enter middle initial...">
                                    </div>
                                </div>
                                <!-- last -->
                                <div class="form-group row">
                                    <label for="new-person-last" class="col-3 col-form-label">Last <span style="color:red; display:inline">*</span></label>
                                    <div class="col-9">
                                        <input class="form-control" type="text" value="" id="new-person-last" name="new-person-last" placeholder="enter last name...">
                                    </div>
                                </div>
                                <!-- race -->
                                <div class="form-group row">
                                    <label for="race-select" class="col-3 col-form-label">Race <span style="color:red; display:inline">*</span></label>
                                    <div class="col-9">
                                        <select id="race-select" name="race-select" class="form-control">
                                            <option>Select Race...</option>
                                            <?php
                                            //need to keep track of different races for form validation
                                            $race_array = array();

                                            while($row = pg_fetch_assoc($get_races)){
                                                $raceOption = $row['unnest'];
                                                echo "<option>{$raceOption}</option>";
                                                $race_array[] = $raceOption;
                                            }

                                            //set a session variable so we can validate races after post
                                            $_SESSION['races'] = $race_array;
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Sex -->
                                <div class="form-group row">
                                    <label for="sex-select" class="col-3 col-form-label">Sex <span style="color:red; display:inline">*</span></label>
                                    <div class="col-9">
                                        <select id="sex-select" name="sex-select" class="form-control">
                                            <option>Select Sex...</option>
                                            <?php
                                            //need to keep track of different races for form validation
                                            $sex_array = array();

                                            while($row = pg_fetch_assoc($get_sexes)){
                                                $sexOption = $row['unnest'];
                                                echo "<option>{$sexOption}</option>";
                                                $sex_array[] = $sexOption;
                                            }

                                            //set a session variable so we can validate races after post
                                            $_SESSION['sexes'] = $sex_array;
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Age -->
                                <div class="form-group row">
                                    <label for="age-input" class="col-3 col-form-label">Age <span style="color:red; display:inline">*</span></label>
                                    <div class="col-9">
                                        <input class="form-control" type="number" value="" id="age-input" name="age-input" placeholder="please enter age...">
                                    </div>
                                </div>
                                <!-- Number of children under 18 -->
                                <div class="form-group row">
                                    <label for="num-children-input" class="col-3 col-form-label">Number of children under 18 <span style="color:red; display:inline">*</span></label>
                                    <div class="col-9">
                                        <input class="form-control" type="number" value="" id="num-children-input" name="num-children-input" placeholder="please enter number of children...">
                                    </div>
                                </div>
                                <!-- Zip code -->
                                <div class="form-group row">
                                    <label for="zip-input" class="col-3 col-form-label">Zip code</label>
                                    <div class="col-9">
                                        <input class="form-control" type="text" value="12601" id="zip-input" name="zip-input" placeholder="enter zip code...">
                                    </div>
                                </div>

                                <!-- validate and add to list above -->
                                <div class="form-footer submit">
                                    <button type="button" class="btn cpca" style="margin-left:15px" onclick="addPerson()">Add Person</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <br/>
                <div class="form-footer submit">
                    <button type="button" class="btn cpca" onclick="submitAttendance()" >Submit Attendance</button>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade w-100" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Participants to Attendence</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <script src="/js/attendance-scripts/attendance-form-add-new-person.js"></script>

    <script>
        //name of the only form on the page
        var pageFormName = 'whole-page-form';

        /**
         * sets the location the form submits to
         *
         * @param action{string} - page the form redirects to
         *
         */
        function setFormAction(action){
            document.getElementById(pageFormName).action = action;
        }

        /**
         * submits the form if valid
         */
        function submitAttendance() {
            setFormAction('attendance-form-confirmation');
            document.getElementById(pageFormName).submit();

        }

        /**
         * sets the form's action to edit participant clicked
         *
         * @param buttonNumber{int} - the nth button clicked on the page
         *
         */
        function editPerson(buttonNumber){
            //set form value to button value
            document.getElementById("editButton").value = buttonNumber;

            setFormAction('attendance-edit-participant');
            document.getElementById(pageFormName).submit();
        }

        /**
         * set the form action to direct to edit class info
         */
        function oldInfoDetails(){
            setFormAction('edit-class-info');
            document.getElementById(pageFormName).submit();
        }

        /**
         * Calls function to validate table. If valid,
         * submits the form to the same page
         */
        function addPerson() {
            if(jsValidateTable() === true){
                setFormAction('attendance-form');
                document.getElementById('fromAddPerson').value = 1; //helps to identify that we just added someone
                document.getElementById(pageFormName).submit();
            }
        }
    </script>

    <script>
        //wrapping in jquery to perform searches and adding users
        $(document).ready(function(){
            //when uesr clicks the search button within the 'search for person' card
            //open a modal using user input as search query, then display in modal
            $(".active-search").click(function(){
                //grabbing user input
                var participantSearch = $(".search-participants").val();
                //creaing a new form to search the participant result page for user-entered participant
                $('<form class='+'add-participant'+'>', {
                    "id": "search-participants",
                    "html": '<input type="text" id="searchquery" name="searchquery" value="' + participantSearch + '" />',
                    "method": "post",
                    //setting the destination url to participant search
                    "action": '/participant-search/'+participantSearch
                }).appendTo(document.body);
                //append it to the bottom
                $("body").append("<form class='add-participant' action=''></form>");

                //instead of directing the user to a new page, we're going to display the
                //results of the search in a modal. There is now a modal hidden at the bottom of the screen that will
                //become active once the search is triggered
                $(".modal-body").empty();
                //using ajax to stay on the origin page
                $.ajax({
                    type: 'GET',
                    url: '/participant-search/'+participantSearch,
                    dataType: 'text',
                    data: $(".add-participant").serialize(),
                    success:function(data){
                        //looking for the participant results
                        var sentName = $(data).find(".list-group-item");
                        //for each result, display a list element with the name of the participant
                        //and a 'add to class' button
                        $.each(sentName, function(){
                            var sentURL = $(this).find('a').attr("href");
                            if(sentURL != undefined) {
                                var sentNameList = $(this).find("span").html();
                                var matched = sentURL.match( /\/ps-view-participant\/(\d*)/);
                                var peopleid = matched[matched.length-1];
                                var details = $(this).find(".sublist").text();
                                //add the view to the modal
                                $(".modal-body").append(
                                    "<form class=\"add-to-sheet\" method =\"POST\"action=\"\">"+
                                    "<input type=\"hidden\" value="+peopleid+" name=\"pidLookup\">"+
                                    "<div class='card'>" +
                                    "<div class='card-body'>" +
                                    "<h4 class='card-title'>" + sentNameList + "<input type='submit' name='lookupId' value='Add' class='btn cpca float-right submit-search' /></h4>" +
                                    "</div>" +
                                    "<div class='card-footer'>" +
                                    details +
                                    "</div>" +
                                    "</div>" +
                                    "</form>");
                                $("#exampleModal").modal();
                            }
                        })
                    }
                });
            });

            //allow user to press 'enter' while searching without
            //resubmitting the whole form just yet
            $('.search-participants').keypress(function(event){
                if(event.keyCode == 13){
                    $('.active-search').click();
                    event.preventDefault();
                }
            });
        });
    </script>

<?php
include('footer.php');
?>