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
$result = $db->no_param_query("select fca.topicname, fca.date, co.sitename " .
"from facilitatorclassattendance fca, classoffering co " .
"where fca.topicname = co.topicname " .
"and fca.sitename = co.sitename " .
"and fca.date = co.date " .
"and fca.facilitatorid = {$peopleid} " .
"order by fca.date desc " .
"limit 20; ");


?>

    <script src="/js/attendance-scripts/historical-class-view.js"></script>

    <div class="container col-12">


        <div class="row col-12">

            <div class="card col-12">

                <div class="card-block">

                    <h4 class="card-title" style="margin-top: 15px;">My Recent Classes</h4>

                    <div class="table-responsive">
                        <form action = "historical-class-view" method="post" name="classView">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Site Name</th>
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
                                        echo "<td>{$row['topicname']}</td>";
                                        echo "<td>{$row['sitename']}</td>";
                                        $time = strtotime($row['date']);
                                        $myFormatDate = date("m/d/y", $time);
                                        $myFormatTime = date("h:i A", $time);
                                        echo "<td>{$myFormatDate} <em>{$myFormatTime}</em></td>";
                                        echo "<td><button href=\"\" class=\"btn btn-link\" type=\"submit\" onclick=\"changeHiddenFormFieldValue({$counter})\">More details...</button></td>";
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
                                    <button type="button" name="btn-pag" id="btn-pag-1" class="btn btn-primary" onclick="selectButton(1)">1</button>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <div class="row flex-row" style="margin-bottom: 20px">
            <button type="button" class="btn btn-default col-12" style="margin-top: 15px" onclick="window.location.href='./new-class'">Record Attendance For New Class</button>
            <button type="button" class="btn btn-info col-12" style="margin-top: 15px" onclick="window.location.href='./historical-class-search'">Search For Historical Attendance</button>
        </div>

    </div>

    <script src="/js/attendance-scripts/dashboard-pagination.js"></script>

<?php
include('footer.php');
?>