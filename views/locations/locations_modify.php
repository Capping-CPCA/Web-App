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
 * @version 0.1.6
 * @since 0.1
 */

global $params, $db;
$isEdit = $params[0] == 'edit';
array_shift($params);

# Get site name from params
$sitename = urldecode(rawurldecode(implode('/', $params)));

# Prepare SQL statements for later use
$db->prepare("get_site", "SELECT * FROM sites WHERE sitename = $1");
$db->prepare("get_addr", "SELECT * FROM addresses WHERE addressid = $1");
$db->prepare("get_zip", "SELECT * FROM zipcodes WHERE zipcode = $1");

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

    # Address
    $id = $site['addressid'];
    $result = $db->execute("get_addr", [$id]);
    if (pg_num_rows($result) > 0) {
        $address = pg_fetch_assoc($result);
    }
    pg_free_result($result);

    # Zip
    if (isset($address)) {
        $zip = $address['zipcode'];
        $result = $db->execute("get_zip", [$zip]);
        if (pg_num_rows($result) > 0) {
            $zipcode = pg_fetch_assoc($result);
        }
        pg_free_result($result);
    }
}

$name = isset($site) ? htmlentities($site['sitename']) : '';
$type = isset($site) ? htmlentities($site['sitetype']) : '';
$street_address = isset($address) ? htmlentities(trim($address['addressnumber'] . ' ' . $address['street'])) : '';
$apartment = isset($address) ? htmlentities(trim($address['aptinfo'])) : '';
$city = isset($zipcode) ? htmlentities(trim($zipcode['city'])) : '';
$state = isset($zipcode) ? htmlentities($zipcode['state']) : '';
$zip = isset($zipcode) ? htmlentities($zipcode['zipcode']) : '';

# Used to track POST errors
$errors = [
    "name" => false,
    "type" => false,
    "address" => false,
    "apt" => false,
    "city" => false,
    "state" => false,
    "zip" => false
];

# Validate form information, display errors if needed
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $oldName = html_entity_decode($name);
    $name = isset($_POST['name']) ? html_entity_decode($_POST['name']) : $name;
    $type = isset($_POST['type']) ? html_entity_decode($_POST['type']) : $type;
    $street_address = isset($_POST['addr']) ? html_entity_decode($_POST['addr']) : $street_address;
    $apartment = isset($_POST['apt']) ? html_entity_decode($_POST['apt']) : $apartment;
    $city = isset($_POST['city']) ? html_entity_decode($_POST['city']) : $city;
    $state = isset($_POST['state']) ? html_entity_decode($_POST['state']) : $state;
    $zip = isset($_POST['zip']) ? html_entity_decode($_POST['zip']) : $zip;

    // Logic for parsing the address into the address number and street name.
    $street_num = NULL;
    $street_name = NULL;

    if ($street_address !== '') {
        $address_info = explode(" ", $street_address);

        // Loop to parse through the address array
        for ($i = 0; $i < sizeOf($address_info); $i++) {
            if ($i === 0) {
                if ($address_info[$i] !== "") {
                    if (is_numeric($address_info[$i])) {
                        $street_num = $address_info[$i];
                    } else {
                        $street_name .= " ".$address_info[$i];
                    }
                }
            } else {
                $street_name .= $address_info[$i] . " ";
            }
        }
    }

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
            $res = $db->query("SELECT zipCodeSafeInsert($1::VARCHAR, $2::TEXT, $3::STATES)", [$zip, $city, $state]);
            $id = isset($address) ? $address['addressid'] : -1; // -1 means address doesn't exist
            $res = $db->query("INSERT INTO Addresses(addressNumber, street, aptInfo, zipCode) VALUES ($1, $2, $3, $4) RETURNING addressid", [
                $street_num, $street_name, $apartment, $zip
            ]);
            $addrID = pg_fetch_assoc($res)['addressid'];
            $res = $db->query("UPDATE sites SET sitetype = $1, addressid = $3 ".
                "WHERE sitename = $2", [$type,$oldName,$addrID]);
            if ($id !== -1) {
                $res = $db->query("DELETE FROM addresses WHERE addressid = $1", [$id]);
            }
        } else {
            $res = $db->query("SELECT zipCodeSafeInsert($1::VARCHAR, $2::TEXT, $3::STATES)", [$zip, $city, $state]);
            $res = $db->query("INSERT INTO Addresses(addressNumber, street, aptInfo, zipCode) VALUES ($1, $2, $3, $4) RETURNING addressid", [
                    $street_num, $street_name, $apartment, $zip
            ]);
            $id = pg_fetch_assoc($res)['addressid'];
            $res = $db->query("INSERT INTO sites VALUES ($1, $2, $3)", [$name,$type,$id]);
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
                die(pg_result_error($res));
            }
        } else {
            $success = false;
        }

        $site = pg_fetch_assoc($db->execute("get_site", [$name]));
    } else {
        $success = false;
        $errorMsg = "There are errors in the form.";
    }

    if ($success) {
        $note['title'] = 'Success!';
        $note['msg'] = 'The curriculum has been ' . ($isEdit ? 'updated' : 'created') . '.';
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
                    isset($errorMsg) ? $errorMsg : ('Uh oh! An error occurred and the location wasn\'t ' .
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
                       value="<?= $name ?>" id="class-name" name="name" required <?= $isEdit ? 'disabled' : '' ?> />
                <div class="invalid-feedback">
                    Invalid characters found in name.
                </div>
            </div>
            <div class="form-group">
                <label for="type-select" class="<?= $errors['type'] ? 'text-danger' : '' ?>">Program Type</label>
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

            <br />
            <h4>Address</h4>
            <div class="row">
                <!-- Street Address -->
                <div class="form-group col-9">
                    <label for="addr" class="<?= $errors['address'] ? 'text-danger' : '' ?>">Street Address</label>
                    <input type="text" class="form-control <?= $errors['address'] ? 'is-invalid' : '' ?>"
                           value="<?= $street_address ?>" id="addr" name="addr" />
                    <div class="invalid-feedback">
                        Invalid characters found in address.
                    </div>
                </div>
                <!-- Apt Number -->
                <div class="form-group col-3">
                    <label for="apt" class="<?= $errors['apt'] ? 'text-danger' : '' ?>">Apartment</label>
                    <input type="text" class="form-control <?= $errors['apt'] ? 'is-invalid' : '' ?>"
                           value="<?= $apartment ?>" id="apt" name="apt"/>
                    <div class="invalid-feedback">
                        Invalid characters found in apartment name.
                    </div>
                </div>
            </div>

            <!-- City -->
            <div class="form-group">
                <label for="city" class="<?= $errors['city'] ? 'text-danger' : '' ?>">City</label>
                <input type="text" class="form-control <?= $errors['city'] ? 'is-invalid' : '' ?>"
                       value="<?= $city ?>" id="city" name="city"/>
                <div class="invalid-feedback">
                    Invalid characters found in city name.
                </div>
            </div>

            <div class="row">
                <!-- State -->
                <div class="form-group col-9">
                    <label for="state" class="<?= $errors['state'] ? 'text-danger' : '' ?>">State</label>
                    <select class="form-control" id="state" name="state" >
                        <option value="" selected="selected">Choose a state</option>
                        <?php
                        $res = $db->query("SELECT unnest(enum_range(NULL::states)) AS type", []);
                        while ($enumtype = pg_fetch_assoc($res)) {
                            $t = $enumtype ['type'];
                            ?>
                            <option value="<?= $t ?>" <?= (isset($state) && $state == $t) ? "selected" : "" ?>><?= $t ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>

                <!-- Zip -->
                <div class="form-group col-3">
                    <label for="zip" class="<?= $errors['zip'] ? 'text-danger' : '' ?>">ZIP</label>
                    <input type="text" class="form-control mask-zip <?= $errors['zip'] ? 'is-invalid' : '' ?>"
                           value="<?= $zip ?>" id="zip" name="zip"/>
                    <div class="invalid-feedback">
                        Invalid characters found in zip code.
                    </div>
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
