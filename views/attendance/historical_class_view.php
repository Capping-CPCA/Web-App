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
 * @version [version number]
 * @since [initial version number]
 */

global $db;

require("attendance_utilities.php");

include('header.php');

$peopleid = $_SESSION['employeeid'];

$classN = null;
$whatPageWeCameFrom = null;

//shared information we're grabbing
$queryClass = "select classes.topicname, fca.date, co.sitename, peop.firstname, peop.middleinit, peop.lastname, cur.curriculumname " .
    "from facilitatorclassattendance fca, classoffering co, " .
    "facilitators fac, employees emp, people peop, curriculumclasses cc, curricula cur, classes " .
    "where fca.classid = co.classid " .
    "and fca.curriculumid = co.curriculumid " .
    "and fca.sitename = co.sitename " .
    "and fca.date = co.date " .
    "and fca.facilitatorid = fac.facilitatorid " .
    "and fac.facilitatorid = emp.employeeid " .
    "and emp.employeeid = peop.peopleid " .
    "and co.curriculumid = cc.curriculumid " .
    "and co.classid = cc.classid " .
    "and classes.classid = cc.classid ".
    "and cc.curriculumid = cur.curriculumid ";

//additional parameters - check to see where the post came from
if(isset ($_POST["whichButton"])) { //from attendance dashboard
    $whatPageWeCameFrom = "dashboard";
    $classN = $_POST["whichButton"];

    $queryClass .= "and fca.facilitatorid = {$peopleid} " .
        "order by fca.date desc " .
        "limit 20; ";
}
else if(isset($_POST["whichButtonHistoricalSearch"])){ //form historical search tool
    $whatPageWeCameFrom = "historicalLookup";
    $classN = $_POST["whichButtonHistoricalSearch"];
    $classListDate = $_POST["input-date"];

    $queryClass .= "and to_char(co.date, 'YYYY-MM-DD') = '{$classListDate}';";
}
else{ //shouldn't be here
    echo "<h1>Please use 'recent classes' or the 'historical attendance lookup tool' to access attendance history.</h1>";
    die(); //on the Whirly Dirly
}

//grab the specific class we clicked
$resultClassInfo = $db->no_param_query($queryClass);

//loop through to desired result
for($i = 0; $i < $classN; $i++){
    pg_fetch_assoc($resultClassInfo);
}
$row = pg_fetch_assoc($resultClassInfo); //actual row we want

$class_topic = $row['topicname'];
$class_curriculum = $row['curriculumname'];
$site_name = $row['sitename'];
$class_date = $row['date'];
$facilitator_name = $row['firstname'] . " " . $row['middleinit'] . " " . $row['lastname'];
$displayDate = formatSQLDate($class_date);

$queryClassInformation = "select * " .
    "from classattendancedetails " .
    "where topicname = '" . escape_apostrophe($class_topic) ."' " .
    "and sitename = '" . escape_apostrophe($site_name) ."' " .
    "and date = '{$class_date}' " .
    "order by lastname asc;";

$result = $db->no_param_query($queryClassInformation);




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
                            $tf = ($row['isnew'] == 't') ? $tf = "yes" : $tf = "no";
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
            <?php
            //back to previous page depends on whether or not came from historical class lookup or dashboard
            if($whatPageWeCameFrom == "dashboard"){
                echo "<a href='/attendance'><button type=\"button\" class=\"btn btn-outline-secondary\">Back To Dashboard</button></a>";
            } else { //historicalLookup
                echo "<a href='/historical-class-search' class='mr-2'><button type=\"button\" class=\"btn cpca\">Search New Day</button></a>";
                echo "<a href='/attendance'><button type=\"button\" class=\"btn btn-outline-secondary\">Back To Dashboard</button></a>";
            }
            ?>
        </div>

    </div>

<?php
include('footer.php');
?>