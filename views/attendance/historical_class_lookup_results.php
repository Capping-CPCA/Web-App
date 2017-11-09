<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * result page for historical search
 *
 * displays a list of all classes on the selected date with an option to view them
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version [version number]
 * @since [initial version number]
 */

authorizedPage();

global $db;
include('header.php');
require ('attendance_utilities.php');

$classDate = $_POST['date-input'];

//TODO: add option to edit last class' attendance
$query = "select fca.topicname, fca.date, co.sitename, cu.curriculumname, peop.firstname, peop.lastname " .
"from facilitatorclassattendance fca, classoffering co, curricula cu, " .
  "facilitators fac, employees emp, people peop " .
"where fca.topicname = co.topicname " .
"and fca.sitename = co.sitename " .
"and fca.date = co.date " .
"and co.curriculumid = cu.curriculumid " .
"and fca.facilitatorid = fac.facilitatorid " .
"and fac.facilitatorid = emp.employeeid " .
"and emp.employeeid = peop.peopleid " .
"and to_char(co.date, 'YYYY-MM-DD') = '{$classDate}';";

$result = $db->no_param_query($query);


?>

    <script src="/js/attendance-scripts/historical-class-view.js"></script>

    <div class="container col-12">
        <div class="row col-12">

            <div class="card col-12">

                <div class="card-block">
                    <?php
                    $convert_date = DateTime::createFromFormat('Y-m-d', $classDate);
                    $formatted_date = $convert_date->format('l, F jS');
                    ?>

                    <h4 class="card-title" style="margin-top: 15px;">Classes On <?php echo $formatted_date; ?></h4>

                    <div class="table-responsive">
                        <form action = "historical-class-view" method="post" name="classView">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Facilitator Name</th>
                                    <th>Curriculum</th>
                                    <th>Class</th>
                                    <th>Site Name</th>
                                    <th>Date/Time</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $counter = 0;
                                while($row = pg_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>{$row['firstname']} {$row['lastname']}</td>";
                                    echo "<td>{$row['curriculumname']}</td>";
                                    echo "<td>{$row['topicname']}</td>";
                                    echo "<td>{$row['sitename']}</td>";
                                    $time = strtotime($row['date']);
                                    $myFormatTime = date("h:i A", $time);
                                    echo "<td><em>{$myFormatTime}</em></td>";
                                    echo "<td><button href=\"#\" class=\"btn btn-link\" type=\"submit\" onclick=\"changeHiddenFormFieldValue({$counter})\">More details...</button></td>";
                                    echo "</tr>";
                                    $counter++;
                                }
                                ?>
                                </tbody>
                            </table>

                            <!-- The hidden field -->
                            <input type="hidden" id="whichButton" name="whichButtonHistoricalSearch" value="" />
                            <input type="hidden" id="input-date" name="input-date" value="<?php echo $classDate; ?>" />
                        </form>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-secondary col-12" style="margin-top: 15px" onclick="window.location.href='./record-attendance'">Return To Attendance Dashboard</button>
            <button type="button" class="btn btn-info col-12" style="margin-top: 15px" onclick="window.location.href='./historical-class-search'">Return To Date Selection</button>
        </div>

    </div>
<?php
include('footer.php');
?>