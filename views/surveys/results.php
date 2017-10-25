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
				
       
    <!--</div>-->
</div>



<?php
include ('footer.php');