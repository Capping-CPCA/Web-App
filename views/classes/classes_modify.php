<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays a page to allow class editing.
 *
 * This page provides various sections to allow an
 * admin to edit details about a class. Once the form
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

$id = isset($params[1]) ? $params[1] : '';

# Prepare SQL statements for later use
$db->prepare("get_class", "SELECT * FROM classes WHERE classid = $1");

// If editing, populate data into variables
if ($isEdit) {
    $result = $db->execute("get_class", [$id]);

    # If no results, class doesn't exist, redirect
    if (pg_num_rows($result) == 0) {
        header('Location: /classes');
        die();
    }

    $class = pg_fetch_assoc($result);
    pg_free_result($result);
}

$name = isset($class) ? htmlentities($class['topicname']) : '';
$desc = isset($class) ? htmlentities($class['description']) : '';

# Used to track POST errors
$errors = [
    "name" => false,
    "desc" => false
];

# Validate form information, display errors if needed
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? htmlentities(trim($_POST['name'])) : $name;
    $desc = isset($_POST['desc']) ? htmlentities(trim($_POST['desc'])) : $desc;

    $valid = true;

    if (!isValidText($name)) {
        $errors['name'] = true;
        $valid = false;
    }
    if (!empty($desc) && !isValidText($desc)) {
        $errors['desc'] = true;
        $valid = false;
    }

    if ($valid) {
        if ($isEdit) {
            $res = $db->query("UPDATE classes SET topicname = $1, description = $2 ".
                "WHERE classid = $3", [$name, $desc, $id]);
        } else {
            $res = $db->query("INSERT INTO classes (topicname, description) VALUES ($1, $2)", [$name,$desc]);
        }

        if ($res) {
            $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
            if ($state == 0) {
                $success = true;
            } else {
                $success = false;
                if ($state == "23505") { // unique_violation
                    $errorMsg = "Class with name \"$name\" already exists.";
                }
                $errorState = $state;
            }
        } else {
            $success = false;
        }

        $class = pg_fetch_assoc($db->execute("get_class", [$id]));
    } else {
        $success = false;
        $errorMsg = "There are errors in the form.";
    }

    if ($success) {
        $note['title'] = 'Success!';
        $note['msg'] = 'The class has been ' . ($isEdit ? 'updated' : 'created') . '.';
        $note['type'] = 'success';
        $_SESSION['notification'] = $note;
        header("Location: /classes");
        die();
    }
}

# Display page
include ('header.php');
?>

<div class="page-wrapper">
    <button class="cpca btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
    <div class="jumbotron form-wrapper mb-3">
        <?php
        if (isset($success)) {
            if (!$success) {
                $notification = new Notification('Error!', isset($errorMsg) ? $errorMsg : ('Uh oh! An error occurred and the class wasn\'t ' .
                    ($isEdit ? 'updated' : 'created') . '.') . (isset($errorState) ? " [$errorState]" : ""), 'danger');
                $notification->display();
            }
        }
        ?>
        <form class="form" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>" novalidate>
            <h4>Information</h4>
            <div class="form-group">
                <label for="class-name" class="<?= $errors['name'] ? 'text-danger' : '' ?>">Name</label>
                <input type="text" class="form-control <?= $errors['name'] ? 'is-invalid' : '' ?>"
                       value="<?= $name ?>" id="class-name" name="name" required />
                <div class="invalid-feedback">
                    Invalid characters found in name.
                </div>
            </div>
            <div class="form-group">
                <label for="class-desc" class="<?= $errors['desc'] ? 'text-danger' : '' ?>">Description</label>
                <input type="text" class="form-control <?= $errors['desc'] ? 'is-invalid' : '' ?>"
                       value="<?= $desc ?>" id="class-desc" name="desc" />
                <div class="invalid-feedback">
                    Invalid characters found in description.
                </div>
            </div>
            <div class="form-footer submit">
                <button type="submit" class="btn cpca"><?= $isEdit ? 'Submit New Changes' : 'Add Class' ?></button>
            </div>
        </form>
    </div>
</div>

<?php
include ('footer.php');
?>
