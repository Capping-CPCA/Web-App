<?php
global $params, $db;
$isEdit = $params[0] == 'edit';
$id = isset($params[1]) ? $params[1] : '';
# Prepare SQL statements for later use
$db->prepare("get_curriculum", "SELECT * FROM curricula WHERE curriculumid = $1");
$db->prepare("get_curr_classes", "SELECT * FROM curriculumclasses WHERE curriculumid = $1 ORDER BY topicname");
$db->prepare("get_other_classes",
    "SELECT * FROM classes WHERE topicname NOT IN (" .
    "SELECT topicname FROM curriculumclasses WHERE curriculumid = $1" .
    ") ORDER BY topicname");
// If editing, populate data into variables
if ($isEdit) {
    $result = $db->execute("get_curriculum", [$id]);
    # If no results, curricula doesn't exist, redirect
    if (pg_num_rows($result) == 0) {
        header('Location: /curricula');
        die();
    }
    $curricula = pg_fetch_assoc($result);
    pg_free_result($result);
    # Classes associated with curriculum
    $topics = $db->execute("get_curr_classes", [$id]);
    # All other available classes
    $allTopics = $db->execute("get_other_classes", [$id]);
} else {
    $allTopics = $db->query("SELECT * FROM classes", []);
}
# Store table columns in variable
$name = isset($curricula) ? $curricula['curriculumname'] : '';
$type = isset($curricula) ? $curricula['curriculumtype'] : '';
$miss = isset($curricula) ? $curricula['missnumber'] : '';
# Used to track POST errors
$errors = [
    "name" => false,
    "type" => false,
    "miss" => false
];
# Used to display messages based on POST
$success = null;
# Validate form information, display errors if needed
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? $_POST['name'] : $name;
    $type = isset($_POST['type']) ? $_POST['type'] : $type;
    $miss = isset($_POST['miss']) ? $_POST['miss'] : $miss;
    $newClasses = isset($_POST['classes']) ? $_POST['classes'] : [];
    $removedClasses = isset($_POST['removed']) ? $_POST['removed'] : [];
    $valid = true;
    if (!isValidText($name)) {
        $errors['name'] = true;
        $valid = false;
    }
    if (empty($type)) {
        $errors['type'] = true;
    }
    if (!isValidNumber($miss, 0)) {
        $errors['miss'] = true;
        $valid = false;
    }
    if ($valid) {
        if ($isEdit) {
            $res = $db->query("UPDATE curricula SET curriculumname = $1, curriculumtype = $2, " .
                "missnumber = $3 WHERE curriculumid = $4", [$name, $type, $miss, $id]);
        } else {
            $res = $db->query("INSERT INTO curricula (curriculumname, curriculumtype, missnumber) VALUES ($1, $2, $3) RETURNING curriculumid",
                [$name, $type, $miss]);
            $id = pg_fetch_assoc($res)['curriculumid'];
        }
        if ($res) {
            foreach ($newClasses as $c) {
                $db->query("INSERT INTO curriculumclasses VALUES ($1, $2)", [$c, $id]);
            }
            foreach ($removedClasses as $r) {
                $db->query("DELETE FROM curriculumclasses WHERE topicname = $1 AND curriculumid = $2",
                    [$r, $id]);
            }
            $success = true;
        } else {
            $success = false;
        }
        # update variables for displaying new values
        $curricula = pg_fetch_assoc($db->execute("get_curriculum", [$id]));
        if (isset($topics)) pg_free_result($topics);
        $topics = $db->execute("get_curr_classes", [$id]);
        if (isset($allTopics)) pg_free_result($allTopics);
        $allTopics = $db->execute("get_other_classes", [$id]);
    }
}
# Display Page
include ('header.php');
?>

<div class="page-wrapper">
    <a href="/back"><button class="btn btn-success"><i class="fa fa-arrow-left"></i> Back</button></a>
    <!--<div class="jumbotron form-wrapper mb-3">-->
	<p>
	<center>
	<div class="container">
		<div class="row justify-content-md-center">
			<div class="col-sm">
				<h4>Results:</h4>
			</div>
		</div>
	</div>
	</center>
	<p>
	<div class="container">
		<div class="row justify-content-md-center">
			<div class="col-sm" style="max-width:85%">
				<div id="accordion" role="tablist">
				  <div class="card">
					<div class="card-header" role="tab" id="headingOne">
					  <h5 class="mb-0">
						<a data-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						  Mario Kheart
						</a>
					  </h5>
					</div>

					<div id="collapseOne" class="collapse" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion">
					  <div class="card-body">
						Q1:	 <b>10/10/2017 10:58:34</b>	<br>
						Q2:	 <b>Yes</b> <br>
						Q3:	 <b>Ryan</b>	<br>
						Q4:  <b>10/10/2017</b>	<br>
						Q5:  <b>Class3</b>	<br>
						Q6:  <b>Location1</b>	<br>
						Q7:  <b>Male</b>	<br>
						Q8:	 <b>White</b>	<br>
						Q9:	 <b>15-19</b>	<br>
						Q10: <b>6</b>	<br>
						Q11: <b>7</b>	<br>
						Q12: <b>7</b>	<br>
						Q13: <b>7</b>	<br>
						Q14: <b>7</b>	<br>
						Q15: <b>7</b>	<br>
						Q16: <b>None</b>	<br>
						Q17: <b>Test</b><br>
					  </div>
					</div>
				  </div>
				  <div class="card">
					<div class="card-header" role="tab" id="heading2">
					  <h5 class="mb-0">
						<a class="collapsed" data-toggle="collapse" href="#collapse2" aria-expanded="false" aria-controls="collapse2">
						  Petey Cruiser
						</a>
					  </h5>
					</div>
					<div id="collapse2" class="collapse" role="tabpanel" aria-labelledby="heading2" data-parent="#accordion">
					  <div class="card-body">
						Q1:	 <b>10/10/2017 10:58:34</b>	<br>
						Q2:	 <b>Yes</b> <br>
						Q3:	 <b>Ryan</b>	<br>
						Q4:  <b>10/10/2017</b>	<br>
						Q5:  <b>Class3</b>	<br>
						Q6:  <b>Location1</b>	<br>
						Q7:  <b>Male</b>	<br>
						Q8:	 <b>White</b>	<br>
						Q9:	 <b>15-19</b>	<br>
						Q10: <b>6</b>	<br>
						Q11: <b>7</b>	<br>
						Q12: <b>7</b>	<br>
						Q13: <b>7</b>	<br>
						Q14: <b>7</b>	<br>
						Q15: <b>7</b>	<br>
						Q16: <b>None</b>	<br>
						Q17: <b>Test</b> <br>
					  </div>
					</div>
				  </div>
				  <div class="card">
					<div class="card-header" role="tab" id="heading3">
					  <h5 class="mb-0">
						<a data-toggle="collapse" href="#collapse3" aria-expanded="true" aria-controls="collapse3">
						  Josh Sthesia
						</a>
					  </h5>
					</div>

					<div id="collapse3" class="collapse" role="tabpanel" aria-labelledby="heading3" data-parent="#accordion">
					  <div class="card-body">
						Q1:	 <b>10/10/2017 10:58:34</b>	<br>
						Q2:	 <b>Yes</b> <br>
						Q3:	 <b>Ryan</b>	<br>
						Q4:  <b>10/10/2017</b>	<br>
						Q5:  <b>Class3</b>	<br>
						Q6:  <b>Location1</b>	<br>
						Q7:  <b>Male</b>	<br>
						Q8:	 <b>White</b>	<br>
						Q9:	 <b>15-19</b>	<br>
						Q10: <b>6</b>	<br>
						Q11: <b>7</b>	<br>
						Q12: <b>7</b>	<br>
						Q13: <b>7</b>	<br>
						Q14: <b>7</b>	<br>
						Q15: <b>7</b>	<br>
						Q16: <b>None</b>	<br>
						Q17: <b>Test</b><br>
					  </div>
					</div>
				  </div>
				  <div class="card">
					<div class="card-header" role="tab" id="heading4">
					  <h5 class="mb-0">
						<a class="collapsed" data-toggle="collapse" href="#collapse4" aria-expanded="false" aria-controls="collapse4">
						  Paul Molive
						</a>
					  </h5>
					</div>
					<div id="collapse4" class="collapse" role="tabpanel" aria-labelledby="heading4" data-parent="#accordion">
					  <div class="card-body">
						Q1:	 <b>10/10/2017 10:58:34</b>	<br>
						Q2:	 <b>Yes</b> <br>
						Q3:	 <b>Ryan</b>	<br>
						Q4:  <b>10/10/2017</b>	<br>
						Q5:  <b>Class3</b>	<br>
						Q6:  <b>Location1</b>	<br>
						Q7:  <b>Male</b>	<br>
						Q8:	 <b>White</b>	<br>
						Q9:	 <b>15-19</b>	<br>
						Q10: <b>6</b>	<br>
						Q11: <b>7</b>	<br>
						Q12: <b>7</b>	<br>
						Q13: <b>7</b>	<br>
						Q14: <b>7</b>	<br>
						Q15: <b>7</b>	<br>
						Q16: <b>None</b>	<br>
						Q17: <b>Test</b><br>
					  </div>
					</div>
				  </div>
				  <div class="card">
					<div class="card-header" role="tab" id="heading5">
					  <h5 class="mb-0">
						<a data-toggle="collapse" href="#collapse5" aria-expanded="true" aria-controls="collapse5">
						  Bob Frapples
						</a>
					  </h5>
					</div>

					<div id="collapse5" class="collapse" role="tabpanel" aria-labelledby="heading5" data-parent="#accordion">
					  <div class="card-body">
						Q1:	 <b>10/10/2017 10:58:34</b>	<br>
						Q2:	 <b>Yes</b> <br>
						Q3:	 <b>Ryan</b>	<br>
						Q4:  <b>10/10/2017</b>	<br>
						Q5:  <b>Class3</b>	<br>
						Q6:  <b>Location1</b>	<br>
						Q7:  <b>Male</b>	<br>
						Q8:	 <b>White</b>	<br>
						Q9:	 <b>15-19</b>	<br>
						Q10: <b>6</b>	<br>
						Q11: <b>7</b>	<br>
						Q12: <b>7</b>	<br>
						Q13: <b>7</b>	<br>
						Q14: <b>7</b>	<br>
						Q15: <b>7</b>	<br>
						Q16: <b>None</b>	<br>
						Q17: <b>Test</b><br>
					  </div>
					</div>
				  </div>
				  <div class="card">
					<div class="card-header" role="tab" id="heading6">
					  <h5 class="mb-0">
						<a class="collapsed" data-toggle="collapse" href="#collapse6" aria-expanded="false" aria-controls="collapse6">
						  Buck Kinnear
						</a>
					  </h5>
					</div>
					<div id="collapse6" class="collapse" role="tabpanel" aria-labelledby="heading6" data-parent="#accordion">
					  <div class="card-body">
						Q1:	 <b>10/10/2017 10:58:34</b>	<br>
						Q2:	 <b>Yes</b> <br>
						Q3:	 <b>Ryan</b>	<br>
						Q4:  <b>10/10/2017</b>	<br>
						Q5:  <b>Class3</b>	<br>
						Q6:  <b>Location1</b>	<br>
						Q7:  <b>Male</b>	<br>
						Q8:	 <b>White</b>	<br>
						Q9:	 <b>15-19</b>	<br>
						Q10: <b>6</b>	<br>
						Q11: <b>7</b>	<br>
						Q12: <b>7</b>	<br>
						Q13: <b>7</b>	<br>
						Q14: <b>7</b>	<br>
						Q15: <b>7</b>	<br>
						Q16: <b>None</b>	<br>
						Q17: <b>Test</b><br>
					  </div>
					</div>
				  </div>
				  <div class="card">
					<div class="card-header" role="tab" id="heading7">
					  <h5 class="mb-0">
						<a data-toggle="collapse" href="#collapse7" aria-expanded="true" aria-controls="collapse7">
						  Sal Monella
						</a>
					  </h5>
					</div>

					<div id="collapse7" class="collapse" role="tabpanel" aria-labelledby="heading7" data-parent="#accordion">
					  <div class="card-body">
						Q1:	 <b>10/10/2017 10:58:34</b>	<br>
						Q2:	 <b>Yes</b> <br>
						Q3:	 <b>Ryan</b>	<br>
						Q4:  <b>10/10/2017</b>	<br>
						Q5:  <b>Class3</b>	<br>
						Q6:  <b>Location1</b>	<br>
						Q7:  <b>Male</b>	<br>
						Q8:	 <b>White</b>	<br>
						Q9:	 <b>15-19</b>	<br>
						Q10: <b>6</b>	<br>
						Q11: <b>7</b>	<br>
						Q12: <b>7</b>	<br>
						Q13: <b>7</b>	<br>
						Q14: <b>7</b>	<br>
						Q15: <b>7</b>	<br>
						Q16: <b>None</b>	<br>
						Q17: <b>Test</b><br>
					  </div>
					</div>
				  </div>
				  <div class="card">
					<div class="card-header" role="tab" id="heading8">
					  <h5 class="mb-0">
						<a class="collapsed" data-toggle="collapse" href="#collapse8" aria-expanded="false" aria-controls="collapse8">
						  Cliff Hanger
						</a>
					  </h5>
					</div>
					<div id="collapse8" class="collapse" role="tabpanel" aria-labelledby="heading8" data-parent="#accordion">
					  <div class="card-body">
						Q1:	 <b>10/10/2017 10:58:34</b>	<br>
						Q2:	 <b>Yes</b> <br>
						Q3:	 <b>Ryan</b>	<br>
						Q4:  <b>10/10/2017</b>	<br>
						Q5:  <b>Class3</b>	<br>
						Q6:  <b>Location1</b>	<br>
						Q7:  <b>Male</b>	<br>
						Q8:	 <b>White</b>	<br>
						Q9:	 <b>15-19</b>	<br>
						Q10: <b>6</b>	<br>
						Q11: <b>7</b>	<br>
						Q12: <b>7</b>	<br>
						Q13: <b>7</b>	<br>
						Q14: <b>7</b>	<br>
						Q15: <b>7</b>	<br>
						Q16: <b>None</b>	<br>
						Q17: <b>Test</b><br>
					  </div>
					</div>
				  </div>
				  <div class="card">
					<div class="card-header" role="tab" id="heading9">
					  <h5 class="mb-0">
						<a data-toggle="collapse" href="#collapse9" aria-expanded="true" aria-controls="collapse9">
						  Terry Aki
						</a>
					  </h5>
					</div>

					<div id="collapse9" class="collapse" role="tabpanel" aria-labelledby="heading9" data-parent="#accordion">
					  <div class="card-body">
						Q1:	 <b>10/10/2017 10:58:34</b>	<br>
						Q2:	 <b>Yes</b> <br>
						Q3:	 <b>Ryan</b>	<br>
						Q4:  <b>10/10/2017</b>	<br>
						Q5:  <b>Class3</b>	<br>
						Q6:  <b>Location1</b>	<br>
						Q7:  <b>Male</b>	<br>
						Q8:	 <b>White</b>	<br>
						Q9:	 <b>15-19</b>	<br>
						Q10: <b>6</b>	<br>
						Q11: <b>7</b>	<br>
						Q12: <b>7</b>	<br>
						Q13: <b>7</b>	<br>
						Q14: <b>7</b>	<br>
						Q15: <b>7</b>	<br>
						Q16: <b>None</b>	<br>
						Q17: <b>Test</b><br>
					  </div>
					</div>
				  </div>
				  <div class="card">
					<div class="card-header" role="tab" id="heading10">
					  <h5 class="mb-0">
						<a class="collapsed" data-toggle="collapse" href="#collapse10" aria-expanded="false" aria-controls="collapse10">
						  Robin Banks
						</a>
					  </h5>
					</div>
					<div id="collapse10" class="collapse" role="tabpanel" aria-labelledby="heading10" data-parent="#accordion">
					  <div class="card-body">
						Q1:	 <b>10/10/2017 10:58:34</b>	<br>
						Q2:	 <b>Yes</b> <br>
						Q3:	 <b>Ryan</b>	<br>
						Q4:  <b>10/10/2017</b>	<br>
						Q5:  <b>Class3</b>	<br>
						Q6:  <b>Location1</b>	<br>
						Q7:  <b>Male</b>	<br>
						Q8:	 <b>White</b>	<br>
						Q9:	 <b>15-19</b>	<br>
						Q10: <b>6</b>	<br>
						Q11: <b>7</b>	<br>
						Q12: <b>7</b>	<br>
						Q13: <b>7</b>	<br>
						Q14: <b>7</b>	<br>
						Q15: <b>7</b>	<br>
						Q16: <b>None</b>	<br>
						Q17: <b>Test</b><br>
					  </div>
					</div>
				  </div>
				  <div class="card">
					<div class="card-header" role="tab" id="heading11">
					  <h5 class="mb-0">
						<a class="collapsed" data-toggle="collapse" href="#collapse11" aria-expanded="false" aria-controls="collapse11">
						  Jimmy Changa
						</a>
					  </h5>
					</div>

					<div id="collapse11" class="collapse" role="tabpanel" aria-labelledby="heading11" data-parent="#accordion">
					  <div class="card-body">
						Q1:	 <b>10/10/2017 10:58:34</b>	<br>
						Q2:	 <b>Yes</b> <br>
						Q3:	 <b>Ryan</b>	<br>
						Q4:  <b>10/10/2017</b>	<br>
						Q5:  <b>Class3</b>	<br>
						Q6:  <b>Location1</b>	<br>
						Q7:  <b>Male</b>	<br>
						Q8:	 <b>White</b>	<br>
						Q9:	 <b>15-19</b>	<br>
						Q10: <b>6</b>	<br>
						Q11: <b>7</b>	<br>
						Q12: <b>7</b>	<br>
						Q13: <b>7</b>	<br>
						Q14: <b>7</b>	<br>
						Q15: <b>7</b>	<br>
						Q16: <b>None</b>	<br>
						Q17: <b>Test</b><br>
					  </div>
					</div>
				  </div>
				  <div class="card">
					<div class="card-header" role="tab" id="heading12">
					  <h5 class="mb-0">
						<a class="collapsed" data-toggle="collapse" href="#collapse12" aria-expanded="false" aria-controls="collapse12">
						  Barry Wine
						</a>
					  </h5>
					</div>
					<div id="collapse12" class="collapse" role="tabpanel" aria-labelledby="heading12" data-parent="#accordion">
					  <div class="card-body">
						Q1:	 <b>10/10/2017 10:58:34</b>	<br>
						Q2:	 <b>Yes</b> <br>
						Q3:	 <b>Ryan</b>	<br>
						Q4:  <b>10/10/2017</b>	<br>
						Q5:  <b>Class3</b>	<br>
						Q6:  <b>Location1</b>	<br>
						Q7:  <b>Male</b>	<br>
						Q8:	 <b>White</b>	<br>
						Q9:	 <b>15-19</b>	<br>
						Q10: <b>6</b>	<br>
						Q11: <b>7</b>	<br>
						Q12: <b>7</b>	<br>
						Q13: <b>7</b>	<br>
						Q14: <b>7</b>	<br>
						Q15: <b>7</b>	<br>
						Q16: <b>None</b>	<br>
						Q17: <b>Test</b><br>
					  </div>
					</div>
				  </div>
				  <p>
				  
				</div>
            </div>
		</div>
	</div>
       
    <!--</div>-->
</div>



<?php
include ('footer.php');