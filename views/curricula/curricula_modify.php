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
    <a href="/back"><button class="cpca btn"><i class="fa fa-arrow-left"></i> Back</button></a>
    <div class="jumbotron form-wrapper mb-3">
        <?php
        if ($success) {
            $notification = new Notification('Success!', 'The curriculum has been '.($isEdit ? 'updated' : 'created').'.', 'success');
            $notification->display();
        } else if ($success == false && $success != null) { // == false, prevents == null from being true
            $notification = new Notification('Error!', 'Uh oh! an error occurred and the curriculum wasn\'t '.
                ($isEdit ? 'updated' : 'created').'.', 'danger');
            $notification->display();
        }
        ?>
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
                <label for="curriculum-type" class="<?= $errors['type'] ? 'text-danger' : '' ?>">Type</label>
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
                    Please select a curriculum type.
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
            <table class="table table-hover table-responsive table-striped table-sm">
                <tbody>
                <?php
                while(isset($topics) && $class = pg_fetch_assoc($topics)) {
                    ?>
                    <tr>
                        <td class="align-middle">
                            <span><?= $class['topicname'] ?></span>
                        </td>
                        <td class="text-right">
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                    onclick="removeClass(this)">
                                Remove
                            </button>
                        </td>
                    </tr>
                    <?php
                }
                if (isset($topics)) pg_free_result($topics);
                ?>
                    <tr id="add-class-row">
                        <td class="align-middle">
                            <select id="class-selector" class="form-control">
                                <option value="" disabled selected>Select a Class</option>
                                <?php
                                while ($t = pg_fetch_assoc($allTopics)) {
                                    ?>
                                    <option value="<?= $t['topicname'] ?>"><?= $t['topicname'] ?></option>
                                    <?php
                                }
                                pg_free_result($allTopics);
                                ?>
                            </select>
                        </td>
                        <td class="text-right align-middle">
                            <button type="button" id="add-class-btn" class="btn cpca btn-sm" disabled>Add</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-footer submit">
                <button type="submit" class="btn cpca"><?= $isEdit ? 'Update' : 'Create' ?></button>
            </div>
        </form>
    </div>
</div>

<script>
    function removeClass(el) {
        // Remove class from table
        var clazz = $($(el).closest('td').prev('td').children('span').get(0)).text();
        $(el).closest('tr').remove();

        // Add class to select list
        $("#class-selector").append(
            '<option value="' + clazz + '">' + clazz + '</option>'
        );

        const hiddenEl = $('input[type="hidden"][value="' + clazz + '"]');
        // Add hidden element for form submission (if not added by form)
        if (hiddenEl.length === 0)
            $(".form").append('<input type="hidden" name="removed[]" value="' + clazz + '" />');

        // Remove hidden element (if added)
        hiddenEl.remove();
    }

    function addClass() {
        const val = $("#class-selector").val();

        // Add class to table
        $("<tr>" +
            "<td class='align-middle'>" +
                "<span>" + val + "</span>" +
            "</td>" +
            "<td class='text-right'>" +
                "<button type='button' class='btn btn-outline-danger btn-sm' " +
                        "onclick='removeClass(this)'>Remove</button>" +
            "</td>" +
        "</tr>").insertBefore("#add-class-row");


        // Remove from select list
        $('option[value="' + val + '"]').remove();

        const hiddenEl = $('input[type="hidden"][value="' + val + '"]');
        // Add hidden element for form submission (if not deleted by form)
        if (hiddenEl.length === 0)
            $(".form").append('<input type="hidden" name="classes[]" value="' + val + '" />');

        // Remove hidden element (if deleted)
        hiddenEl.remove();
    }

    $(function() {
        $('#add-class-btn').click(addClass);

        // Enable button if value is selected
        $('#class-selector').change(function() {
            const val = $("#class-selector").val();
            if (val) {
                $('#add-class-btn').attr('disabled', false);
            }
        });
    });
</script>

<?php
include ('footer.php');