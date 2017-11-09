<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * confirmation page for edit participant
 *
 * shows all changes and asks if you want to submit them
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

$changed_numChildren = $_POST['num-children-input-change'];
$changed_zip = $_POST['zip-input-change'];
//validate inputs in PHP when submitted on attendance_form.php

if(!isset($_POST['editButton'])){
    echo "<h5>Error you must choose a participant to edit.</h5>";
    die(); //from a juggling accident
}

$pageInformation = deserializeParticipantMatrix($_SESSION['serializedInfo']);

$selected_edit_num = $_POST['editButton'];
$selected_person = $pageInformation[$selected_edit_num];
?>

<script type="text/javascript">
    function setFormAction(pageFormName, action){
        document.getElementById(pageFormName).action = action;
    }
    function cancelConfirm() {
        setFormAction('participant-edit-confirm', 'edit-participant');
        document.getElementById('participant-edit-confirm').submit();
    }

    function confirmSubmit() {
        setFormAction('participant-edit-confirm', 'attendance-form');
        document.getElementById('participant-edit-confirm').submit();
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
                    <form id="participant-edit-confirm" action="" method="post">
                        <h3 class="card-title" style="margin-top: 10px; margin-left: 10px; text-align: center;">Confirm Changes</h3>

                        <div style="margin:15px; font-size: 13pt;">
                            <?php
                            $fullName = $selected_person['fn'] . " " . $selected_person['mi'] . " " . $selected_person['ln'];
                            ?>
                            <p><b>Name:</b> <?php echo $fullName; ?></p>
                            <p><b>Date of Birth:</b> <?php echo $selected_person['dob']; ?></p>
                            <p><b>Race:</b> <?php echo $selected_person['race'] ?></p>
                            <p><b>Number of children under 18:</b> <?php echo $changed_numChildren ?></p>
                            <p><b>Zip code:</b> <?php echo $changed_zip ?></p>

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

                        //edit button information
                        echo "<input type=\"hidden\" id=\"editButton\" name=\"editButton\" value=\"{$selected_edit_num}\" />";

                        //changed field values
                        echo "<input type=\"hidden\" id=\"num-children-input-change\" name=\"num-children-input-change\" value=\"{$changed_numChildren}\" />";
                        echo "<input type=\"hidden\" id=\"zip-input-change\" name=\"zip-input-change\" value=\"{$changed_zip}\" />";

                        //lets the attendance form know to change a record
                        echo "<input type=\"hidden\" id=\"fromConfirmEditParticipant\" name=\"fromConfirmEditParticipant\" value=\"\" />";
                        ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="row flex-column">
            <div class="d-flex flex-row justify-content-between">
                <div class="p-2">
                    <button type="button" class="btn btn-warning" onclick="cancelConfirm()" style="margin-left: 7px;">Change Field</button>
                </div>
                <div class="p-2">
                    <button type="button" class="btn btn-success" onclick="confirmSubmit()" style="margin-right: 7px;">Confirm Changes</button>
                </div>
            </div>
        </div>


    </div>

<?php
include('footer.php');
?>