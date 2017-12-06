<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * enter information to take attendance for new class
 *
 * provides a list of fields to specify what class attendance will be recorded for
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version 1.0
 * @since 1.0
 */

global $db;

require ('attendance_utilities.php');
require ('shared_queries.php');

//form options
$result_curriculum = $db->execute('shared_query_curriculum', []);

$result_classes = $db->execute('shared_query_classes', []);

$result_sites = $db->execute('shared_query_sites', []);

$result_languages = $db->execute('shared_query_languages', []);

$result_facilitators = $db->execute('shared_query_facilitators',[]);

//previous inputs
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

//only update class info if coming from attendance sheet (if duplicate classOffering can come from confirmed page)
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    updateSessionClassInformation();
}

include('header.php');

?>

    <script type="text/javascript" src="/js/attendance-scripts/attendance-utilities.js"></script>

    <script>
        //matrix that holds all of the associated curriculum id's class id's and topicNames
        var classesMatrix = [
            <?php
            while($row = pg_fetch_assoc($result_classes)){
                echo "[{$row['curriculumid']},\"". addslashes($row['topicname']) . "\", {$row['classid']}],";
            }
            ?>
        ];

        /**
         * sets the default class on the class information page
        */
        function setSelectedClass(){
            var classSelected = parseInt(<?php echo $selected_topic_id; ?>);

            var parent = document.getElementById('classes');

            for(var i = 0; i < parent.childElementCount; i++){
                if(parent.children[i].id === ("top-" + classSelected).toString()){
                    parent.children[i].selected = true;
                    return;
                }
            }
        }


        /**
         * populates class selection after curriculum is selected
         */
        function enableSecondSelection() {

            //disable submit button in case first class was changed
            document.getElementById("sub").disabled = true;

            //enable class selection
            document.getElementById("classSelection").disabled = false;

            /* Begin display of proper classes */

            //clear the current class selection
            var classesElement = document.getElementById("classes");

            while(classesElement.firstChild){
                classesElement.removeChild(classesElement.firstChild);
            }

            //get current input of curriculum
            var curriculumNumberSelected = $("#curr").find("option:selected").data("id");
            document.getElementById('curr-id').value = curriculumNumberSelected;

            //add new options
            var node = document.createElement("OPTION");
            node.selected = true;
            node.disabled = true;
            node.innerHTML = "Select Class";
            classesElement.appendChild(node);

            for(var i = 0; i < classesMatrix.length; i++){
                if(classesMatrix[i][0] === curriculumNumberSelected){ //same course number
                    var classNode = document.createElement("OPTION");
                    classNode.setAttribute('id', 'top-' + classesMatrix[i][2].toString());

                    var classSelected = <?php echo $selected_topic_id ?>;
                    if(classesMatrix[i][2] === classSelected){
                        classNode.setAttribute('selected', 'selected');
                    }
                    classNode.innerHTML = classesMatrix[i][1]; //name
                    classNode.dataset.id = classesMatrix[i][2]; //topic_id
                    classesElement.appendChild(classNode);
                }
            }
            /* End display of proper classes */

        }


        /**
         * appends list of times to time input selection
         *
         * @param hour{int}
         * @param minute{int}
         * @param amORpm{string}
         * @returns {Element}
         *
         */
        function createTime(hour, minute, amORpm){
            var option = document.createElement('OPTION');

            //set current set time as the time selected
            if((hour + ":" + minute + " " + amORpm) === "<?php echo $selected_time; ?>" ){
                option.setAttribute('selected','selected');
            }
            option.innerHTML = (hour + ":" + minute + " " + amORpm).toString();

            return option;
        }


        /**
         * enables the submit button
         */
        function enableSubmitButton() {
            updateClassSelection();
            document.getElementById("sub").disabled = false;
        }

        /**
         * enables the submit button by default if they
         * don't want to change any class info
         */
        function enableSubmitButtonWithoutUpdate(){
            document.getElementById("sub").disabled = false;
        }

        /**
         * grabs the id of the selected topic option and
         * sets it's topicID as value of hidden form field
         */
        function updateClassSelection(){
            document.getElementById('topic-id').value = $("#classes").find("option:selected").data("id");
        }

        /**
         * sets the default facilitator to the one logged in
         */
        function setEmployeeIdField(){
            var formFac = document.getElementById('facilitator');
            var selection = document.getElementById('facilitator-name');

            var nameSelected = selection.value;
            var employeeId;
            //match name with id
            for(var i = 0; i < selection.childElementCount; i++){
                if(selection[i].value === nameSelected){
                    employeeId = parseInt(selection[i].id);
                }
            }

            formFac.value = employeeId;
        }

    </script>
    <div class="container">
        <div class="jumbotron form-wrapper mb-3">
            <h2 class="display-4 text-center" style="font-size: 34px"> Class Information</h2>

            <form action="attendance-form" method="post">
                <div class="form-group">
                    <label for="site">Location</label>
                    <select id="site" class="form-control" name="site" onchange="">
                        <?php
                        //site options
                        while($row = pg_fetch_assoc($result_sites)){
                            //set default selected element
                            if($row['sitename'] == $selected_site)
                            {
                                echo "<option selected='selected'>{$row['sitename']}</option>";
                            } else{
                                echo "<option>{$row['sitename']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="curr">Curriculum</label>
                    <select id="curr" class="form-control" onchange="enableSecondSelection()" name="curr">
                        <option disabled selected name="classList">Select Curriculum</option>
                        <?php
                        //curriculum options
                        while($row = pg_fetch_assoc($result_curriculum)){
                            //set default selected
                            if($row['curriculumid'] == $selected_curr_id){
                                echo "<option id='cur-{$row['curriculumid']}' data-id='{$row['curriculumid']}' selected='selected'>{$row['curriculumname']}</option>";
                            } else{
                                echo "<option id='cur-{$row['curriculumid']}' data-id='{$row['curriculumid']}'>{$row['curriculumname']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <fieldset disabled="disabled" id="classSelection" >
                    <div class="form-group">
                        <label for="classes">Class</label>
                        <select id="classes" class="form-control" name="classes" onchange="enableSubmitButton()">
                            <option></option>
                        </select>
                    </div>
                </fieldset>

                <div class="form-group">
                    <label for="language">Language</label>
                    <select id="language" class="form-control" name="lang" onchange="">
                        <?php
                        //language options
                        while($row = pg_fetch_assoc($result_languages)){
                            if($row['lang'] == $selected_lang) {
                                echo "<option selected='selected'>{$row['lang']}</option>";
                            } else{
                                echo "<option>{$row['lang']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="facilitator-name">Facilitator</label>
                    <select id="facilitator-name" class="form-control" name="facilitator-name" onchange="setEmployeeIdField()">
                        <?php

                        //populate facilitator options
                        while($row = pg_fetch_assoc($result_facilitators)){

                            $facilitatorId = $row['peopleid'];

                            $fullName = ucwords($row['firstname'] . " " . $row['middleinit'] . " " . $row['lastname']);

                            //set previous facilitator selected as the default person selected
                            if($selected_facilitator == $facilitatorId){
                                echo "<option selected ='selected' id=\"{$facilitatorId}\">{$fullName}</option>";
                            }
                            else{
                                echo "<option id=\"{$facilitatorId}\">{$fullName}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="row">
                    <div class="form-group col-6">
                        <label for="date-input">Date</label>
                        <input class="form-control" type="date" value="<?php echo $selected_date; ?>" id="date-input" name = "date-input">
                    </div>

                    <div class="form-group col-6">
                        <label for="time-input">Time</label>
                        <select class="form-control" id="time-input" name = "time-input"></select>
                    </div>
                </div>

                <!-- Hidden form field values -->
                <?php echo "<input type = \"hidden\" id=\"facilitator\" name=\"facilitator\" value=\"{$selected_facilitator}\" />"  ?>
                <input type = "hidden" id="topic-id" name="topic-id" value="<?php echo $selected_topic_id ?>" />
                <input type = "hidden" id="curr-id" name="curr-id" value="<?php echo $selected_curr_id ?>" />
                <input type = "hidden" id="fromEditClassInfo" name="fromEditClassInfo" value="0" />


                <!-- Submit Button -->
                <fieldset disabled="disabled" id="sub">
                    <div class="form-footer submit">
                        <button type="submit" class="btn cpca">Change Attendance Sheet</button>
                    </div>
                </fieldset>


            </form>
        </div>
    </div>

    <script>
        window.onload = function () {
            populateTimes();
            enableSecondSelection();
            enableSubmitButtonWithoutUpdate();
            setSelectedClass();
        };
    </script>

<?php
include('footer.php');
?>