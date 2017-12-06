<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays a page to allow account editing.
 *
 * This page provides various sections to allow:
 * 1. A user to modify their own account settings
 * 2. An admin/superuser to modify other users'
 * account settings
 *
 * Once the form is filled out, if there are any
 * errors, they will be displayed upon submission.
 *
 * @author Michelle Crawley
 * @copyright 2017 Marist College
 * @version 0.3.3
 * @since 0.1
 */

global $db, $route, $params, $view;

# Get employee id from the route parameters
$employeeid = $params[1];

# Get if employee is a superuser
$result = $db->query("SELECT superuserID FROM superusers WHERE superuserid = $1", [$employeeid]);
$isSuperUser = pg_fetch_assoc($result);

# Checks if the user is trying to edit their own account or if they are an admin/superuser and trying to edit another superuser
if ((($_SESSION['employeeid'] != $employeeid) && (!(hasRole(Role::Admin)))) ||
    ($isSuperUser && !(hasRole(Role::Superuser)))) {
    header('Location: /dashboard');
    die();
} else {
    $loggedInUserRole = $_SESSION['role'];

    # Get employee information
    $db->prepare("get-employee-info", "SELECT people.firstname, people.middleinit, people.lastname, employees.email, employees.primaryphone, employees.permissionlevel " .
        "FROM employees " .
        "LEFT JOIN people ON employees.employeeid = people.peopleid " .
        "WHERE employees.employeeid=$1");
    $result = $db->execute("get-employee-info", [$employeeid]);
    $employee = pg_fetch_assoc($result);
    extract($employee);

    # Get permissions from DB
    $db->prepare("get-permissions", "SELECT unnest(enum_range(NULL::permission)) AS permission;");
    $result = $db->execute("get-permissions", []);
    $permissions = pg_fetch_all($result);

    # Check if the user is a facilitator
    $db->prepare("get-is-facilitator", "SELECT facilitatorid FROM facilitators WHERE facilitatorid = $1 AND df = $2");
    $result = $db->execute("get-is-facilitator", [$employeeid, 0]);
    $isFacilitator = pg_fetch_assoc($result);

    # Prepare query to get languages
    $db->prepare("get-languages", "SELECT languages.lang FROM languages ");
    $db->prepare("get-facilitator-languages", "SELECT lang FROM facilitatorlanguage WHERE facilitatorid = $1 AND level = $2");

    # If the user is a facilitator, get his/her languages
    if ($isFacilitator) {
        $result = $db->execute("get-languages", []);
        $languages = pg_fetch_all($result);
        $result = $db->execute("get-facilitator-languages", [$employeeid, "PRIMARY"]);
        $primaryLang = pg_fetch_assoc($result);
        $result = $db->execute("get-facilitator-languages", [$employeeid, "SECONDARY"]);
        $secondaryLang = pg_fetch_all($result);
        # Otherwise, check if the user is in the facilitator table at all so we know whether to insert or update later
    } else {
        $result = $db->execute("get-is-facilitator", [$employeeid, 1]);
        $isInFacilitatorTable = pg_fetch_assoc($result);
    }

    # Used to track POST errors
    $errors = [
        "firstname" => false,
        "lastname" => false,
        "primaryphone" => false
    ];

    # Validate form information, display errors if needed
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $firstname = isset($_POST['firstname']) ? $_POST['firstname'] : "";
        $middleinit = isset($_POST['middleinit']) ? $_POST['middleinit'] : "";
        $lastname = isset($_POST['lastname']) ? $_POST['lastname'] : "";
        $primaryphone = isset($_POST['primaryphone']) ? $_POST['primaryphone'] : "";
        if ($isFacilitator) {
            $primaryLang['lang'] = isset($_POST['primaryLanguage']) ? $_POST['primaryLanguage'] : $primaryLang;
            $newLanguages = isset($_POST['addedSecondaryLanguages']) ? $_POST['addedSecondaryLanguages'] : [];
            $removedLanguages = isset($_POST['removedSecondaryLanguages']) ? $_POST['removedSecondaryLanguages'] : [];
        }
        if (hasRole(Role::Admin)) {
            $permissionlevel = isset($_POST['permissionlevel']) ? $_POST['permissionlevel'] : $permissionlevel;
            $setFacilitatorDF = isset($_POST['setFacilitatorDF']) ? $_POST['setFacilitatorDF'] : $setFacilitatorDF;
        }

        # Replace special characters in phone number
        $primaryphone = preg_replace("/[^0-9]/", "", $primaryphone);

        $valid = true;
        $success = true;

        if (empty($firstname) || !ctype_alpha(str_replace(' ', '', $firstname))) {
            $errors['firstname'] = true;
            $errorMsg = "The employee first name could not be updated.";
            $valid = false;
        }
        if (empty($lastname) || !ctype_alpha(str_replace(' ', '', $lastname))) {
            $errors['lastname'] = true;
            $errorMsg = "The employee last name could not be updated.";
            $valid = false;
        }

        if (!empty($primaryphone) && !ctype_digit($primaryphone)) {
            $errors['$primaryphone'] = true;
            $errorMsg = "The employee phone number could not be updated.";
            $valid = false;
        }

        if ($valid) {
            # Update name
            $res1 = $db->query("UPDATE people SET firstname = $1, middleinit = $2, lastname = $3 " .
                "WHERE peopleid = $4", [$firstname, $middleinit, $lastname, $employeeid]);
            if ($res1) {
                $state = pg_result_error_field($res1, PGSQL_DIAG_SQLSTATE);
                if ($state != 0) {
                    $success = false;
                    $errorMsg = "The employee name could not be updated.";
                } else if ($employeeid === $_SESSION['employeeid']) {
                    $_SESSION['username'] = "$firstname $lastname";
                }
            } else {
                $success = false;
                $errorMsg = "The employee name could not be updated.";
            }

            # If the user is a facilitator, update their languages
            if ($isFacilitator) {
                $res2 = $db->query("UPDATE facilitatorlanguage SET lang = $1 " .
                    "WHERE facilitatorid = $2 AND level=$3", [$primaryLang['lang'], $employeeid, "PRIMARY"]);
                if ($res2) {
                    $state = pg_result_error_field($res1, PGSQL_DIAG_SQLSTATE);
                    if ($state != 0) {
                        $success = false;
                        $errorMsg = "The facilitator language could not be updated.";
                    }
                } else {
                    $success = false;
                    $errorMsg = "The facilitator language could not be updated.";
                }

                # If there are new languages, insert them into the facilitator languages table
                foreach ($newLanguages as $l) {
                    $db->query("INSERT INTO facilitatorlanguage VALUES ($1, $2, $3)", [$employeeid, $l, "SECONDARY"]);
                }

                foreach ($removedLanguages as $r) {
                    $db->query("DELETE FROM facilitatorlanguage WHERE facilitatorid = $1 AND lang = $2 AND level = $3",
                        [$employeeid, $r, "SECONDARY"]);
                }

            }

            # Update phone number
            $res3 = $db->query("UPDATE employees SET primaryphone = $1 " .
                "WHERE employeeid = $2", [$primaryphone, $employeeid]);
            if ($res3) {
                $state = pg_result_error_field($res1, PGSQL_DIAG_SQLSTATE);
                if ($state != 0) {
                    $success = false;
                    $errorMsg = "The primary phone could not be updated.";
                }
            } else {
                $success = false;
                $errorMsg = "The primary phone could not be updated.";
            }


            # Update permission level if signed in employee is an Admin, and the employee being updated is not a Superuser
            # Superusers do not have permission levels
            if (hasRole(Role::Admin) && !$isSuperUser) {
                $res4 = $db->query("UPDATE employees SET permissionlevel = $1 " .
                    "WHERE employeeid = $2", [$permissionlevel, $employeeid]);
                if ($res4) {
                    $state = pg_result_error_field($res1, PGSQL_DIAG_SQLSTATE);
                    if ($employeeid == $_SESSION['employeeid']) {
                        $_SESSION['role'] = Role::roleFromPermissionLevel($permissionlevel);
                    }
                    if ($state != 0) {
                        $success = false;
                        $errorMsg = "The permission level could not be updated.";
                    }
                } else {
                    $success = false;
                    $errorMsg = "The permission level could not be updated.";
                }
            }

            # If the logged in user is an Admin and the employee being updated is currently a facilitator,
            # "delete" them from the facilitator table by setting the delete flag to TRUE
            if (hasRole(Role::Admin) && $isFacilitator && $setFacilitatorDF == 1) {
                # Sets DF to TRUE
                $db->query("UPDATE facilitators SET df = TRUE WHERE facilitatorid = $1", [$employeeid]);

                # Removes the employee's languages
                $db->query("DELETE FROM facilitatorlanguage WHERE facilitatorid = $1 AND lang = $2 AND level = 'PRIMARY'", [$employeeid, $primaryLang['lang']]);

                if ($secondaryLang) {
                    foreach ($secondaryLang as $r) {
                        $db->query("DELETE FROM facilitatorlanguage WHERE facilitatorid = $1 AND lang = $2 AND level = 'SECONDARY'",
                            [$employeeid, $r['lang']]);
                    }
                }
            } else if (hasRole(Role::Admin) && !$isFacilitator && $setFacilitatorDF == 0) {
                if ($isInFacilitatorTable) {
                    # Sets DF to FALSE
                    $db->query("UPDATE facilitators SET df = FALSE WHERE facilitatorid = $1", [$employeeid]);
                } else {
                    # Adds employee to the facilitators table
                    $db->query("INSERT INTO facilitators VALUES ($1)", [$employeeid]);
                }
                # Adds default primary language as English
                $db->query("INSERT INTO facilitatorlanguage VALUES ($1, $2, $3)", [$employeeid, "English", "PRIMARY"]);
            }

            # Get new employee information
            $result = $db->execute("get-employee-info", [$employeeid]);
            $employee = pg_fetch_assoc($result);
            extract($employee);

            # Check if employee is a facilitator after POST
            $result = $db->execute("get-is-facilitator", [$employeeid, 0]);
            $isFacilitator = pg_fetch_assoc($result);

            # If the user is a facilitator, get his/her languages
            if ($isFacilitator) {
                $langs = $db->execute("get-languages", []);
                $languages = pg_fetch_all($langs);
                $result = $db->execute("get-facilitator-languages", [$employeeid, "PRIMARY"]);
                $primaryLang = pg_fetch_assoc($result);
                $result = $db->execute("get-facilitator-languages", [$employeeid, "SECONDARY"]);
                $secondaryLang = pg_fetch_all($result);
                # Otherwise, check if the user is in the facilitator table at all
            } else {
                $result = $db->execute("get-is-facilitator", [$employeeid, 1]);
                $isInFacilitatorTable = pg_fetch_assoc($result);
            }
        } else {
            $success = false;
        }

        # If POST is successful, display a notification on account-settings/id page
        if ($success) {
            $note['title'] = 'Success!';
            $note['msg'] = 'The user has been updated.';
            $note['type'] = 'success';
            $_SESSION['notification'] = $note;
            header("Location: /account-settings/" . $employeeid);
            die();
        }

    }
    include('header.php');
    ?>
    <div class="page-wrapper">
        <div class="jumbotron form-wrapper mb-3">
            <?php
            # Display error message if any updates failed
            if (isset($success)) {
                if (!$success) {
                    $notification = new Notification('Error!', isset($errorMsg) ? $errorMsg : ('Uh oh! an error occurred and the account information wasn\'t updated.'), 'danger');
                    $notification->display();
                }
            }
            ?>
            <form class="form" method="post" action="<?= $_SERVER['REQUEST_URI'] ?>" novalidate>
                <h4>Information</h4>
                <div class="form-group row">
                    <div class="col-sm-5">
                        <label for="class-name" class=""><b>First Name</b></label>
                        <input type="text" class="form-control"
                               value="<?= ucwords($firstname) ?>" id="employee-firstname" name="firstname" required>
                        <div class="invalid-feedback">
                            First Name cannot be empty.
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <label for="class-name" class=""><b>Middle</b></label>
                        <input type="text" class="form-control"
                               value="<?= ucwords($middleinit) ?>" id="employee-middleinit" name="middleinit">
                    </div>
                    <div class="col-sm-5">
                        <label for="class-name" class=""><b>Last Name</b></label>
                        <input type="text" class="form-control"
                               value="<?= ucwords($lastname) ?>" id="employee-lastname" name="lastname" required>
                        <div class="invalid-feedback">
                            Last Name cannot be empty.
                        </div>
                    </div>
                </div>
                <?php
                # If the employee is a facilitator, display associated languages (defaults to English upon creation)
                if ($isFacilitator) { ?>
                    <div class="form-group">
                        <label for="employee-Languages" style="margin-bottom: -10px;"><b>Languages</b></label>
                        <div class="form-group" id="primaryLanguageForm">
                            <table class="table table-responsive table-striped table-sm" style="margin-bottom: 0px;">
                                <thead>
                                <p style="margin-top: 10px; margin-bottom: 0px;">Primary</p>
                                <hr style="margin-top: 6px; margin-bottom: 4px;">
                                </thead>
                                <tbody>
                                <tr class="languages">
                                    <?php
                                    # All users should have a primary language, but check just in case
                                    if ($primaryLang) { ?>
                                        <td class="align-middle">
                                            <span class="language-span" id="employee-primaryLanguage"
                                                  style='padding-left: 10px;'><?= $primaryLang['lang'] ?></span>
                                        </td>
                                    <?php } else { ?>
                                        <td class="align-middle" id="employee-primaryLanguage">
                                            <span class="language-span" style='padding-left: 10px;'>No primary language</span>
                                        </td>
                                    <?php } ?>
                                    <td class="text-right align-middle">
                                        <select class="form-control" id="primaryLanguage-selector"
                                                style="margin-left: 200px; height: 45px; width: 220px;">
                                            <?php if ($languages) { ?>
                                                <option selected disabled>Choose a language...</option>
                                                <?php foreach ($languages as $language) { ?>
                                                    <option value="<?= $language['lang'] ?>"><?= $language['lang'] ?></option>
                                                <?php } } else { ?>
                                                <option selected disabled>No languages available</option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <input type="hidden" name="primaryLanguage" value="<?= $primaryLang['lang'] ?>"/>
                                    <td class="text-right align-middle">
                                        <button type="button" id="update-lang-btn" class="btn cpca btn-sm"
                                                style="width: 125px;" disabled>Update Language
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div style="margin-bottom: -10px;" class="form-group" id="secondaryLanguageForm">
                            <table class="table table-responsive table-striped table-sm">
                                <thead>
                                <p style="margin-top: 10px; margin-bottom: 0px;">Secondary</p>
                                <hr style="margin-top: 6px; margin-bottom: 4px;">
                                </thead>
                                <tbody>
                                <?php
                                if ($secondaryLang) {
                                    foreach ($secondaryLang as $language) { ?>
                                        <tr class="languages">
                                            <td class="align-middle" style="height: 54px; vertical-align: middle;">
                                                <span class="secondaryLanguage language-span"
                                                      style='padding-left: 10px;'><?= $language['lang'] ?></span>
                                            </td>
                                            <td class="text-right align-middle">
                                                <button type='button' class='btn btn-outline-danger btn-sm'
                                                        onclick='removeLanguage(this)'
                                                        style='width:125px; padding: .25rem;'>Remove Language
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr class="languages">
                                        <td class="align-middle" style="height: 54px; vertical-align: middle;">
                                                <span class="secondaryLanguage language-span" id="no-secondary-lang"
                                                      style='padding-left: 10px;'>
                                                    <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                                                    <i>No secondary languages</i>
                                                </span>
                                        </td>
                                        <td class="text-right">
                                            <span></span>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr id="add-language-row" class="languages">
                                    <td class="text-right align-middle languages">
                                        <select class="form-control" id="secondaryLanguage-selector"
                                                style="margin-left: 271px; height: 45px; width: 220px;">
                                            <?php if ($languages) { ?>
                                                <option selected disabled>Choose a language...</option>
                                                <?php foreach ($languages as $language) { ?>
                                                    <option value="<?= $language['lang'] ?>"><?= $language['lang'] ?></option>
                                                <?php } } else { ?>
                                                <option selected disabled>No languages available</option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td class="text-right align-middle languages">
                                        <button type="button" id="add-secondary-lang-btn" class="btn cpca btn-sm"
                                                disabled style="width: 125px;">Add Language
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php }
                # If the logged in user has an admin/superuser role, allow them to change user permission levels and add/remove facilitator permissions
                if (hasRole(Role::Admin)) { ?>
                    <div class="form-group">
                        <label><b>Permissions & Abilities</b></label>
                        <?php if (!$isSuperUser) { ?>
                            <select class="form-control" name="permissionlevel" id="select-permissionlevel"
                                    onchange="updatepermissionlevel()" style="margin-bottom: 10px">
                                <?php foreach($permissions as $permission) { ?>
                                    <option <?php if ($permissionlevel == $permission['permission']) { ?> selected <?php } ?> value="<?=$permission['permission']?>"><?=$permission['permission']?></option>
                                <?php } ?>
                            </select>
                            <input type="hidden" class="form-control"
                                   value="<?= $permissionlevel ?>" id="employee-permissionlevel" name="permissionlevel"/>
                        <?php } ?>
                        <?php
                        # Add <br> to make it look pretty
                        if ($isSuperUser) { ?> <br> <?php } ?>
                        <label for="isFacilitator"> Is this employee a facilitator? </label>
                        <div class="btn-group" data-toggle="buttons" style="margin-left: 10px;">
                            <label class="btn btn-secondary" id="isFacilitator">
                                <input type="radio" name="setFacilitatorDF" value="0" autocomplete="off"> Yes
                            </label>
                            <label class="btn btn-secondary" id="isNotFacilitator">
                                <input type="radio" name="setFacilitatorDF" value="1" autocomplete="off"> No
                            </label>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="form-group">
                        <label for="class-desc" class=""><b>Type</b></label>
                        <input type="text" class="form-control"
                               value="<?= $permissionlevel ?>" id="employee-permissionlevel" name="permissionlevel"
                               disabled/>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <label for="class-desc" class=""><b>Phone Number</b></label>
                    <input type="text" class="form-control mask-phone"
                           value="<?= $primaryphone ?>" id="employee-primaryphone" name="primaryphone"/>
                </div>
                <div class="form-footer submit">
                    <button type="submit" class="btn cpca">Submit New Changes</button>
                    <button type="button" class="btn btn-secondary" onclick="goBack()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        /**
         * Function to remove a language from the secondary languages table.
         * @param el - the corresponding language row 'Remove' button
         */
        function removeLanguage(el) {
            // Remove language from table
            var language = $($(el).closest('td').prev('td').children('span').get(0)).text();
            $(el).closest('tr').remove();

            // Add language to select list
            $("#secondaryLanguage-selector").append(
                '<option value="' + language + '">' + language + '</option>'
            );

            // Add language to primary select list
            if($('#primaryLanguage-selector').find('option[value="' + language + '"]').length == 0 && $('#employee-primaryLanguage').text() != language) {
                $("#primaryLanguage-selector").append(
                    '<option value="' + language + '">' + language + '</option>'
                );
            }

            const hiddenEl = $("#secondaryLanguageForm").find('input[type="hidden"][value="' + language + '"]');
            // Add hidden element for form submission (if not added by form)
            if (hiddenEl.length === 0)
                $("#secondaryLanguageForm").append('<input type="hidden" name="removedSecondaryLanguages[]" value="' + language + '" />');

            // Remove hidden element (if added)
            hiddenEl.remove();

            // Add "no secondary languages" column if empty
            if ($(".secondaryLanguage").length == 0) {
                $("<tr class='language-row'>" +
                    "<td class='align-middle' style='height: 54px; vertical-align: middle;'>" +
                    " <span class='secondaryLanguage language-span' id='no-secondary-lang' style='padding-left: 10px;'>" +
                    "<i class='fa fa-exclamation-circle' aria-hidden='true'></i> <i>User has no secondary languages</i></span> " +
                    "</td>" +
                    "<td class='text-right'>" +
                    "<span></span> " +
                    "</tr>").insertBefore("#add-language-row");
            }

            // Enable buttons if there is something to select
            if ($('#primaryLanguage-selector').find('option').length > 1)
                $('#update-lang-btn').attr('disabled', false);

            if ($('#secondaryLanguage-selector').find('option').length > 1)
                $('#add-secondary-lang-btn').attr('disabled', false);
        }

        function addLanguage() {
            // If there were no secondary langauges at first, remove that row
            if ($("#no-secondary-lang") != null) {
                $("#no-secondary-lang").closest('tr').remove();
            }

            const val = $("#secondaryLanguage-selector").val();

            // Add language to table
            $("<tr class='language-row'>" +
                "<td class='align-middle' style='height: 54px; vertical-align: middle;'> " +
                "<span class='secondaryLanguage language-span' style='padding-left: 10px;'>" + val + "</span>" +
                "</td>" +
                "<td class='align-middle text-right'>" +
                "<button type='button' class='btn btn-outline-danger btn-sm' " +
                "onclick='removeLanguage(this)' style='width:125px; padding: .25rem;'>Remove Language</button>" +
                "</td>" +
                "</tr>").insertBefore("#add-language-row");


            // Remove from select lists
            $('#secondaryLanguage-selector').find('option[value="' + val + '"]').remove();
            $('#primaryLanguage-selector').find('option[value="' + val + '"]').remove();

            const hiddenEl = $("#secondaryLanguageForm").find('input[type="hidden"][value="' + val + '"]');
            // Add hidden element for form submission (if not deleted by form)
            if (hiddenEl.length === 0)
                $("#secondaryLanguageForm").append('<input type="hidden" name="addedSecondaryLanguages[]" value="' + val + '" />');

            // Remove hidden element (if deleted)
            hiddenEl.remove();

            // Disable buttons if there is nothing to select
            if ($('#primaryLanguage-selector').find('option').length <= 1)
                $('#update-lang-btn').attr('disabled', true);

            if ($('#secondaryLanguage-selector').find('option').length <= 1)
                $('#add-secondary-lang-btn').attr('disabled', true);
        }

        function updateLang() {
            // Get the original value of the row and the new value from the selector
            var originalVal = $('#employee-primaryLanguage').text();
            const val = $('#primaryLanguage-selector').val();

            // Add the original value back into the selects
            $('#primaryLanguage-selector').append($('<option/>', {
                value: originalVal,
                text: originalVal
            }));

            if($('#secondaryLanguage-selector').find('option[value="' + originalVal + '"]').length == 0) {
                $('#secondaryLanguage-selector').append($('<option/>', {
                    value: originalVal,
                    text: originalVal
                }));
            }

            // Remove the new value from the select and to the table
            $('#primaryLanguage-selector').find('option[value="' + val + '"]').remove();
            $('#secondaryLanguage-selector').find('option[value="' + val + '"]').remove();
            $('#employee-primaryLanguage').html(val);

            const hiddenEl = $("#primaryLanguageForm").find('input[type="hidden"]');
            // Add hidden element for form submission if not already there, otherwise just change the value
            if (hiddenEl.length === 0)
                $("#primaryLanguageForm").append('<input type="hidden" name="primaryLanguage" value="' + val + '" />');
            else
                $(hiddenEl).attr("value", val);

            // Disable buttons if there is nothing to select
            if ($('#primaryLanguage-selector').find('option').length <= 1)
                $('#update-lang-btn').attr('disabled', true);

            if ($('#secondaryLanguage-selector').find('option').length <= 1)
                $('#add-secondary-lang-btn').attr('disabled', true);

        }

        function updatepermissionlevel() {
            // Get new value from the select
            const newVal = $("#select-permissionlevel").val();
            // Change the value of the input
            $('#employee-permissionlevel').attr("value", newVal);
        }

        $(function () {
            // If the user is a facilitator, click yes on the "is facilitator?" button
            <?php if ($isFacilitator && hasRole(Role::Admin)) { ?>
            document.getElementById("isFacilitator").click();
            // Otherwise, click no
            <?php } else if (!$isFacilitator && hasRole(Role::Admin)) { ?>
            document.getElementById("isNotFacilitator").click();
            <?php } ?>

            $('#update-lang-btn').click(updateLang);
            $('#add-secondary-lang-btn').click(addLanguage);

            // Remove the initial primary language value from the selectors
            var val = $('#employee-primaryLanguage').text();
            $('#primaryLanguage-selector').find('option[value="' + val + '"]').remove();
            $('#secondaryLanguage-selector').find('option[value="' + val + '"]').remove();
            // Remove the initial secondary language values from the selector
            $(".secondaryLanguage").each(function () {
                val = $(this).text();
                $('#secondaryLanguage-selector').find('option[value="' + val + '"]').remove();
                $('#primaryLanguage-selector').find('option[value="' + val + '"]').remove();
            });

            // Enable secondary button if value is selected
            $('#secondaryLanguage-selector').on('change', function () {
                const val = $("#secondaryLanguage-selector").val();
                if (val) {
                    $('#add-secondary-lang-btn').attr('disabled', false);
                }
            });

            // Enable primary button if value is selected
            $('#primaryLanguage-selector').on('change', function () {
                const val = $("#primaryLanguage-selector").val();
                if (val) {
                    $('#update-lang-btn').attr('disabled', false);
                }
            });

            $('.mask-phone').mask('(000) 000-0000');
        });
    </script>
    <?php
    include('footer.php');
} ?>