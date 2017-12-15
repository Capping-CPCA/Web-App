<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displays details about a location.
 *
 * This page displays the detailed information
 * about a specific location. From here the user
 * can also edit or delete the entry.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.3.2
 * @since 0.1
 */

global $params, $db;
array_shift($params);

# Get topic name from params
$sitename = urldecode(rawurldecode(implode('/', $params)));

$result = $db->query("SELECT * FROM sites WHERE sitename = $1", [$sitename]);

# If no results, class doesn't exist, redirect
if (pg_num_rows($result) == 0) {
    header('Location: /locations');
    die();
}

$site = pg_fetch_assoc($result);
pg_free_result($result);

# Address
$id = $site['addressid'];
$result = $db->query("SELECT * FROM addresses WHERE addressid = $1", [$id]);
if (pg_num_rows($result) == 1) {
    $address = pg_fetch_assoc($result);
    $zip = $address['zipcode'];
}
pg_free_result($result);

if (isset($zip)) {
    $result = $db->query("SELECT * FROM zipcodes WHERE zipcode = $1", [$zip]);
    if (pg_num_rows($result) > 0) {
        $zipcode = pg_fetch_assoc($result);
    }
    pg_free_result($result);
}

$full_address = (isset($address['addressnumber']) ? ($address['addressnumber'].' ') : '') .
    (isset($address['street']) ? $address['street'] : '');
$full_address = htmlentities($full_address);

if (isset($zip)) {
    $location = $zipcode['city'] . ' ' . $zipcode['state'] . ' ' . $zipcode['zipcode'];
    $location = htmlentities($location);
}

include('header.php');
?>
    <div style="width: 100%">
        <button class="cpca btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
        <div class="form-wrapper card view-card">
            <h4 class="card-header text-left">
                <?= htmlentities($site['sitename']) ?>
                <?php if (hasRole(Role::Coordinator)) { ?>
                    <div class="float-right">
                        <a href="/locations/edit/<?= implode('/', $params) ?>"><button class="btn btn-outline-secondary btn-sm">Edit</button></a>
                        <a href="/locations/delete/<?= implode('/', $params) ?>"><button class="btn btn-outline-danger btn-sm">Delete</button></a>
                    </div>
                <?php } ?>
            </h4>
            <div class="card-body d-flex justify-content-center flex-column">
                <h4>Information</h4>
                <div class="d-flex justify-content-center">
                    <div class="display-stack">
                        <div class="display-top"><?= $site['sitetype'] ?></div>
                        <div class="display-split"></div>
                        <div class="display-bottom">Location Type</div>
                    </div>
                </div>

                <h4>Address</h4>
                <div class="ml-2">
                    <p class="mb-1"><b>Street: </b><?= empty($full_address) ? 'Not specified' : $full_address ?></p>
                    <p class="mb-1"><b>Apartment: </b><?= empty($address['aptinfo']) ? 'Not specified' : htmlentities($address['aptinfo']) ?></p>
                    <p class="mb-1"><b>Location: </b><?= isset($location) ? $location : 'Not specified'  ?></p>
                </div>
            </div>
        </div>
    </div>
<?php
include('footer.php');