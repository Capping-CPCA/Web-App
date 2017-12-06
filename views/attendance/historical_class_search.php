<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * search for classes
 *
 * form that prompts user to enter in a date and search for all the classes on that day
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version 1.1
 * @since 0.7
 */

include('header.php');

require ('shared_queries.php');

global $db;

//get all of th curriculum and class options
$result_curriculum = $db->execute('shared_query_curriculum',[]);
$result_classes = $db->execute('shared_query_classes',[]);

//clear search criteria
unset($_SESSION['attendance-search-curr']);
unset($_SESSION['attendance-search-curr-name']);
unset($_SESSION['attendance-search-class']);
unset($_SESSION['attendance-search-class-name']);
unset($_SESSION['attendance-search-date']);
unset($_SESSION['attendance-search-query']);
unset($_SESSION['attendance-numResult']);

?>

    <script>

        /**
         * grabs the id of the selected option and sets it as value of hidden form field
         */
        function updateClassSelection(){
            document.getElementById('topic-id').value = $("#classes").find("option:selected").data("id");
        }

        //matrix that holds all of the associated curriculum id's class id's and topicNames
        var classesMatrix = [
            <?php
            while($row = pg_fetch_assoc($result_classes)){
                echo "[{$row['curriculumid']},\"". addslashes($row['topicname']) . "\", {$row['classid']}],";
            }
            ?>
        ];

        /**
         * populates class selection after curriculum is selected and
         * enables the submit button after a curriculum is selected
         */
        function enableSecondSelection() {

            //enable selection
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
                    classNode.innerHTML = classesMatrix[i][1];
                    classNode.dataset.id = classesMatrix[i][2];
                    classesElement.appendChild(classNode);
                }
            }
            /* End display of proper classes */

        }

        /**
         * ensures that at least one option was selected before submitting the form
         */
        function enableSubmit() {
            document.getElementById("submit-search").disabled = false;
        }
    </script>

    <div class="container">
        <div class="card" style="max-width: 700px; width: 100%; margin: 0 auto">
            <div class="card-body">
                <div style="text-align: center">
                    <h4 class="card-title">Attendance Search</h4>
                    <p class="card-text">Please select one or more search criteria.</p>
                </div>
                <hr style="margin-top: 0px!important;">
                <form action="historical-class-search-results" method="post">
                    <div class="form-group">
                        <label for="date-input">Class Date</label>
                        <input class="form-control" style="width: 264.7px;" type="date" id="date-input" name="date-input" onchange="enableSubmit()">
                    </div>

                    <div class="form-group">
                        <label for="curr">Curriculum</label>
                        <select id="curr" class="form-control" onchange="enableSecondSelection(); enableSubmit()" name="curr">
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
                            <select id="classes" class="form-control" name="classes" onchange="updateClassSelection();">
                                <option></option>
                            </select>
                        </div>
                    </fieldset>

                    <!-- Hidden Form -->
                    <input type = "hidden" id="topic-id" name="topic-id" value="" />
                    <input type = "hidden" id="curr-id" name="curr-id" value="" />

                    <div class="form-footer submit">
                        <fieldset disabled="disabled" id="submit-search">
                            <button type="submit" class="btn cpca" >Find Attendance Records</button>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
include('footer.php');
?>