<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * view attendance details for a class
 *
 * view class attendance information for any class via day or number on list
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version 1.1
 * @since 0.7
 */

global $db;

require("attendance_utilities.php");

include('header.php');


if(!isset($_POST['recentClass'])){ //shouldn't be here
    header("Location: /attendance");
    die();
}

unset($_SESSION['serializedInfo']);
$peopleid = $_SESSION['employeeid'];

$classN = $_POST['recentClass'];

//shared information we're grabbing
$queryClass = "select classes.topicname, fca.date, co.sitename, peop.firstname, peop.middleinit, peop.lastname, cur.curriculumname " .
    "from facilitatorclassattendance fca, classoffering co, " .
    "facilitators fac, employees emp, people peop, curriculumclasses cc, curricula cur, classes " .
    "where fca.sitename = co.sitename " .
    "and fca.date = co.date " .
    "and fca.facilitatorid = fac.facilitatorid " .
    "and fac.facilitatorid = emp.employeeid " .
    "and emp.employeeid = peop.peopleid " .
    "and co.curriculumid = cc.curriculumid " .
    "and co.classid = cc.classid " .
    "and classes.classid = cc.classid ".
    "and cc.curriculumid = cur.curriculumid " .
    "and fca.facilitatorid = {$peopleid} " .
    "order by fca.date desc " .
    "limit 20; ";



//fetch the list of classes from the dashboard query
$resultClassInfo = $db->no_param_query($queryClass);

//loop through to desired result
for($i = 0; $i < $classN; $i++){
    pg_fetch_assoc($resultClassInfo);
}
$row = pg_fetch_assoc($resultClassInfo); //actual row we want

$class_topic = $row['topicname'];
$class_curriculum = $row['curriculumname'];
$site_name = $row['sitename'];
$class_timestamp = $row['date'];
$facilitator_name = $row['firstname'] . " " . $row['middleinit'] . " " . $row['lastname'];
$displayDate = formatSQLDate($class_timestamp);

//queries the classattendancedetails view to get all information for that class
$queryClassInformation = "select * " .
    "from classattendancedetails " .
    "where topicname = '" . escape_apostrophe($class_topic) ."' " .
    "and sitename = '" . escape_apostrophe($site_name) ."' " .
    "and date = '{$class_timestamp}' " .
    "order by lastname asc;";

$result = $db->no_param_query($queryClassInformation);

// Format date for query
$classDate = new DateTime($class_timestamp);
$date = $classDate->format('Y-m-d');
$time = $classDate->format('g:i A');
$dateTime = $classDate->format('Y-m-d H:i:s');

// Format attendance info array for editing class
$r = $db->query("SELECT * FROM facilitatorclassattendance WHERE date = $1 AND sitename = $2", [$dateTime,  escape_apostrophe($site_name)]);
$facilitator = pg_fetch_assoc($r);
$r = $db->query("SELECT * FROM classoffering WHERE date = $1 AND sitename = $2", [$dateTime, escape_apostrophe($site_name)]);
$classOffering = pg_fetch_assoc($r);

$attendanceInfo = [
    'site' => $site_name,
    'curr' => $class_curriculum,
    'classes' => $class_topic,
    'lang' => $classOffering['lang'],
    'facilitator-name' => $facilitator_name,
    'date-input' => $date,
    'time-input' => $time,
    'facilitator' => $facilitator['facilitatorid'],
    'topic-id' => $classOffering['classid'],
    'curr-id' => $classOffering['curriculumid'],
    'edit' => true
];
$_SESSION['editClass'] = true;
$_SESSION['attendance-info'] = $attendanceInfo;

?>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title" style="margin-top: 10px; margin-left: 10px; text-align: center;">Attendance: <?php echo $displayDate; ?> </h4>
                <h6 style="text-align: center;"><?php echo $class_curriculum . " : " . $class_topic?></h6>
                <h6 style="text-align: center;"><i><?php echo "Facilitator : " . $facilitator_name?></i></h6>
                <h6 style="text-align: center;"><i><?php echo "Site : " . $site_name?></i></h6>
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Zip</th>
                            <th>Number of </br>children under 18</th>
                            <th>Comments</th>
                            <th>New?</th>
                            <th>Race</th>
                            <th>Sex</th>
                            <th>DOB</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        while($row = pg_fetch_assoc($result)) {
                            echo "<tr class=\"m-0\">";
                            echo "<td>{$row['firstname']} {$row['middleinit']} {$row['lastname']}</td>";
                            $dob = $row['dateofbirth'];
                            $age = calculate_age($dob);
                            echo "<td>{$age}</td>";
                            echo "<td>{$row['zipcode']}</td>";
                            echo "<td>{$row['numchildren']}</td>";
                            echo "<td>{$row['comments']}</td>";
                            //convert boolean to string
                            $tf = ($row['isnew'] == 't') ? $tf = "Yes" : $tf = "No";
                            echo "<td>{$tf}</td>";
                            echo "<td>{$row['race']}</td>";
                            echo "<td>{$row['sex']}</td>";
                            echo "<td>{$dob}</td>";

                            echo "</tr>";
                        }

                        ?>
                        </tbody>
                    </table>
                    <!-- /Table -->
                </div>
            </div>
        </div>

        <div class="text-center" style="margin-bottom: 20px; margin-top: 15px;">
            <a href='/edit-class-info' class='mr-2'><button type="button" class="btn btn-outline-secondary" style="margin-right: .5rem;">Edit Class Attendance</button></a>
            <a href='/attendance'><button type="button" class="btn btn-outline-secondary">Back To Dashboard</button></a>
        </div>

    </div>

<?php
include('footer.php');
?>