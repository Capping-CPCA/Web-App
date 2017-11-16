<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Confirmation screen.
 *
 * Page showing all the selected people and comments the facilitator made.
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version [version number]
 * @since [initial version number]
 */

require ('attendance_utilities.php');

if (!isset($_SESSION['attendance-info'])) {
    header("Location: /new-class");
    die();
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


$pageInformation = null;

//if we have previous information passed to us from lookup form or add person form,
//  then display this information instead of db information
if(isset($_SESSION['serializedInfo'])) {
    //update class information from previous form
    updateSessionClassInformation();
    $pageInformation = deserializeParticipantMatrix($_SESSION['serializedInfo']);
} else { //you shouldn't be here
    die; //quick and painful
}

$convert_date = DateTime::createFromFormat('Y-m-d', $selected_date);
$display_date = $convert_date->format('l, F jS');

$convert_time = DateTime::createFromFormat('h:i A', $selected_time);
$display_time = $convert_time->format('g:i A');

include('header.php');
?>

    <script>
        function setFormAction(formID, action){
            document.getElementById(formID).action = action;
        }

        function submitAttendance() {
            //set page to go to that
            setFormAction('attendance-sheet', 'attendance-form-confirmed');
            document.getElementById('attendance-sheet').submit();
        }

        function editAttendance(){
            setFormAction('attendance-sheet', 'attendance-form');
            document.getElementById('attendance-sheet').submit();
        }

    </script>


    <div class="container">
    <div class="flex-column">
        <!-- Default container contents -->

        <h3 class="text-center"><?= "$selected_site: $selected_class" ?></h3>
        <h6 class="text-center text-secondary" style="font-weight: 200;"><?= "Class Time: $display_time - $display_date" ?></h6>
        <br />

        <div class="alert alert-dark text-center" role="alert">
            Please review this attendance sheet and verify its contents.
        </div>

        <form action="" method="post" id="attendance-sheet">
            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped" id="class-list" style="border: 1px solid #EEE;">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Zip</th>
                        <th>Number of </br> children under 18</th>
                        <th>Comments</th>
                    </tr>
                    </thead>
                    <tbody>


                    <tr class="m-0">
                        <?php
                        //loop through page information and populate table
                        for($i = 0; $i < count($pageInformation); $i++) {
                            $present = (boolean)$pageInformation[$i]['present'];

                            if ($present) {

                                //field names - unique field names for individuals which are checked upon post
                                $presentName = (string)$i . "-" . "check";
                                $commentName = (string)$i . "-" . "comment";

                                echo "<tr class=\"m-0\" id=\"{$i}\">";
                                echo "<td>{$pageInformation[$i]['fn']} {$pageInformation[$i]['mi']} {$pageInformation[$i]['ln']}</td>";

                                $age = calculate_age($pageInformation[$i]['dob']);
                                echo "<td>{$age}</td>";
                                echo "<td>{$pageInformation[$i]['zip']}</td>";
                                echo "<td>{$pageInformation[$i]['numChildren']}</td>";
                                echo "<td>";
                                echo "<div class=\"form-group\">";
                                echo "<div class=\"col-10\">";
                                echo "<fieldset disabled>"; //disable comments
                                //pre-fill comment if exists
                                $comment = null;
                                $placeholder = null; //disable placeholder
                                (is_null($pageInformation[$i]['comments'])) ? $comment = "" : $comment = $pageInformation[$i]['comments'];
                                (($pageInformation[$i]['comments']) == '') ? $placeholder = "" : $placeholder = "placeholder=\"enter comments here...\"";
                                echo "<textarea class=\"form-control\" type=\"textarea\" rows=\"2\" {$placeholder} name='{$commentName}'>{$comment}</textarea>";
                                echo "</fieldset>";
                                echo "</div>";
                                echo "</div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            //update the session information
                            $_SESSION['serializedInfo'] = serializeParticipantMatrix($pageInformation);
                        }
                        ?>
                    </tbody>
                </table>
                <!-- /Table -->
            </div>
            <?php
            //hidden form values
            echo "<input type=\"hidden\" id=\"fromConfirmPage\" name=\"fromConfirmPage\" value=\"1\" />";

            //edit button information
            echo "<input type=\"hidden\" id=\"editButton\" name=\"editButton\" value=\"\" />";
            ?>
        </form>

        <div class="flex-column">
            <div class="d-flex flex-row justify-content-between">
                <div class="p-2">
                    <a href="/attendance-form">
                        <button type="button" class="btn btn-outline-secondary" style="margin-left: 7px;">Edit Attendance</button>
                    </a>
                </div>
                <div class="p-2">
                    <button type="button" class="btn cpca" onclick="submitAttendance()" style="margin-right: 7px;">Submit Attendance</button>
                </div>
            </div>
        </div>


    </div>
    </div>


<?php
include('footer.php');
?>