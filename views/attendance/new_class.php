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
 * @version [version number]
 * @since [initial version number]
 */

authorizedPage();

global $db;

//unset previous class session information
if(isset($_SESSION['serializedInfo'])) {
    unset($_SESSION['serializedInfo']);
}

if(isset($_SESSION['attendance-info'])) {
    unset($_SESSION['attendance-info']);
}

$result_curriculum = $db->no_param_query("SELECT c.curriculumid, c.curriculumname FROM curricula c ORDER BY c.curriculumname ASC;");

$result_classes = $db->no_param_query("SELECT cc.curriculumid, topicname, cc.classid FROM curriculumclasses cc, classes WHERE classes.classid = cc.classid ORDER BY cc.curriculumid;");

$result_sites = $db->no_param_query("SELECT s.sitename FROM sites s;");

$result_languages = $db->no_param_query("SELECT * FROM languages;");

$result_facilitators = $db->no_param_query("SELECT peop.firstname, peop.middleinit, peop.lastname, peop.peopleid " .
                                            "FROM people peop, employees emp, facilitators f " .
                                            "WHERE peop.peopleid = emp.employeeid " .
                                            "AND emp.employeeid = f.facilitatorid " .
                                            "AND f.df = 0 " .
                                            "ORDER BY peop.lastname ASC;"
);

include('header.php');

?>

<script>
    //js for holding all the class choices
    var classesMatrix = [
        <?php
            while($row = pg_fetch_assoc($result_classes)){
                echo "[{$row['curriculumid']},\"". addslashes($row['topicname']) . "\", {$row['classid']}],";
            }
        ?>
        ];

    //js for controlling the disabled selection of class section
    function enableSecondSelection() {

        //disable submit button in case first class was changed
        document.getElementById("sub").disabled = true;

        //enable selection
        document.getElementById("classSelection").disabled = false;

        /* Display the proper classes */

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
                classNode.innerHTML = classesMatrix[i][1];
                classNode.dataset.id = classesMatrix[i][2];
                classesElement.appendChild(classNode);
            }
        }

    }

    function populateTimes() {
        var selectTime = document.getElementById('time-input');

        var optionArray = [];

        //AM
        for(var i = 0; i < 12; i++){
            var hourAM;
            if(i === 0){
                hourAM = 12;
            } else{
                hourAM = i;
            }
            optionArray.push(createTime(hourAM, "00", "AM"))
            optionArray.push(createTime(hourAM, "30", "AM"))
        }

        //PM
        for(var j = 0; j < 12; j++){
            var hourPM;
            if(j === 0){
                hourPM = 12;
            } else{
                hourPM = j;
            }
            optionArray.push(createTime(hourPM, "00", "PM"))
            optionArray.push(createTime(hourPM, "30", "PM"))
        }

        for(var k = 0; k < optionArray.length; k++){
            selectTime.appendChild(optionArray[k]);
        }
    }

    //input: hour and time integers
    //output: option object
    function createTime(hour, minute, amORpm){
        var option = document.createElement('OPTION');
        option.innerHTML = (hour + ":" + minute + " " + amORpm).toString();

        return option;
    }

    //input: none
    //output: none
    //description: enables the submit button
    function enableSubmitButton() {
        updateClassSelection();
        document.getElementById("sub").disabled = false;
    }

    //grabs the id of the selected option and sets it as value of hidden form field
    function updateClassSelection(){
        document.getElementById('topic-id').value = $("#classes").find("option:selected").data("id");
    }

    //input: none
    //output: none
    //description: sets the default facilitator to the one logged in
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
        <h2 class="display-4 text-center" style="font-size: 34px">Class Information</h2>

        <form action="attendance-form" method="post">
            <div class="form-group">
                <label for="site">Location</label>
                <select id="site" class="form-control" name="site" onchange="">
                    <?php
                    //site options
                    while($row = pg_fetch_assoc($result_sites)){
                        echo "<option>{$row['sitename']}</option>";
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
                            echo "<option id='cur-{$row['curriculumid']}' data-id='{$row['curriculumid']}'>{$row['curriculumname']}</option>";
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
                        echo "<option>{$row['lang']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="facilitator-name">Facilitator</label>
                <select id="facilitator-name" class="form-control" name="facilitator-name" onchange="setEmployeeIdField()">
                    <?php
                    $counter = 0;
                    $first = true;
                    $defaultValueFacilitatorId = 0;
                    while($row = pg_fetch_assoc($result_facilitators)){
                        if($first){$first = false; $defaultValueFacilitatorId = $row['peopleid'];}
                        $selected = "";
                        //choose default facilitator
                        if((isset($_SESSION['employeeid'])) && ($_SESSION['employeeid'] == $row['peopleid'])){
                            $selected = "selected=\"selected\"";
                            $defaultValueFacilitatorId = $row['peopleid'];
                        }

                        $facilitatorId = $row['peopleid'];

                        $fullName = $row['firstname'] . " " . $row['middleinit'] . " " . $row['lastname'];
                        echo "<option {$selected} id=\"{$facilitatorId}\">{$fullName}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="row">
                <div class="form-group col-6">
                    <label for="date-input">Date</label>
                    <input class="form-control" type="date" value="<?php echo date('Y-m-d'); ?>" id="date-input" name = "date-input">
                </div>

                <div class="form-group col-6">
                    <label for="time-input">Time</label>
                    <select class="form-control" id="time-input" name = "time-input"></select>
                </div>
            </div>

            <?php echo "<input type = \"hidden\" id=\"facilitator\" name=\"facilitator\" value=\"{$defaultValueFacilitatorId}\" />"  ?>
            <input type = "hidden" id="topic-id" name="topic-id" value="" />
            <input type = "hidden" id="curr-id" name="curr-id" value="" />

            <fieldset disabled="disabled" id="sub">
                <div class="form-footer submit">
                    <button type="submit" class="btn cpca">Create Attendance Sheet</button>
                </div>
            </fieldset>


        </form>
    </div>
</div>

<script>
    window.onload = function () {
        populateTimes();
    };
</script>

<?php
include('footer.php');
?>