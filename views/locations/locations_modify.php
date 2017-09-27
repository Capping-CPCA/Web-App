<?php
global $params, $db;
$isEdit = $params[0] == 'edit';
array_shift($params);

# Get site name from params
$sitename = rawurldecode(implode('/', $params));

# Prepare SQL statements for later use
$db->prepare("get_site", "SELECT * FROM sites WHERE sitename = $1");

// If editing, populate data into variables
if ($isEdit) {
    $result = $db->execute("get_site", [$sitename]);

    # If no results, class doesn't exist, redirect
    if (pg_num_rows($result) == 0) {
        header('Location: /locations');
        die();
    }

    $site = pg_fetch_assoc($result);
    pg_free_result($result);
}

$name = isset($site) ? $site['sitename'] : '';
$type = isset($site) ? $site['programtype'] : '';

# Used to track POST errors
$errors = [
    "name" => false,
    "type" => false
];
# Used to display messages based on POST
$success = "";
$errorMsg = "";

# Validate form information, display errors if needed
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $oldName = $name;
    $name = isset($_POST['name']) ? $_POST['name'] : $name;
    $type = isset($_POST['type']) ? $_POST['type'] : $type;

    $valid = true;

    if (!isValidText($name)) {
        $errors['name'] = true;
        $valid = false;
    }
    if (empty($type)) {
        $errors['type'] = true;
        $valid = false;
    }

    if ($valid) {
        if ($isEdit) {
            $res = $db->query("UPDATE sites SET programtype = $1 ".
                "WHERE sitename = $2", [$type,$oldName]);
        } else {
            $res = $db->query("INSERT INTO sites VALUES ($1, $2)", [$name,$type]);
        }

        if ($res) {
            $state = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
            if ($state == 0) {
                $success = "true";
            } else {
                $success = "false";
                if ($state == "23505") { // unique_violation
                    $errorMsg = "Location with name \"$name\" already exists.";
                } else {
                    // process other errors
                }
            }
        } else {
            $success = "false";
        }

        $site = pg_fetch_assoc($db->execute("get_site", [$name]));
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
            $notification = new Notification('Success!', 'The location has been '.($isEdit ? 'updated' : 'created').'.', 'success');
            $notification->display();
        } else if ($success == "false") { // == false, prevents == null from being true
            $notification = new Notification('Error!', !empty($errorMsg) ? $errorMsg : ('Uh oh! an error occurred and the location wasn\'t '.
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
                <label for="type-select" class="<?= $errors['desc'] ? 'text-danger' : '' ?>">Program Type</label>
                <select id="type-select" class="form-control <?= $errors['type'] ? 'is-invalid' : '' ?>" name="type">
                    <?php
                    $res = $db->query("SELECT unnest(enum_range(NULL::programtype)) AS type", []);
                    while ($programtype = pg_fetch_assoc($res)) {
                        $t = $programtype['type'];
                        ?>
                        <option value="<?= $t ?>" <?= $type == $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php
                    }
                    ?>
                </select>
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
