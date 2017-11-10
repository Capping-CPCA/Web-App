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
require ('attendance_utilities.php');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: /historical-class-search");
    die();
} else {
    $classDate = $_POST['date-input'];

    //TODO: add option to edit last class' attendance
    $query =
        "select classes.topicname, fca.date, co.sitename, cu.curriculumname, peop.firstname, peop.lastname " .
        "from facilitatorclassattendance fca, classoffering co, curricula cu, facilitators fac, employees emp, people peop, classes, curriculumclasses cc " .
        "where fca.classid = co.classid " .
        "  and fca.curriculumid = co.curriculumid " .
        "  and fca.date = co.date " .
        "  and fca.sitename = co.sitename " .
        "  and co.curriculumid = cc.curriculumid " .
        "  and co.classid = cc.classid " .
        "  and cc.curriculumid = cu.curriculumid " .
        "  and cc.classid = classes.classid " .
        "  and fca.facilitatorid = fac.facilitatorid " .
        "  and fac.facilitatorid = emp.employeeid " .
        "  and emp.employeeid = peop.peopleid " .
        "  and to_char(co.date, 'YYYY-MM-DD') = '{$classDate}';";

    $result = $db->no_param_query($query);
    $attendanceResults = pg_fetch_all($result);
}
include('header.php');
?>

    <script src="/js/attendance-scripts/historical-class-view.js"></script>

    <div class="container">

        <div class="card">

            <div class="card-body">
                <?php
                $convert_date = DateTime::createFromFormat('Y-m-d', $classDate);
                $formatted_date = $convert_date->format('l, F jS');
                ?>

                <?php if ($attendanceResults) { ?>
                <h4 class="card-title" style="margin-top: 15px;">Classes On <?= $formatted_date ?></h4>
                <?php } ?>
                <div class="table-responsive">
                    <form action = "historical-class-view" method="post" name="classView">
                        <table class="table table-striped">
                            <?php if ($attendanceResults) { ?>
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
                                foreach ($attendanceResults as $result) {
                                    echo "<tr>";
                                    echo "<td>{$result['firstname']} {$result['lastname']}</td>";
                                    echo "<td>{$result['curriculumname']}</td>";
                                    echo "<td>{$result['topicname']}</td>";
                                    echo "<td>{$result['sitename']}</td>";
                                    $time = strtotime($result['date']);
                                    $myFormatTime = date("h:i A", $time);
                                    echo "<td><em>{$myFormatTime}</em></td>";
                                    echo "<td><button href=\"#\" class=\"btn btn-outline-secondary\" type=\"submit\" onclick=\"changeHiddenFormFieldValue({$counter})\">More details...</button></td>";
                                    echo "</tr>";

                                    $counter++;
                                }
                            } else { ?>
                                <div class="w-100 d-flex flex-column justify-content-center text-center">
                                    <h3 class="display-3 text-secondary" style="font-size: 40px;"><i
                                                class="fa fa-exclamation-circle"></i></h3>
                                    <h4 class="display-3 text-secondary" style="font-size: 30px;">No Attendance Records Found for <?= $formatted_date ?>.</h4>
                                </div>
                            <?php } ?>
                            </tbody>
                        </table>

                        <!-- The hidden field -->
                        <input type="hidden" id="whichButton" name="whichButtonHistoricalSearch" value="" />
                        <input type="hidden" id="input-date" name="input-date" value="<?= $classDate ?>" />
                    </form>
                </div>
            </div>

            <div class="text-center" style="margin-bottom: 20px; margin-top: 15px;">
                <a href="/record-attendance"><button type="button" class="btn cpca">Return To Attendance Dashboard</button></a>
                <a href="/historical-class-search"><button type="button" class="btn btn-outline-secondary">Return To Date Selection</button></a>
            </div>
        </div>

    </div>
<?php
include('footer.php');
?>