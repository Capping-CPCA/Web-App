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

global $db;

require("attendance_utilities.php");

if (!isset($_SESSION['attendance-info'])) {
    header("Location: /new-class");
    die();
}

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

include('header.php');
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
                setFormAction('participant-edit', 'attendance-form');
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
                div.setAttribute("class", "alert alert-danger");
                div.setAttribute("style", "margin: 10px; text-align: center;");
                div.innerHTML = "<strong>Error! </strong>" + errorMessage;
            }

            //success or failure message
            var insertAlertHere = document.getElementById("alert-box");

            while(insertAlertHere.hasChildNodes()) { //remove all children
                insertAlertHere.removeChild(insertAlertHere.lastChild);
            }
            insertAlertHere.appendChild(div);
        }

        function validateNumChildren(num) {
            if (num === "")
                return true;

            var number = parseInt(num);
            if(isNaN(number)) return false;
            if(typeof(number) !== "number") return false;
            //returns true if a valid number of children
            return((num >= 0) && (num <= 25))
        }

        function validateZip(zip) {
            if (zip === "")
                return true;

            //validate zip code (from stackoverflow)
            return (/(^\d{5}$)/.test(zip));
        }
    </script>

    <div class="container">
        <div class="flex-column">
            <!-- Default container contents -->
            <div class="h3 text-center">

            </div>

            <?php
            $fullName = $selected_person['fn'] . " " . $selected_person['mi'] . " " . $selected_person['ln'];
            ?>

            <div class="jumbotron form-wrapper mb-3">
                <div id = "alert-box"></div>
                <h2 class="display-4 text-center" style="font-size: 34px"><?= $fullName ?></h2>
                <form id="participant-edit" action="" method="post">

                    <div style="margin:15px; font-size: 13pt;">
                        <p><b>Date of Birth:</b> <?= $selected_person['dob']; ?></p>
                        <p><b>Race:</b> <?= $selected_person['race'] ?></p>

                        <!-- Number of children under 18 -->
                        <div class="form-group">
                            <label for="num-children-input" class="col-form-label" style="text-align: left;"><b>Number of children under 18 </b></label>
                                <?php
                                //get the number of children
                                $selected_person_nc = null;
                                if(isset($_POST['num-children-input-change'])) $selected_person_nc = $_POST['num-children-input-change']; //from confirm page
                                else $selected_person_nc = $selected_person['numChildren']; //from attendance sheet

                                echo "<input class=\"form-control\" type=\"number\" value=\"{$selected_person_nc}\" id=\"num-children-input-change\" name=\"num-children-input-change\" placeholder=\"Please enter number of children...\">";
                                ?>
                        </div>
                        <!-- Zip code -->
                        <div class="form-group">
                            <label for="zip-input" class="col-form-label" style="text-align: left;"><b>Zip code</b></label>
                                <?php
                                $selected_person_zip = null;
                                if(isset($_POST['zip-input-change'])) $selected_person_zip = $_POST['zip-input-change'];
                                else $selected_person_zip = $selected_person['zip'];

                                echo "<input class=\"form-control\" type=\"text\" value=\"{$selected_person_zip}\" id=\"zip-input-change\" name=\"zip-input-change\" placeholder=\"Enter zip code...\">";
                                ?>
                        </div>
                    </div>

                    <!-- edit button information -->
                    <input type="hidden" id="editButton" name="editButton" value="<?= $selected_edit_num ?>" />
                    
                    <input type="hidden" id="fromConfirmPage" name="fromConfirmEditParticipant" value="1" />

                    <div class="form-footer submit">
                        <a href="/attendance-form">
                            <button type="button" class="btn btn-outline-secondary" style="margin-right: 7px;">Cancel</button>
                        </a>
                        <button type="button" class="btn cpca" onclick="submitEdit()" style="margin-right: 7px;">Confirm Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
include('footer.php');
?>