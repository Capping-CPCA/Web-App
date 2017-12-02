<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Landing page for record attendance.
 *
 * Shows the last 20 classes and options to record new attendance or look
 * at historical attendance records.
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version 1.1
 * @since 0.7
 */

global $db;

$peopleid = $_SESSION['employeeid'];

$dashboardClassesQuery =
    "select classes.topicname, fca.date, co.sitename " .
    "from facilitatorclassattendance fca, classoffering co, curriculumclasses, classes " .
    "where fca.sitename = co.sitename " .
    "and fca.date = co.date " .
    "and fca.facilitatorid = {$peopleid} " .
    "and co.classid = curriculumclasses.classid " .
    "and co.curriculumid = curriculumclasses.curriculumid " .
    "and curriculumclasses.classid = classes.classid " .
    "order by fca.date desc limit 20; ";

$result = $db->no_param_query($dashboardClassesQuery);

include('header.php');

?>

    <script>
        /* Attendance Historical Class View */
        /**
         * @param buttonNumber{int}
         */
        function changeHiddenFormFieldValue(buttonNumber) {
            document.getElementById("whichButton").value = buttonNumber;
        }
    </script>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title" style="margin-top: 15px;">My Recent Classes</h4>
                <div class="table-responsive">
                    <form action = "historical-class-view" method="post" name="classView">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Site Name</th>
                                <th>Class</th>
                                <th>Date/Time</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="result-pag">
                            <?php

                            //used to identify rows of class
                            $counter = 0;

                            //populate table with list of recent classes
                            while($row = pg_fetch_assoc($result)) {
                                echo "<tr id='{$counter}'>";
                                echo "<td class='align-middle'>{$row['sitename']}</td>";
                                echo "<td class='align-middle'>{$row['topicname']}</td>";

                                //convert sql timestamp to human readable date and time
                                $time = strtotime($row['date']);
                                $myFormatDate = date("m/d/y", $time);
                                $myFormatTime = date("h:i A", $time);

                                echo "<td class='align-middle'>{$myFormatDate} <em>{$myFormatTime}</em></td>";
                                echo "<td class='align-middle'><button href=\"\" class=\"btn outline-cpca\" type=\"submit\" onclick=\"changeHiddenFormFieldValue({$counter})\">More details...</button></td>";
                                echo "</tr>";
                                $counter++;
                            }
                            ?>
                            </tbody>
                        </table>

                        <!-- The hidden field responsible for submitting what recent class was clicked -->
                        <input type="hidden" id="whichButton" name="whichButton" value="" />
                    </form>

                    <!-- Pagination buttons -->
                    <div class="d-flex justify-content-center">
                        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                            <div class="btn-group mr-2" role="group" aria-label="First group" id="button-nav" style="margin-bottom: 10px;">
                                <button type="button" name="btn-pag" id="btn-pag-1" class="btn btn-dark" onclick="selectButton(1)">1</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Links to attendance functions -->
        <div class="text-center" style="margin-bottom: 20px; margin-top: 15px;">
            <a href="/new-class"><button type="button" class="btn cpca">Record Attendance For New Class</button></a>
            <a href="/historical-class-search"><button type="button" class="btn btn-outline-secondary">Search For Historical Attendance</button></a>
        </div>

    </div>

    <script src="/js/attendance-scripts/dashboard-pagination.js"></script>

<?php
include('footer.php');
?>