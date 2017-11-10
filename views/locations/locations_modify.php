<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays a page to allow location editing.
 *
 * This page provides various sections to allow an
 * admin to edit details about a location. Once the form
 * is filled out, if there are any errors, they will
 * be displayed upon submission.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.6
 * @since 0.1
 * @deprecated
 */

global $params, $db;
$isEdit = $params[0] == 'edit';

array_shift($params);

$name = rawurldecode(implode('/', $params));

$db->prepare("get_site", "SELECT TRUE WHERE $1 IN (SELECT unnest(enum_range(NULL::programtype))::text as type);");

if ($isEdit) {
    $result = $db->execute("get_site", [$name]);

    if (pg_num_rows($result) == 0) {
        header('Location: /locations');
        die();
    }

    pg_free_result($result);
}

# Used to track POST errors
$errors = [
    "name" => false,
];

# Validate form information, display errors if needed
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $oldName = $name;
    $name = isset($_POST['name']) ? $_POST['name'] : $name;
    $name = trim(addslashes($name));
    $valid = true;

    if (!isValidText($name)) {
        $errors['name'] = true;
        $valid = false;
    }

    if ($valid) {
        if ($isEdit) {
            // Remove old enum from site
            $res = $db->query("UPDATE sites SET sitetype = NULL WHERE sitetype = $1", [$oldName]);
            if ($res && pg_result_error_field($res, PGSQL_DIAG_SQLSTATE) == 0) {
                // Update enum name
                $res = $db->query("UPDATE pg_enum SET enumlabel = $1 " .
                    "WHERE enumtypid = 'programtype'::REGTYPE AND " .
                    "enumlabel = $2", [$name, $oldName]);
                if ($res && pg_result_error_field($res, PGSQL_DIAG_SQLSTATE) == 0) {
                    // Update sitetype in site
                    $res = $db->query("UPDATE sites SET sitetype = $1 WHERE sitetype IS NULL", [$name]);
                }
            }
        } else {
            $res = $db->query("ALTER TYPE programtype ADD VALUE E'$name'", []);
        }

        if ($res) {
            $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
            if ($state == 0) {
                $success = true;
            } else {
                $success = false;
                if ($state == "23505") { // unique_violation
                    $errorMsg = "Location with name \"$name\" already exists.";
                }
                $errorState = $state;
            }
        } else {
            $success = false;
        }

    } else {
        $success = false;
    }

    if ($success) {
        $note['title'] = 'Success!';
        $note['msg'] = 'The location has been ' . ($isEdit ? 'updated.' : 'created.');
        $note['type'] = 'success';
        $_SESSION['notification'] = $note;
        header("Location: /locations");
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
                $notification = new Notification('Error!',
                    isset($errorMsg) ? $errorMsg : ('Uh oh! An error occurred and the location wasn\'t created.') . (isset($errorState) ? " [$errorState]" : ""), 'danger');
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
            <div class="form-footer submit">
                <button type="submit" class="btn cpca"><?= $isEdit ? 'Submit New Changes' : 'Add Location' ?></button>
            </div>
        </form>
    </div>
</div>

<?php
include ('footer.php');
?>
