<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * view attendance details for a class
 *
 * view class attendance information for any day, curriculum, and/or class
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version 1.1
 * @since 1.1
 */

global $db;

require("attendance_utilities.php");


$peopleid = $_SESSION['employeeid'];

$classN = null;

$queryClass = null;

//shouldn't be on this page
if(!isset($_SESSION['attendance-search-query'])){
    echo "<h1>Please use 'recent classes' or the 'historical attendance lookup tool' to access attendance history.</h1>";
    die();
}
else{
    $queryClass = $_SESSION['attendance-search-query'];
    //grab the specific class we clicked
    $resultClassInfo = $db->no_param_query($queryClass);

    //get what button we clicked
    if(isset($_POST['numResult'])){
        $classN = $_POST['numResult'];
        $_SESSION['attendance-numResult'] = $classN;
    }
    else if(isset($_SESSION['attendance-numResult'])){
        $classN = $_SESSION['attendance-numResult'];
    }
    else{
        //shouldn't be here
        die();
    }

}
//loop through to desired result
for($i = 0; $i < $classN; $i++){
    pg_fetch_assoc($resultClassInfo);
}

//actual row we want
$row = pg_fetch_assoc($resultClassInfo);

//make connection
$class_topic = $row['topicname'];
$class_curriculum = $row['curriculumname'];
$site_name = $row['sitename'];
$class_date = $row['date'];
$mi = isset($row['middleinit']) ? $row['middleinit'] : "";
$facilitator_name = $row['firstname'] . " " . $mi . " " . $row['lastname'];
$displayDate = formatSQLDate($class_date);

$queryClassInformation = "select * " .
    "from classattendancedetails " .
    "where topicname = '" . escape_apostrophe($class_topic) ."' " .
    "and sitename = '" . escape_apostrophe($site_name) ."' " .
    "and date = '{$class_date}' " .
    "order by lastname asc;";

$result = $db->no_param_query($queryClassInformation);


include('header.php');

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
            //historicalLookup
            echo "<a href='/historical-class-search' class='mr-2'><button type=\"button\" class=\"btn cpca\">New Search</button></a>";
            echo "<a href='/attendance'><button type=\"button\" class=\"btn btn-outline-secondary\">Back To Dashboard</button></a>";
            ?>
        </div>

    </div>

<?php
include('footer.php');
?>