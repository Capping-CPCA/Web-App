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

global $db;

$result_curriculum = $db->no_param_query("SELECT c.curriculumid, c.curriculumname FROM curricula c WHERE c.df IS FALSE ORDER BY c.curriculumname ASC;");

$result_classes = $db->no_param_query("SELECT cc.curriculumid, topicname, cc.classid FROM curriculumclasses cc, classes WHERE classes.classid = cc.classid AND classes.df IS FALSE ORDER BY cc.curriculumid;");

require ('attendance_utilities.php');

$query = "";

$selected_class = $selected_curr = $selected_date = $selected_curr_name = $selected_class_name = null;

//query already set from previous session
if(isset($_SESSION['attendance-search-query'])){
    //set all the required fields
    $query = $_SESSION['attendance-search-query'];
    if(isset($_SESSION['attendance-search-class'])) $selected_class = $_SESSION['attendance-search-class'];
    if(isset($_SESSION['attendance-search-class-name'])) $selected_class_name = $_SESSION['attendance-search-class-name'];
    if(isset($_SESSION['attendance-search-curr'])) $selected_curr = $_SESSION['attendance-search-curr'];
    if(isset($_SESSION['attendance-search-curr-name'])) $selected_curr_name = $_SESSION['attendance-search-curr-name'];
    if(isset($_SESSION['attendance-search-date'])) $selected_date = $_SESSION['attendance-search-date'];

}
// Get attendance info from POST
else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(!empty($_POST['curr-id'])){
        $selected_curr = $_POST['curr-id'];
        $selected_curr_name = $_POST['curr'];

        //also check if a class is selected
        if(!empty($_POST['topic-id'])){
            $selected_class = $_POST['topic-id'];
            $selected_class_name = $_POST['classes'];
        }
    }

    //if not set
    if(!empty($_POST['date-input'])) {
        $selected_date = $_POST['date-input'];
    }

    $_SESSION['attendance-search-curr'] = $selected_curr;
    $_SESSION['attendance-search-curr-name'] = $selected_curr_name;
    $_SESSION['attendance-search-class'] = $selected_class;
    $_SESSION['attendance-search-class-name'] = $selected_class_name;
    $_SESSION['attendance-search-date'] = $selected_date;
    //1. Validate Post Fields - die if not valid
    {
        if(!is_null($selected_curr)){
            if(!validateCurriculum($result_curriculum, $selected_curr)) die();
        }
        if(!is_null($selected_class)){
            if(!validateClass($result_classes, $selected_class)) die();
        }
        if(!is_null($selected_date)){
            if(!validateDate($selected_date)) die();
        }
    }

    //2. Build Result Query Based On Fields

    //base query
    $query =
        "select classes.topicname, fca.date, co.sitename, cu.curriculumname, peop.firstname, peop.lastname " .
        "from facilitatorclassattendance fca, classoffering co, curricula cu, facilitators fac, employees emp, people peop, classes, curriculumclasses cc " .
        "where fca.date = co.date " .
        "  and fca.sitename = co.sitename " .
        "  and co.curriculumid = cc.curriculumid " .
        "  and co.classid = cc.classid " .
        "  and cc.curriculumid = cu.curriculumid " .
        "  and cc.classid = classes.classid " .
        "  and fca.facilitatorid = fac.facilitatorid " .
        "  and fac.facilitatorid = emp.employeeid " .
        "  and emp.employeeid = peop.peopleid ";

    if(!is_null($selected_curr)){
        $query .= " and cu.curriculumid = {$selected_curr} ";
    }
    if(!is_null($selected_class)){
        $query .= " and co.classid = {$selected_class} " ;
    }
    if(!is_null($selected_date)){
        $query .= " and to_char(co.date, 'YYYY-MM-DD') = '{$selected_date}' ";
    }

    //limit search to most recent 100
    $query .= " ORDER BY fca.date desc limit 100;" ;

    //3. Set Session Variable With This Query
    $_SESSION['attendance-search-query'] = $query;
}
else {
    header("Location: /historical-class-search");
    die();
}

$result = $db->no_param_query($query);
$attendanceResults = pg_fetch_all($result);
include('header.php');
?>

    <script src="/js/attendance-scripts/historical-class-view.js"></script>


    <script>
        /* Attendance Historical Class View */
        function changeHiddenFormFieldValue(buttonNumber) {
            document.getElementById("numResult").value = buttonNumber;
        }
    </script>

    <div class="container">

        <div class="card">

            <div class="card-body">

                <?php
                $formatted_date = null;
                if(!empty($selected_date)){
                    $convert_date = DateTime::createFromFormat('Y-m-d', $selected_date);
                    $formatted_date = $convert_date->format('l, F jS');
                }

                if($attendanceResults){
                    echo "<div style='text-align: center'>";
                    echo "<h4 class=\"card-title\">Results Found</h4>";
                    echo "<em>";
                    if(!is_null($formatted_date)){
                        echo "<p class='card-text'>{$formatted_date}</p>";
                    }
                    //selected curriculum
                    if(!is_null($selected_curr)){
                        echo "<p class='card-text'>Curriculum : {$selected_curr_name}</p>";
                    }
                    //selected class
                    if(!is_null($selected_class)){
                        echo "<p class='card-text'>Class : {$selected_class_name}</p>";
                    }
                    echo "</em>";
                    echo "</div>";
                }

                ?>

                <div class="table-responsive">
                    <form action = "historical-class-search-view" method="post" name="classView">
                        <table class="table table-striped">
                            <?php if ($attendanceResults) { ?>
                            <thead>
                            <tr>
                                <th>Facilitator Name</th>
                                <th>Curriculum</th>
                                <th>Class</th>
                                <th>Site Name</th>
                                <?php
                                //if date was a parameter, don't show date in column header
                                if(!is_null($formatted_date)){
                                    echo "<th>Time</th>";
                                } else {
                                    echo "<th>Date/Time</th>";
                                }
                                ?>
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
                                $time = $result['date'];

                                //if date was a parameter in search, don't display here
                                $myFormatTime = "";
                                if(!is_null($formatted_date)){
                                    $time = strtotime($time);
                                    $myFormatTime = date("h:i A", $time);
                                } else{
                                    $myFormatTime = formatSQLDateShort($time);
                                }
                                echo "<td><em>{$myFormatTime}</em></td>";
                                echo "<td><button href=\"#\" class=\"btn btn-outline-secondary\" type=\"submit\" onclick=\"changeHiddenFormFieldValue({$counter})\">More details...</button></td>";
                                echo "</tr>";

                                $counter++;
                            }
                            } else { ?>
                                <div class="w-100 d-flex flex-column justify-content-center text-center">
                                    <h3 class="display-3 text-secondary" style="font-size: 40px;"><i
                                                class="fa fa-exclamation-circle"></i></h3>

                                    <h4 class="display-3 text-secondary" style="font-size: 30px;">No Attendance Records Found.</h4>
                                </div>
                            <?php } ?>
                            </tbody>
                        </table>

                        <input type="hidden" id="numResult" name="numResult" value="" />

                    </form>
                </div>
            </div>

        </div>
        <div class="text-center" style="margin-bottom: 20px; margin-top: 15px;">
            <a href="/attendance"><button type="button" class="btn cpca">Return To Attendance Dashboard</button></a>
            <a href="/historical-class-search"><button type="button" class="btn btn-outline-secondary">Return To Attendance Search</button></a>
        </div>
    </div>
<?php
include('footer.php');
?>