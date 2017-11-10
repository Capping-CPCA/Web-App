<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * edit participant page
 *
 * provides the options to change number of children and zip code
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version [version number]
 * @since [initial version number]
 */

//after confirmation screen or if a facilitator chooses to edit a class

authorizedPage();

global $db;

require("attendance_utilities.php");

include('header.php');

$selected_class = $_POST['classes'];
$selected_curr = $_POST['curr'];
$selected_date = $_POST['date-input'];
$selected_time = $_POST['time-input'];
$selected_site = $_POST['site'];
$selected_lang = $_POST['lang'];
$selected_facilitator = $_POST['facilitator'];

//1. deserialize and update session info
//update class information from previous form

//if we're not from the confirm edit page, update page information with attendance form post fields
if(!isset($_POST['fromConfirmEditParticipant'])){
    updateSessionClassInformation();
}
$pageInformation = deserializeParticipantMatrix($_SESSION['serializedInfo']);

$checkPageInfo = deserializeParticipantMatrix($_SESSION['serializedInfo']);

//2. grab the post to see which person we clicked to edit
//shouldn't be on this page if POST not set

if(!isset($_POST['editButton'])){
    echo "<h5>Error you must choose a participant to edit.</h5>";
    die(); //from a juggling accident
}

$selected_edit_num = $_POST['editButton'];
//3. grab person's session info
$selected_person = $pageInformation[$selected_edit_num];

//4. display information and changeable information with a button to confirm or go back
    //--go back sends back all page info
    //--confirm js validates data

//5. send to confirmation screen and php validate
    //--confirm button: modifies session info and sends to attendance sheet
    //--back button: sends user back to edit page

?>

    <script type="text/javascript">

        function setFormAction(pageFormName, action){
            document.getElementById(pageFormName).action = action;
        }
        function cancelEdit() {
            setFormAction('participant-edit', 'attendance-form');
            document.getElementById('participant-edit').submit();
        }

        function submitEdit() {
            var success = validateFieldsJS();
            if(success === true){
                setFormAction('participant-edit', 'edit-participant-confirm');
                document.getElementById('participant-edit').submit();
            }
        }

        function validateFieldsJS(){
            var childrenInput = document.getElementById("num-children-input-change").value;
            var zipInput = document.getElementById("zip-input-change").value;

            var validNC = validateNumChildren(childrenInput);
            var validZip = validateZip(zipInput);

            if (validNC && validZip){
                return true;
            } else if(!validNC){
                //make error box
                createMessage(false, "Number of children is not valid");
                return false;
            } else{ //zip not valid
                //make error box
                createMessage(false, "Zip code is not valid");
                return false;
            }
        }

        function createMessage(success, errorMessage) {
            var div = document.createElement("div");
            div.setAttribute("role", "alert");

            if(!success){
                div.setAttribute("class", "alert alert-warning");
                div.setAttribute("style", "margin: 10px; text-align: center;");
                div.innerHTML = "<strong>Oops! </strong>" + errorMessage;
            }

            //success or failure message
            var insertAlertHere = document.getElementById("alert-box");

            while(insertAlertHere.hasChildNodes()) { //remove all children
                insertAlertHere.removeChild(insertAlertHere.lastChild);
            }
            insertAlertHere.appendChild(div);
        }

        function validateNumChildren(num) {
            var number = parseInt(num);
            if(isNaN(number)) return false;
            if(typeof(number) !== "number") return false;
            //returns true if a valid number of children
            return((num >= 0) && (num <= 25))
        }

        function validateZip(zip) {
            //validate zip code (from stackoverflow)
            return (/(^\d{5}$)|(^\d{5}-\d{4}$)/.test(zip));
        }
    </script>

    <div class="container-fluid">
        <div class="row flex-column">
            <!-- Default container contents -->
            <div class="h3 text-center">

            </div>

            <div class="card">
                <div class="card-block">
                    <div id = "alert-box"></div>
                    <form id="participant-edit" action="" method="post">
                        <h3 class="card-title" style="margin-top: 10px; margin-left: 10px; text-align: center;">Edit Participant</h3>

                        <div style="margin:15px; font-size: 13pt;">
                            <?php
                            $fullName = $selected_person['fn'] . " " . $selected_person['mi'] . " " . $selected_person['ln'];
                            ?>
                            <p><b>Name:</b> <?php echo $fullName; ?></p>
                            <p><b>Date of Birth:</b> <?php echo $selected_person['dob']; ?></p>
                            <p><b>Race:</b> <?php echo $selected_person['race'] ?></p>

                            <!-- Number of children under 18 -->
                            <div class="form-group row">
                                <label for="num-children-input" class="col-3 col-form-label" style="text-align: left;"><b>Number of children under 18 </b></label>
                                <div class="col-9">
                                    <?php
                                    //get the number of children
                                    $selected_person_nc = null;
                                    if(isset($_POST['num-children-input-change'])) $selected_person_nc = $_POST['num-children-input-change']; //from confirm page
                                    else $selected_person_nc = $selected_person['numChildren']; //from attendance sheet

                                    echo "<input class=\"form-control\" type=\"number\" value=\"{$selected_person_nc}\" id=\"num-children-input-change\" name=\"num-children-input-change\" placeholder=\"please enter number of children...\">";
                                    ?>
                                </div>
                            </div>
                            <!-- Zip code -->
                            <div class="form-group row">
                                <label for="zip-input" class="col-3 col-form-label" style="text-align: left;"><b>Zip code</b></label>
                                <div class="col-9">
                                    <?php
                                    $selected_person_zip = null;
                                    if(isset($_POST['zip-input-change'])) $selected_person_zip = $_POST['zip-input-change'];
                                    else $selected_person_zip = $selected_person['zip'];

                                    echo "<input class=\"form-control\" type=\"text\" value=\"{$selected_person_zip}\" id=\"zip-input-change\" name=\"zip-input-change\" placeholder=\"enter zip code...\">";
                                    ?>
                                </div>
                            </div>
                        </div>




                        <?php
                        //class information

                        echo "<input type=\"hidden\" id=\"classes\" name=\"classes\" value=\"{$selected_class}\" />";
                        echo "<input type=\"hidden\" id=\"curr\" name=\"curr\" value=\"{$selected_curr}\" />";
                        echo "<input type=\"hidden\" id=\"date-input\" name=\"date-input\" value=\"{$selected_date}\" />";
                        echo "<input type=\"hidden\" id=\"time-input\" name=\"time-input\" value=\"{$selected_time}\" />";
                        echo "<input type=\"hidden\" id=\"site\" name=\"site\" value=\"{$selected_site}\" />";
                        echo "<input type=\"hidden\" id=\"lang\" name=\"lang\" value=\"{$selected_lang}\" />";
                        echo "<input type=\"hidden\" id=\"facilitator\" name=\"facilitator\" value=\"{$selected_facilitator}\" />";

                        echo "<input type=\"hidden\" id=\"fromConfirmPage\" name=\"fromConfirmPage\" value=\"1\" />";

                        //edit button information
                        echo "<input type=\"hidden\" id=\"editButton\" name=\"editButton\" value=\"{$selected_edit_num}\" />";

                        ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="row flex-column">
            <div class="d-flex flex-row justify-content-between">
                <div class="p-2">
                    <button type="button" class="btn btn-danger" onclick="cancelEdit()" style="margin-left: 7px;">Cancel Edit</button>
                </div>
                <div class="p-2">
                    <button type="button" class="btn btn-success" onclick="submitEdit()" style="margin-right: 7px;">Confirm Changes</button>
                </div>
            </div>
        </div>


    </div>

<?php
include('footer.php');
?>