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
 * @version [version number]
 * @since [initial version number]
 */

authorizedPage();

global $db;
include('header.php');

$peopleid = $_SESSION['employeeid'];

//TODO: add option to edit last class' attendance
$dashboardClassesQuery =
"select classes.topicname, fca.date, co.sitename " .
"from facilitatorclassattendance fca, classoffering co, curriculumclasses, classes " .
"where fca.classid = co.classid " .
      "and fca.curriculumid = co.curriculumid " .
      "and fca.sitename = co.sitename " .
      "and fca.date = co.date " .
      "and fca.facilitatorid = {$peopleid} " .
      "and co.classid = curriculumclasses.classid " .
      "and co.curriculumid = curriculumclasses.curriculumid " .
      "and curriculumclasses.classid = classes.classid " .
"order by fca.date desc limit 20; ";

$result = $db->no_param_query($dashboardClassesQuery);

?>

    <script src="/js/attendance-scripts/historical-class-view.js"></script>

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
                            $counter = 0; //for hidden form

                            //populate table with historical class information
                            while($row = pg_fetch_assoc($result)) {
                                echo "<tr id='{$counter}'>";
                                    echo "<td>{$row['sitename']}</td>";
                                    echo "<td>{$row['topicname']}</td>";
                                    $time = strtotime($row['date']);
                                    $myFormatDate = date("m/d/y", $time);
                                    $myFormatTime = date("h:i A", $time);
                                    echo "<td>{$myFormatDate} <em>{$myFormatTime}</em></td>";
                                    echo "<td><button href=\"\" class=\"btn outline-cpca\" type=\"submit\" onclick=\"changeHiddenFormFieldValue({$counter})\">More details...</button></td>";
                                echo "</tr>";
                                $counter++;
                            }
                            ?>
                            </tbody>
                        </table>

                        <!-- The hidden field -->
                        <input type="hidden" id="whichButton" name="whichButton" value="" />
                    </form>
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

        <div class="text-center" style="margin-bottom: 20px; margin-top: 15px;">
            <a href="/new-class"><button type="button" class="btn cpca">Record Attendance For New Class</button></a>
            <a href="/historical-class-search"><button type="button" class="btn btn-outline-secondary">Search For Historical Attendance</button></a>
        </div>

    </div>

    <script src="/js/attendance-scripts/dashboard-pagination.js"></script>

<?php
include('footer.php');
?>