<?php
global $params, $db;
$isEdit = $params[0] == 'edit';
array_shift($params);

# Get topic name from params
$topicname = rawurldecode(implode('/', $params));

# Prepare SQL statements for later use
$db->prepare("get_class", "SELECT * FROM classes WHERE topicname = $1");

// If editing, populate data into variables
if ($isEdit) {
    $result = $db->execute("get_class", [$topicname]);

    # If no results, class doesn't exist, redirect
    if (pg_num_rows($result) == 0) {
        header('Location: /classes');
        die();
    }

    $class = pg_fetch_assoc($result);
    pg_free_result($result);
}

$name = isset($class) ? $class['topicname'] : '';
$desc = isset($class) ? $class['description'] : '';

# Used to track POST errors
$errors = [
    "name" => false,
    "desc" => false
];
# Used to display messages based on POST
$success = "";
$errorMsg = "";

# Validate form information, display errors if needed
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $oldName = $name;
    $name = isset($_POST['name']) ? $_POST['name'] : $name;
    $desc = isset($_POST['desc']) ? $_POST['desc'] : $desc;

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
            $res = $db->query("UPDATE classes SET description = $1 ".
                "WHERE topicname = $2", [$desc,$oldName]);
        } else {
            $res = $db->query("INSERT INTO classes VALUES ($1, $2)", [$name,$desc]);
        }

        if ($res) {
            $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
            if ($state == 0) {
                $success = "true";
            } else {
                $success = "false";
                if ($state == "23505") { // unique_violation
                    $errorMsg = "Class with name \"$name\" already exists.";
                } else {
                    // process other errors
                }
            }
        } else {
            $success = "false";
        }

        $class = pg_fetch_assoc($db->execute("get_class", [$name]));
    }
}

# Display page
include ('header.php');
?>

<div class="page-wrapper">
    <a href="/back"><button class="cpca btn"><i class="fa fa-arrow-left"></i> Back</button></a>
    <div class="jumbotron form-wrapper mb-3">
        <?php
        if ($success == "true") {
            $notification = new Notification('Success!', 'The class has been '.($isEdit ? 'updated' : 'created').'.', 'success');
            $notification->display();
        } else if ($success == "false") { // == false, prevents == null from being true
            $notification = new Notification('Error!', !empty($errorMsg) ? $errorMsg : ('Uh oh! an error occurred and the class wasn\'t '.
                ($isEdit ? 'updated' : 'created').'.'), 'danger');
            $notification->display();
        }
        ?>
        <form class="form" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>" novalidate>
            <h4>Information</h4>
            <div class="form-group">
                <label for="class-name" class="<?= $errors['name'] ? 'text-danger' : '' ?>">Name</label>
                <input type="text" class="form-control <?= $errors['name'] ? 'is-invalid' : '' ?>"
                       value="<?= $name ?>" id="class-name" name="name" required <?= $isEdit ? 'disabled' : '' ?> />
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
                <button type="submit" class="btn cpca"><?= $isEdit ? 'Update' : 'Create' ?></button>
            </div>
        </form>
    </div>
</div>

<?php
include ('footer.php');
?>
