<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays a page to allow curriculum editing.
 *
 * This page provides various sections to allow an
 * admin to edit details about a curriculum. Once the form
 * is filled out, if there are any errors, they will
 * be displayed upon submission.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.6
 * @since 0.1
 */

global $params, $db;
$isEdit = $params[0] == 'edit';

# Get topic name from params
$id = isset($params[1]) ? htmlentities($params[1]) : '';

# Prepare SQL statements for later use
$db->prepare("get_curriculum", "SELECT * FROM curricula WHERE curriculumid = $1");

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
}

# Store table columns in variable
$name = isset($curricula) ? htmlentities($curricula['curriculumname']) : '';
$miss = isset($curricula) ? htmlentities($curricula['missnumber']) : '';

# Used to track POST errors
$errors = [
    "name" => false,
    "miss" => false
];

# Validate form information, display errors if needed
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? html_entity_decode(trim($_POST['name'])) : $name;
    $miss = isset($_POST['miss']) ? html_entity_decode($_POST['miss']) : $miss;

    $valid = true;

    if (!isValidText($name)) {
        $errors['name'] = true;
        $valid = false;
    }
    if (!isValidNumber($miss, 0)) {
        $errors['miss'] = true;
        $valid = false;
    }

    if ($valid) {
        // Edit & Update
        if ($isEdit) {
            $res = $db->query("UPDATE curricula SET curriculumname = $1, " .
                "missnumber = $2 WHERE curriculumid = $3", [$name, $miss, $id]);
        }
        // Create
        else {
            $res = $db->query("INSERT INTO curricula (curriculumname, missnumber) VALUES ($1, $2) ;",
                [$name, $miss]);
        }

        if ($res) {
            $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
            if ($state == 0) {
                $success = true;
            } else {
                $success = false;
                if ($state == "23505") { // unique violation
                    $errorMsg = "Curriculum with name \"$name\" already exists. [$state]";
                }
                $errorState = $state;
            }
        } else {
            $success = false;
        }

        # update variables for displaying new values
        $curricula = pg_fetch_assoc($db->execute("get_curriculum", [$id]));
    } else {
        $success = false;
        $errorMsg = "There are errors in the form.";
    }

    if ($success) {
        $note['title'] = 'Success!';
        $note['msg'] = 'The curriculum has been ' . ($isEdit ? 'updated' : 'created') . '.';
        $note['type'] = 'success';
        $_SESSION['notification'] = $note;
        header("Location: /curricula");
        die();
    }

}

# Display Page
include ('header.php');
?>

<div class="page-wrapper">
    <button class="cpca btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
    <div class="jumbotron form-wrapper mb-3">
        <?php
        if (isset($success)) {
            if (!$success) {
                $notification = new Notification('Error!',
                    isset($errorMsg) ? $errorMsg : ('Uh oh! An error occurred and the curriculum wasn\'t ' .
                    ($isEdit ? 'updated' : 'created') . '.') . (isset($errorState) ? " [$errorState]" : ""), 'danger');
                $notification->display();
            }
        }
        ?>
        <?php if (isset($curricula)) { ?>
            <h2 class="display-4 text-center" style="font-size: 34px"><?= htmlentities($curricula['curriculumname']) ?></h2>
        <?php } ?>
        <form class="form" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>" novalidate>
            <h4>Information</h4>
            <div class="form-group">
                <label for="curriculum-name" class="<?= $errors['name'] ? 'text-danger' : '' ?>">Name</label>
                <input type="text" class="form-control <?= $errors['name'] ? 'is-invalid' : '' ?>"
                       value="<?= $name ?>" id="curriculum-name" name="name" required />
                <div class="invalid-feedback">
                    Invalid characters found in name.
                </div>
            </div>
            <div class="form-group">
                <label for="curriculum-miss" class="<?= $errors['miss'] ? 'text-danger' : '' ?>">Maximum # of Missed Classes</label>
                <input type="number" class="form-control <?= $errors['miss'] ? 'is-invalid' : '' ?>"
                       value="<?= $miss ?>" id="curriculum-miss" name="miss" required />
                <div class="invalid-feedback">
                    Please select a number 0 (zero) or greater.
                </div>
            </div>
            <?php if ($isEdit) { ?>
                <h4>Classes</h4>
                <a href="/curricula/classes/<?= $id ?>"><button type="button" class="btn btn-secondary">Click to Manage Classes</button></a>
            <?php } ?>
            <div class="form-footer submit">
                <button type="submit" class="btn cpca"><?= $isEdit ? 'Submit New Changes' : 'Add Curriculum' ?></button>
            </div>
        </form>
    </div>
</div>

<?php
include ('footer.php');