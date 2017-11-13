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
$id = isset($params[1]) ? $params[1] : '';

# Prepare SQL statements for later use
$db->prepare("get_curriculum", "SELECT * FROM curricula WHERE curriculumid = $1");
$db->prepare("get_site", "SELECT * FROM sites WHERE sitename = $1");

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
$name = isset($curricula) ? $curricula['curriculumname'] : '';
$type = '';
if (isset($curricula)) {
    $site = pg_fetch_assoc($db->execute("get_site", [$name]));
    $type = $site['sitetype'];
}
$miss = isset($curricula) ? $curricula['missnumber'] : '';

# Used to track POST errors
$errors = [
    "name" => false,
    "type" => false,
    "miss" => false
];

# Validate form information, display errors if needed
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : $name;
    $type = isset($_POST['type']) ? $_POST['type'] : $type;
    $miss = isset($_POST['miss']) ? $_POST['miss'] : $miss;

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
        // Edit & Update
        if ($isEdit && isset($site)) {
            $siteName = $site['sitename'];
            $res = $db->query("UPDATE sites SET sitename = $1, sitetype = $2 WHERE sitename = $3", [$name, $type, $siteName]); // update sitename
            $res = $db->query("UPDATE curricula SET curriculumname = $1, " .
                "missnumber = $2 WHERE curriculumid = $3", [$name, $miss, $id]);
        }
        // Create
        else {
            $res = $db->query("INSERT INTO sites (sitename, sitetype) VALUES ($1, $2)", [$name, $type]);
            if ($res && pg_result_error_field($res, PGSQL_DIAG_SQLSTATE) == 0) {
                $res = $db->query("INSERT INTO curricula (curriculumname, missnumber) VALUES ($1, $2) ;",
                    [$name, $miss]);
            }
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
            <h2 class="display-4 text-center" style="font-size: 34px"><?= $curricula['curriculumname'] ?></h2>
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
                <label for="curriculum-type" class="<?= $errors['type'] ? 'text-danger' : '' ?>">Location</label>
                <select type="text" class="form-control <?= $errors['type'] ? 'is-invalid' : '' ?>"
                       id="curriculum-type" name="type" required>
                    <?php
                    $res = $db->query("SELECT unnest(enum_range(NULL::programtype)) AS type", []);
                    while ($currtype = pg_fetch_assoc($res)) {
                        $t = $currtype['type'];
                        ?>
                        <option value="<?= $t ?>" <?= $type == $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php
                    }
                    ?>
                </select>
                <div class="invalid-feedback">
                    Please select a curriculum location.
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
            <h4>Classes</h4>
            <a href="/curricula/classes/<?= $id ?>"><button type="button" class="btn btn-secondary">Click to Manage Classes</button></a>
            <div class="form-footer submit">
                <button type="submit" class="btn cpca"><?= $isEdit ? 'Submit New Changes' : 'Add Curriculum' ?></button>
            </div>
        </form>
    </div>
</div>

<?php
include ('footer.php');