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
 * @version [version number]
 * @since [initial version number]
 */

authorizedPage();

//generic db script copied and pasted

global $db;

require "attendance_utilities.php";

include('header.php');
	
//make sure that information was entered into form
if(!isset($_POST['curr']))
{
    echo "<div class='container'>";
    echo "<p>Error: Please first pick a class to take attendance for.</p>";
    echo "<p><a href='new-class'>Class Selection Link</a></p>";
    echo "</div>";
    include('footer.php');
    die;
}

//grab post,
$selected_class = $_POST['classes'];
$selected_curr = $_POST['curr'];
$selected_date = $_POST['date-input'];
$selected_time = $_POST['time-input'];
$selected_site = $_POST['site'];
$selected_lang = $_POST['lang'];

$selected_facilitator = $_POST['facilitator'];

$pageInformation = array();

$successAddingPerson = false;
$duplicatePerson = false;

//if we have previous information passed to us from lookup form or add person form,
//  then display this information instead of db information
if(isset($_SESSION['serializedInfo'])) {
    //we're coming from the attendance-form-confirmation page - no change needed
    if(isset($_POST['fromConfirmPage'])) {
        $pageInformation = deserializeParticipantMatrix($_SESSION['serializedInfo']);
    }
    else if(isset($_POST['fromConfirmEditParticipant'])){
        $pageInformation = deserializeParticipantMatrix($_SESSION['serializedInfo']);
        $selected_edit_num = $_POST['editButton'];
        $changed_num_children = $_POST['num-children-input-change'];
        $changed_zip = $_POST['zip-input-change'];

        //validate
        if(validateNumChildren($changed_num_children) && validateZip($changed_zip)){
            //get the corresponding person
            $pageInformation[$selected_edit_num]['numChildren'] = $changed_num_children;
            $pageInformation[$selected_edit_num]['zip'] = $changed_zip;

            //update the session matrix
            $_SESSION['serializedInfo'] = serializeParticipantMatrix($pageInformation);
        } else{ //TODO: change when tested
            echo "<h1>Invalid Input</h1>";
            die();
        }
    }
    else if(isset($_POST['lookupId'])){
        $pageInformation = deserializeParticipantMatrix($_SESSION['serializedInfo']);
        $lookupId = $_POST['pidLookup'];

        //lookup most recent info from DB --either from class info or from intake packet
        $resultClassPastAttendance = $db->no_param_query(
                "select * from classattendancedetails " .
                "where participantid = {$lookupId} " .
                "order by date desc; "
        );

        $fn = $mi = $ln = $dob = $race = $zip = $nc = $isNew = null;
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
        } else{ //we didn't find that information find name and other info
            //grab info from intake packet
            $resultPersonLookup = $db->no_param_query(
                "select pe.firstname, pe.middleinit, pe.lastname, pa.dateofbirth, pa.race " .
                "from people pe, participants pa " .
                "where pe.peopleid = pa.participantid " .
                "and pe.peopleid = {$lookupId};"
            );
            $row = pg_fetch_assoc($resultPersonLookup);
            $fn = $row['firstname'];
            $mi = $row['middleinit'];
            $ln = $row['lastname'];
            $dob = $row['dateofbirth'];
            $race = $row['race'];
            $isNew = true;
        }

        //look for duplicates
        for($k = 0; $k < count($pageInformation); $k++){
            if($pageInformation[$k]['pid'] == $lookupId){
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
                "firstClass"    => false
            );
        }

        //update our posted information
        updateSessionClassInformation();

    }
    //we're coming from the current page and posting new participant info
    else if(isset($_POST['fromAddPerson']) && $_POST['fromAddPerson'] == 1){ //value is changed when click to add person
        $firstTimeSubmitting = true;

        $pageInformation = deserializeParticipantMatrix($_SESSION['serializedInfo']);

        $fn = $_POST['new-person-first'];
        $mi = $_POST['new-person-middle'];
        $ln = $_POST['new-person-last'];
        $race = $_POST['race-select'];
        $ageInput = $_POST['age-input'];
        $numC = $_POST['num-children-input'];
        $zipInput = $_POST['zip-input'];


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
                $pageInformation[$j]['zip'] == $zipInput
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
                    "firstClass"    => true
                );

                $successAddingPerson = true;
            }
        }


    }
    else{
        //default, just load the page
        $pageInformation = deserializeParticipantMatrix($_SESSION['serializedInfo']);
    }

}
//else grab information from the db and format it into the associative array format
else {

    $threeWeeksAgo = date_subtraction('22 days');

    $fullQuery = "select * from classattendancedetails " .
        " where curriculumname = '" . escape_apostrophe($selected_curr) . "' " .
        "and facilitatorid = {$selected_facilitator} " .
        "and date >= '{$threeWeeksAgo}'" .
        "ORDER BY date DESC;";

    //query the view
    $get_participants = $db->no_param_query($fullQuery);

    $addedPIDs = array();

    while($row = pg_fetch_assoc($get_participants)){

        $pid = $row['participantid'];

        //look through the records to see if this person was added before

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
                "comments"      => null,
                "present"       => false,
                "isNew"         => false, //isNew field from DB
                //people who haven't completed the intake forms and just filled out info in the "no intake form" section
                "firstClass"    => false
            );
        }

    }


}


$get_races = $db->no_param_query("SELECT unnest(enum_range(NULL::race));");

$convert_date = DateTime::createFromFormat('Y-m-d', $selected_date);
$display_date = $convert_date->format('l, F jS');

$convert_time = DateTime::createFromFormat('h:i A', $selected_time);
$display_time = $convert_time->format('g:i A');



?>
    <script src="/js/attendance-scripts/attendance-form-add-new-person.js"></script>

       <script>
        //name of the only form on the page
        var pageFormName = 'whole-page-form';
        //input: page the form redirects to
        //output: none
        function setFormAction(action){
            document.getElementById(pageFormName).action = action;
        }
        function submitAttendance() {
            if(validateComments()){
                setFormAction('attendance-form-confirmation');
                document.getElementById(pageFormName).submit();
            }
        }
        //used in edit buttons
        function editPerson(buttonNumber){
            //set form value to button value
            document.getElementById("editButton").value = buttonNumber;

            setFormAction('edit-participant');
            document.getElementById(pageFormName).submit();
        }
        function addPerson() {
            //TODO: handle submission logic
            if(jsValidateTable() === true){
                setFormAction('attendance-form');
                document.getElementById('fromAddPerson').value = 1; //helps to identify that we just added someone
                document.getElementById(pageFormName).submit();
            }
        }

        function validateComments(){
            //loop through table and verify each comment
            var tableParent = document.getElementById('attendance-table-body');
            //create alert box and report what record failed

            for(var i = 0; i < tableParent.childElementCount; i++){
                //grab the contents of the textarea
                var tableRow = tableParent.children[i];
                var textArea = tableRow.children[(tableRow.childElementCount - 2)].children[0].children[0].children[0];

                var comment = textArea.value;
                //not a valid comment
                if(!(validateComment(comment))){
                    var name = tableRow.children[1].innerHTML;
                    createCommentErrorBox(name);
                    return false;
                }
            }


            return true;
        }

        //input: name of comment in person's text area that has special/forbidden characters
        function createCommentErrorBox(errorFor){
            var insertAlertHere = document.getElementById('insert-comment-alert-here');

            while(insertAlertHere.hasChildNodes()) { //remove all children
                insertAlertHere.removeChild(insertAlertHere.lastChild);
            }

            //create error box
            var div = document.createElement("div");
            div.setAttribute("role", "alert");
            div.setAttribute("class", "alert alert-warning");
            div.innerHTML = "<strong>Oops! </strong>Error in comment for <strong><em>" + errorFor +  "</em></strong>: Please use only letters, numbers, periods, commas, " +
                "spaces, and question marks in comments.";

            insertAlertHere.appendChild(div);
        }

        function validateComment(comment) {
            //returns true if matched, validates for a-z A-Z spaces period or comma
            return (/^[a-zA-Z0-9\s.,?]*$/.test(comment));
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
							if(sentURL != undefined){
							var sentNameList = $(this).find("span").html();
							console.log(sentURL);
							var matched = sentURL.match( /\/view-participant\/(\d*)/);
							var peopleid = matched[matched.length-1];
							var details = $(this).find(".sublist").text();
							//add the view to the modal
							$(".modal-body").append("<form class=\"add-to-sheet\" method =\"POST\"action=\"\">"+
							"<input type=\"hidden\" value="+peopleid+" name=\"pidLookup\">"+
							"<input type=\"hidden\" value=\"<?=$selected_curr?>\" name=\"curr\">"+
							"<input type=\"hidden\" value=\"<?=$selected_class?>\" name=\"classes\">"+
							"<input type=\"hidden\" value=\"<?=$selected_date?>\" name=\"date-input\">"+
							"<input type=\"hidden\" value=\"<?=$selected_time?>\" name=\"time-input\">"+
							"<input type=\"hidden\" value=\"<?=$selected_site?>\" name=\"site\">"+
							"<input type=\"hidden\" value=\"<?=$selected_lang?>\" name=\"lang\">"+
							"<input type=\"hidden\" value=\"<?=$selected_facilitator?>\" name=\"facilitator\">"+
								"<ul class='list-group'>"+
								"<li class='list-group-item'>"+sentNameList+
								"<input type=\"submit\" name=\"lookupId\" value= \"Add\" class=\"btn cpca float-right submit-search\">"+
								"</li>"+
								"<ul class='list-group'><li class='list-group-item'>"+details+"</li></ul>"+
								"</ul>"+
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

    <div class="container-fluid">
        <form action="" method="post" id="whole-page-form">
        <div class="row flex-column">
            <!-- Default container contents -->
            <div class="h3 text-center">
                <?php
                    echo "{$selected_curr} : {$selected_class}";
                ?>
            </div>
            <div class="h6 text-center">
                <?php
                    echo "Class Time: {$display_time} - {$display_date}";
                ?>
            </div>

            <?php

            if($successAddingPerson){
                echo "<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\"> ";
                echo        "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"> ";
                echo        "<span aria-hidden=\"true\">&times;</span> ";
                echo        "</button>";
                echo        "<div style = 'text-align: center;'><strong>Success!</strong> Participant added to list. </div>";
                echo    "</div>";
            }
            if($duplicatePerson){
                echo "<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\"> ";
                echo        "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"> ";
                echo        "<span aria-hidden=\"true\">&times;</span> ";
                echo        "</button>";
                echo        "<div style = 'text-align: center;'><strong>Warning!</strong> Duplicate person or page has been refreshed. </div>";
                echo    "</div>";
            }

            ?>

                <div class="card" style="margin-bottom: 10px;">
                    <div class="card-block">

                        <div id="insert-comment-alert-here"></div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-hover table-striped" id="class-list">
                                    <thead>
                                    <tr>
                                        <th>Present</th>
                                        <th>Name</th>
                                        <th>Age</th>
                                        <th>Zip</th>
                                        <th>Number of </br> children under 18</th>
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
                                        echo "<td>";
                                        echo "<label class=\"custom-control custom-checkbox\">";
                                        //checkbox checked option
                                        $checked = null;
                                        $pageInformation[$i]['present'] ? $checked = "checked=\"checked\"" : $checked = "";
                                        echo "<input type=\"checkbox\" class=\"custom-control-input\" {$checked} name='{$presentName}'>";
                                        echo "<span class=\"custom-control-indicator\"></span>";
                                        echo "</label>";
                                        echo "</td>";
                                        echo "<td>{$pageInformation[$i]['fn']} {$pageInformation[$i]['mi']} {$pageInformation[$i]['ln']}</td>";

                                        $age = calculate_age($pageInformation[$i]['dob']);
                                        echo "<td>{$age}</td>";
                                        echo "<td>{$pageInformation[$i]['zip']}</td>";
                                        echo "<td>{$pageInformation[$i]['numChildren']}</td>";
                                        echo "<td>";
                                        echo "<div class=\"form-group\">";
                                        echo "<div class=\"col-10\">";
                                        //pre-fill comment if exists
                                        $comment = null;
                                        (is_null($pageInformation[$i]['comments'])) ? $comment = "" : $comment = $pageInformation[$i]['comments'];
                                        echo "<textarea class=\"form-control\" type=\"textarea\" rows=\"2\" placeholder=\"enter comments here...\" name='{$commentName}'>{$comment}</textarea>";
                                        echo "</div>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo "<td>";
                                        echo "<button class='btn btn-link' onclick='editPerson({$i})'>Edit</button>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }

                                    $_SESSION['serializedInfo'] = serializeParticipantMatrix($pageInformation);
                                        ?>
                                    </tbody>
                                </table>
                                <!-- /Table -->
                            </div>
                            <?php
                            //hidden form values

                            //class information
                            echo "<input type=\"hidden\" id=\"classes\" name=\"classes\" value=\"{$selected_class}\" />";
                            echo "<input type=\"hidden\" id=\"curr\" name=\"curr\" value=\"{$selected_curr}\" />";
                            echo "<input type=\"hidden\" id=\"date-input\" name=\"date-input\" value=\"{$selected_date}\" />";
                            echo "<input type=\"hidden\" id=\"time-input\" name=\"time-input\" value=\"{$selected_time}\" />";
                            echo "<input type=\"hidden\" id=\"site\" name=\"site\" value=\"{$selected_site}\" />";
                            echo "<input type=\"hidden\" id=\"lang\" name=\"lang\" value=\"{$selected_lang}\" />";
                            echo "<input type=\"hidden\" id=\"facilitator\" name=\"facilitator\" value=\"{$selected_facilitator}\" />";

                            //helps identify if we've just added a person
                            echo "<input type=\"hidden\" id=\"fromAddPerson\" name=\"fromAddPerson\" value=\"0\" />";

                            //edit button information
                            echo "<input type=\"hidden\" id=\"editButton\" name=\"editButton\" value=\"\" />";
                            ?>
                    </div>
                </div>
        </div>

        <div class="row flex-column">

            <div id="accordion" role="tablist" aria-multiselectable="true">
                <div class="card">
                        <div class="card-header" role="tab" id="headingOne">
                            <h5 class="mb-0">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    Search for Person
                                </a>
                            </h5>
                        </div>

                        <div id="collapseOne" class="collapse show" role="tabpanel" aria-labelledby="headingOne">
                            <div class="card-block" style="padding: 10px;">
                                <p>
                                    If a person is not shown here but has already filled out the intake packet,
                                    please search for them here.
                                </p>
                                    <div class="form-group">
                                        <input type="text" class="form-control search-participants" name="searchquery"  placeholder="Begin typing participant's name...">
                                    </div>
                                    <button type="button" class="btn cpca form-control active-search" >Submit</button>
                            </div>
                        </div>
                </div>
                <div class="card">
                    <div class="card-header" role="tab" id="headingTwo">
                        <h5 class="mb-0">
                            <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                No Intake Form
                            </a>
                        </h5>
                    </div>
                    <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
                            <div class="card-block" style="padding: 10px">
                                <div class="form-group row" style="margin-left: 10px">
                                    <p>If a person has not filled out intake forms, please enter their information below.</p>
                                </div>
                                <div id = "alert-box"></div>
                                <!-- first -->
                                <div class="form-group row">
                                    <label for="new-person-first" class="col-3 col-form-label">First <div style="color:red; display:inline">*</div></label>
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
                                    <label for="new-person-last" class="col-3 col-form-label">Last <div style="color:red; display:inline">*</div></label>
                                    <div class="col-9">
                                        <input class="form-control" type="text" value="" id="new-person-last" name="new-person-last" placeholder="enter last name...">
                                    </div>
                                </div>
                                <!-- race
                                TODO: dynamically get races
                                -->
                                <div class="form-group row">
                                    <label for="race-select" class="col-3 col-form-label">Race <div style="color:red; display:inline">*</div></label>
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
                                <!-- Age -->
                                <div class="form-group row">
                                    <label for="age-input" class="col-3 col-form-label">Age <div style="color:red; display:inline">*</div></label>
                                    <div class="col-9">
                                        <input class="form-control" type="number" value="" id="age-input" name="age-input" placeholder="please enter age...">
                                    </div>
                                </div>
                                <!-- Number of children under 18 -->
                                <div class="form-group row">
                                    <label for="num-children-input" class="col-3 col-form-label">Number of children under 18 <div style="color:red; display:inline">*</div></label>
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
                                <div class = "row">
                                    <button type="button" class="btn btn-primary" style="margin-left:15px" onclick="addPerson()">Add Person</button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>

            <br/>
            <div class="d-flex justify-content-start">
                <button type="button" class="btn btn-success" onclick="submitAttendance()" >Submit Attendance</button>
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


<?php
include('footer.php');
?>