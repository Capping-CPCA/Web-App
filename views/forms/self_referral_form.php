<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Self-Referral Form that participants fill out for PEP programs.
 *
 * The Self-Referral Form is a form that is filled out by a participant before enrolling in a PEP parenting program.
 * The form is submitted when the participant takes the initiative to refer themselves to a program.
 * Since this is an in-house application, somebody from the CPCA will have to manually input the form
 * in order for the Self-Referral form to make its way into the system.
 *
 * @author Stephen Bohner
 * @copyright 2017 Marist College
 * @version 0.3.3
 * @since 0.3.2
 */

authorizedPage();
global $db, $params, $route, $view;

// Checks if the page has a people ID associated for updating/editing forms.
if (isset($params[0]) && isset($params[1]) && isset($params[2])) {

    $peopleid = $params[1];
    $formID = $params[2];

    // SELECT FROM VIEWS TO POPULATE FIELDS
    $self_referral_view = $db->query("SELECT * FROM SelfReferralInfo WHERE formID = $1;", [$formID]);
    $self_referral_view_result = pg_fetch_assoc($self_referral_view);
    // First Card (Participant Information)
    $self_pers_firstname_result = $self_referral_view_result['firstname'];
    $self_pers_lastname_result = $self_referral_view_result['lastname'];
    $self_pers_middlein_result = $self_referral_view_result['middleinit'];

    $self_pers_dob_edit = $db->query("SELECT SelfReferralInfo.PDoB FROM SelfReferralInfo WHERE formID = $1;", [$formID]);
    $self_pers_dob_result = pg_fetch_result($self_pers_dob_edit, 0);

    $self_pers_race_edit = $db->query("SELECT SelfReferralInfo.PRace FROM SelfReferralInfo WHERE formID = $1;", [$formID]);
    $self_pers_race_result = pg_fetch_result($self_pers_race_edit, 0);

    $self_pers_sex_edit = $db->query("SELECT SelfReferralInfo.PSex FROM SelfReferralInfo WHERE formID = $1;", [$formID]);
    $self_pers_sex_result = pg_fetch_result($self_pers_sex_edit, 0);

    $self_pers_street_num_edit = $db->query("SELECT Addresses.addressNumber FROM Addresses WHERE addressID = $1;", [$formID]);
    $self_pers_street_num_result = pg_fetch_result($self_pers_street_num_edit, 0);

    $self_pers_street_name_edit = $db->query("SELECT Addresses.street FROM Addresses WHERE addressID = $1;", [$formID]);
    $self_pers_street_name_result = pg_fetch_result($self_pers_street_name_edit, 0);

    $self_pers_zip_edit = $db->query("SELECT Addresses.zipCode FROM Addresses WHERE addressID = $1;", [$formID]);
    $self_pers_zip_result = pg_fetch_result($self_pers_zip_edit, 0);

    $self_pers_state_edit = $db->query("SELECT ZipCodes.state FROM ZipCodes WHERE zipCode = $1;", [$self_pers_zip_result]);
    $self_pers_state_result = pg_fetch_result($self_pers_state_edit, 0);

    $self_pers_city_edit = $db->query("SELECT ZipCodes.city FROM ZipCodes WHERE zipCode = $1;", [$self_pers_zip_result]);
    $self_pers_city_result = pg_fetch_result($self_pers_city_edit, 0);

    $self_apt_info_edit = $db->query("SELECT Addresses.aptinfo FROM Addresses WHERE addressID = $1;", [$formID]);
    $self_apt_info_result = pg_fetch_result($self_apt_info_edit, 0);

    $self_pers_phone_edit = $db->query("SELECT FormPhoneNumbers.phoneNumber FROM FormPhoneNumbers WHERE formID = $1 AND phoneType = 'Primary';", [$formID]);
    $self_pers_phone_result = pg_fetch_result($self_pers_phone_edit, 0);

    // Second Card (Additional Information)
    $self_involvement_edit = $db->query("SELECT SelfReferralInfo.hasInvolvementCPS FROM SelfReferralInfo WHERE formID = $1;", [$formID]);
    $self_involvement_result = pg_fetch_result($self_involvement_edit, 0);

    $self_attended_edit = $db->query("SELECT SelfReferralInfo.hasAttendedPEP FROM SelfReferralInfo WHERE formID = $1;", [$formID]);
    $self_attended_result = pg_fetch_result($self_attended_edit, 0);

    $self_ref_source_edit = $db->query("SELECT SelfReferralInfo.referralSource FROM SelfReferralInfo WHERE formID = $1;", [$formID]);
    $self_ref_source_result = pg_fetch_result($self_ref_source_edit, 0);

    $reason_edit = $db->query("SELECT SelfReferralInfo.reasonAttendingPEP FROM SelfReferralInfo WHERE formID = $1;", [$formID]);
    $reason_result = pg_fetch_result($reason_edit, 0);

    // Third Card (Office Information)
    $self_office_firstCall_edit = $db->query("SELECT SelfReferralInfo.dateFirstCall FROM SelfReferralInfo WHERE formID = $1;", [$formID]);
    $self_office_firstCall_result = pg_fetch_result($self_office_firstCall_edit, 0);

    $self_office_returnedCall_edit = $db->query("SELECT SelfReferralInfo.returnClientCallDate FROM SelfReferralInfo WHERE formID = $1;", [$formID]);
    $self_office_returnedCall_result = pg_fetch_result($self_office_returnedCall_edit, 0);

    $self_tentative_start_edit = $db->query("SELECT SelfReferralInfo.tentativeStartDate FROM SelfReferralInfo WHERE formID = $1;", [$formID]);
    $self_tentative_start_result = pg_fetch_result($self_tentative_start_edit, 0);

    $self_letter_mailed_edit = $db->query("SELECT SelfReferralInfo.introLetterMailedDate FROM SelfReferralInfo WHERE formID = $1;", [$formID]);
    $self_letter_mailed_result = pg_fetch_result($self_letter_mailed_edit, 0);

    $self_assigned_to_edit = $db->query("SELECT SelfReferralInfo.classAssignedTo FROM SelfReferralInfo WHERE formID = $1;", [$formID]);
    $self_assigned_to_result = pg_fetch_result($self_assigned_to_edit, 0);

    $notes_edit = $db->query("SELECT SelfReferralInfo.notes FROM SelfReferralInfo WHERE formID = $1;", [$formID]);
    $notes_result = pg_fetch_result($notes_edit, 0);
    // END OF VIEW QUERIES
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /*************
     * VARIABLES *
     *************/

    $form_type = "self referral";

    // First Card (Participant Information)
    $self_pers_firstname = !empty($_POST['self_pers_firstname']) ? trim($_POST['self_pers_firstname']) : NULL;
    $self_pers_lastname = !empty($_POST['self_pers_lastname']) ? trim($_POST['self_pers_lastname']) : NULL;
    $self_pers_middlein = !empty($_POST['self_pers_middlein']) ? trim($_POST['self_pers_middlein']) : NULL;
    $self_pers_dob = !empty($_POST['self_pers_dob']) ? $_POST['self_pers_dob'] : NULL;
    $self_pers_race = !empty($_POST['self_pers_race']) ? $_POST['self_pers_race'] : NULL;
    $self_pers_sex = !empty($_POST['self_pers_sex']) ? $_POST['self_pers_sex'] : NULL;
    $self_pers_address = !empty($_POST['self_pers_address']) ? trim($_POST['self_pers_address']) : NULL;

    // Logic for parsing the address into the address number and street name.
    $self_address_info = explode(" ", $self_pers_address);
    $self_pers_street_num = NULL;
    $self_pers_street_name = NULL;

    // Loop to parse through the address array ($self_address_info)
    for ($i = 0; $i < sizeOf($self_address_info); $i++) {
        if ($i === 0) {
            if ($self_address_info[$i] !== "") {
                if (is_numeric($self_address_info[$i])) {
                    $self_pers_street_num = $self_address_info[$i];
                } else {
                    $self_pers_street_name .= " " . $self_address_info[$i];
                }
            }
        } else {
            $self_pers_street_name .= $self_address_info[$i] . " ";
        }
    }

    $self_pers_zip = !empty($_POST['self_pers_zip']) ? $_POST['self_pers_zip'] : 12601; // Default Zipcode is Poughkeepsie.
    $self_pers_state = !empty($_POST['self_pers_state']) ? $_POST['self_pers_state'] : "New York";
    $self_pers_city = !empty($_POST['self_pers_city']) ? trim($_POST['self_pers_city']) : "Poughkeepsie";
    $self_apt_info = !empty($_POST['self_apt_info']) ? trim($_POST['self_apt_info']) : NULL;
    $self_pers_phone = !empty($_POST['self_pers_phone']) ? phoneStrToNum($_POST['self_pers_phone']) : NULL;

    // Second Card (Additional Information)
    // This is logic for if a user does NOT select Yes or No, the result in the DB will be NULL.
    if (!empty($_POST['self_involvement'])) {
        $self_involvement = $_POST['self_involvement'] === "Yes" ? 1 : 0;
    } else {
        $self_involvement = NULL;
    }
    // This is logic for if a user does NOT select Yes or No, the result in the DB will be NULL.
    if (!empty($_POST['self_attended'])) {
        $self_attended = $_POST['self_attended'] === "Yes" ? 1 : 0;
    } else {
        $self_attended = NULL;
    }

    $self_ref_source = !empty($_POST['self_ref_source']) ? $_POST['self_ref_source'] : NULL;
    $reason = !empty($_POST['reason']) ? trim($_POST['reason']) : NULL;

    // Third Card (Office Information)
    $self_office_firstCall = !empty($_POST['self_office_firstCall']) ? trim($_POST['self_office_firstCall']) : NULL;
    $self_office_returnedCall = !empty($_POST['self_office_returnedCall']) ? trim($_POST['self_office_returnedCall']) : NULL;
    $self_tentative_start = !empty($_POST['self_tentative_start']) ? trim($_POST['self_tentative_start']) : NULL;
    $self_letter_mailed = !empty($_POST['self_letter_mailed']) ? trim($_POST['self_letter_mailed']) : NULL;
    $self_assigned_to = !empty($_POST['self_assigned_to']) ? trim($_POST['self_assigned_to']) : NULL;
    $notes = !empty($_POST['notes']) ? trim($_POST['notes']) : NULL;
    $eID = $_SESSION['employeeid'];



    if (isset($params[0]) && isset($params[1]) && isset($params[2])) {

        /*************
         *  UPDATES  *
         *************/

        $pID = $params[1];
        $fID = $params[2];

        $updatePeopleResult = $db->query("UPDATE
                                    People
                                    SET
                                    firstName = $1,
                                    lastName = $2,
                                    middleInit = $3
                                    WHERE 
                                    peopleID = $4;", [$self_pers_firstname, $self_pers_lastname, $self_pers_middlein, $pID]);

        $updateParticipantResult = $db->query("UPDATE 
                                    Participants
                                    SET 
                                    dateOfBirth = $1,
                                    race = $2,
                                    sex = $3
                                    WHERE 
                                    participantID = $4;", [$self_pers_dob, $self_pers_race, $self_pers_sex, $pID]);

        $newZip = $db->query("INSERT INTO ZipCodes VALUES($1, $2, $3);", [$self_pers_zip, $self_pers_city, $self_pers_state]);

        $updateAddressResult = $db->query("UPDATE
                                    Addresses
                                    SET
                                    addressNumber = $1,
                                    aptInfo = $2,
                                    street = $3,
                                    zipCode = $4
                                    WHERE
                                    addressID = $5;", [$self_pers_street_num, $self_apt_info, $self_pers_street_name, $self_pers_zip, $fID]);

        $updatePhoneResult = $db->query("UPDATE 
                                    FormPhoneNumbers
                                    SET 
                                    phoneNumber = $1,
                                    phoneType = $2
                                    WHERE
                                    formID = $3", [$self_pers_phone, 'Primary', $fID]);

        $updateSelfReferralResult = $db->query("UPDATE 
                            SelfReferral
                                    SET 
                                    referralSource = $1,
                                    hasInvolvementCPS = $2,
                                    hasAttendedPEP = $3,
                                    reasonAttendingPEP = $4,
                                    dateFirstCall = $5,
                                    returnClientCallDate = $6,
                                    tentativeStartDate = $7,
                                    classAssignedTo = $8,
                                    introLetterMailedDate = $9,
                                    Notes = $10
                                    WHERE
                                    selfReferralID = $11;", [$self_ref_source,
            $self_involvement, $self_attended, $reason, $self_office_firstCall,
            $self_office_returnedCall, $self_tentative_start, $self_assigned_to,
            $self_letter_mailed, $notes, $fID]);

        header('Location: /ps-view-participant/'.$pID);
        die();

    } else {

        /*********************
         * STORED PROCEDURES *
         *********************/

        // Stored Procedures
        // Gets the participant ID related to the person who is filling out the form to associate it with the form ID.

        $pIDResult = checkForDuplicates($db, $self_pers_firstname, $self_pers_lastname, $self_pers_middlein);
        // Inserts self referral form data into DB and associates the form with a participant ID.
        $result = $db->query("SELECT addSelfReferral(  
                                    referralParticipantID := $1::INT,
                                    referralDOB := $2::DATE,
                                    referralRace := $3::RACE,
                                    referralSex := $4::SEX,
                                    houseNum := $5::INT,
                                    streetAddress := $6::TEXT,
                                    apartmentInfo := $7::TEXT,
                                    zip := $8::VARCHAR(5),
                                    cityName := $9::TEXT,
                                    stateName := $10::STATES,
                                    refSource := $11::TEXT,
                                    hasInvolvement := $12::BOOLEAN,
                                    hasAttended := $13::BOOLEAN,
                                    reasonAttending := $14::TEXT,
                                    firstCall := $15::DATE,
                                    returnCallDate := $16::DATE,
                                    startDate := $17::DATE,
                                    classAssigned := $18::TEXT,
                                    letterMailedDate := $19::DATE,
                                    extraNotes := $20::TEXT,
                                    eID := $21::INT
                                    );", [$pIDResult, $self_pers_dob, $self_pers_race, $self_pers_sex, $self_pers_street_num, $self_pers_street_name, $self_apt_info, $self_pers_zip, $self_pers_city,
            $self_pers_state, $self_ref_source, $self_involvement, $self_attended, $reason, $self_office_firstCall,
            $self_office_returnedCall, $self_tentative_start, $self_assigned_to, $self_letter_mailed, $notes, $eID]);

        $result = pg_fetch_result($result, 0);

        if ($self_pers_phone !== NULL) {
            $phoneResult = $db->query("INSERT INTO FormPhoneNumbers (formID, phoneNumber, phoneType)
                                          VALUES ($1, $2, $3);", [$result, $self_pers_phone, 'Primary']);
        }

        if ($result) {
            $state = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);
            if ($state != 0) {
                $_SESSION['form-error'] = true;
                $_SESSION['error-state'] = $state;
                header("Location: /form-success");
                die();
            }
        }

            $_SESSION['form-type'] = $form_type;
            header("Location: /form-success");
            die();
    }


}

include('header.php');
?>

    <!-- Page Content -->
    <div id="page-content-wrapper" style="width:100%">
		<div class="container-fluid controls" style="height:30px;">
            <button class="cpca btn" onclick="goBack()" style="float:left;"><i class="fa fa-arrow-left"></i> Back</button>
			<button type="button" class="btn cpca" onclick="window.print()" style="float:right;"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
		</div>
        <div class="container-fluid">

            <div class="dropdown">

                <?php
                if (isset($params[1]) && isset($params[2]))
                    echo '<form id="self_participant_info" action="/self-referral-form/'.$params[0].'/'.$params[1].'/'.$params[2].'" method="post" novalidate>';
                else
                    echo '<form id="self_participant_info" action="/self-referral-form" method="post" novalidate>';
                ?>

                    <div id="accordion" role="tablist" aria-multiselectable="true">
                        <br>
                        <!-- first collapsible -->
                        <div class="card">
                            <div class="card-header" role="tab">
                                <h5 class="card-title" style="font-weight: normal;">
                                    <a data-toggle="collapse" id="self_pers_title" class="form-header" data-parent="#accordion" onfocusin="section1()" href="#collapse1">Participant Information</a>
                                </h5>
                            </div>

                            <div id="collapse1" class="collapse show" role="tabpanel">
                                <div class="card-body">
                                    <h5>Personal Information</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="self_pers_firstname">Participant Name:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" id="self_pers_firstname" name="self_pers_firstname"
                                                   value="<?= (isset($self_pers_firstname_result)) ? $self_pers_firstname_result : "" ?>" placeholder="First name" required>
                                            <div id="fname_error" class="invalid-feedback">Enter first name</div>
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="self_pers_lastname">Last Name:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" id="self_pers_lastname" name="self_pers_lastname"
                                                   value="<?= (isset($self_pers_lastname_result)) ? $self_pers_lastname_result : "" ?>" placeholder="Last name" required>
                                            <div id="lname_error" class="invalid-feedback">Enter last name</div>
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="self_pers_middlein">MInitial:</label>
                                        <div class="col-sm-1 col">
                                            <input type="text" class="form-control" id="self_pers_middlein" name="self_pers_middlein"
                                                   value="<?= (isset($self_pers_middlein_result)) ? $self_pers_middlein_result : ""?>" placeholder="Initial" maxlength="1">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="self_pers_dob">Date of Birth:</label>
                                        <div class="col-sm-2 col">
                                            <input type="date" class="form-control" id="self_pers_dob" name="self_pers_dob"
                                                   value="<?= (isset($self_pers_dob_result)) ? $self_pers_dob_result : "" ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="self_pers_race">Race:</label>
                                        <div class="col-sm-2 col">
                                            <select class="form-control select_sex" name="self_pers_race" id="intake_ethnicity">
                                                <option value="" selected="selected" disabled="disabled">Choose one</option>
                                                <?php
                                                $res = $db->query("SELECT unnest(enum_range(NULL::race)) AS type", []);
                                                while ($enumtype = pg_fetch_assoc($res)) {
                                                    $t = $enumtype ['type'];
                                                    ?>
                                                    <option value="<?= $t ?>" <?= (isset($self_pers_race_result) && $self_pers_race_result == $t) ? "selected" : "" ?>><?= $t ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="self_pers_sex">Sex:</label>
                                        <div class="col-sm-2 col">
                                            <select class="form-control select_sex" name="self_pers_sex" id="intake_ethnicity">
                                                <option value="" selected="selected" disabled="disabled">Choose one</option>
                                                <?php
                                                $res = $db->query("SELECT unnest(enum_range(NULL::sex)) AS type", []);
                                                while ($enumtype = pg_fetch_assoc($res)) {
                                                    $t = $enumtype ['type'];
                                                    ?>
                                                    <option value="<?= $t ?>" <?= (isset($self_pers_sex_result) && $self_pers_sex_result == $t) ? "selected" : "" ?>><?= $t ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <h5>Contact Information</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 col-2" for="self_pers_address">Street Address:</label>
                                        <div class="col-sm-3 col">
                                            <input type="text" class="form-control" id="self_pers_address" name="self_pers_address"
                                                   value="<?= (isset($self_pers_street_name_result)) ? $self_pers_street_num_result . " " . $self_pers_street_name_result : "" ?>" placeholder="Street address">
                                        </div>

                                        <label class="col-form-label col-sm-1 col-2" for="self_apt_info">Apartment:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" id="self_apt_info" name="self_apt_info"
                                                   value="<?= (isset($self_apt_info_result)) ? $self_apt_info_result : "" ?>" placeholder="Apartment Information">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="self_pers_state">State:</label>
                                        <div class="col-sm-3 col">
                                            <select class="form-control" id="self_pers_state" name="self_pers_state" >
                                                <option value="" selected="selected" disabled="disabled">Choose a state</option>
                                                <?php
                                                $res = $db->query("SELECT unnest(enum_range(NULL::states)) AS type", []);
                                                while ($enumtype = pg_fetch_assoc($res)) {
                                                    $t = $enumtype ['type'];
                                                    ?>
                                                    <option value="<?= $t ?>" <?= (isset($self_pers_state_result) && $self_pers_state_result == $t) ? "selected" : "" ?>><?= $t ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <label class="col-form-label col-sm-1 col-2" for="self_pers_city">City:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" id="self_pers_city" name="self_pers_city"
                                                   value="<?= (isset($self_pers_city_result)) ? $self_pers_city_result : ""?>" placeholder="City">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="self_pers_zip">ZIP:</label>
                                        <div class="col-sm-1 col">
                                            <input type="text" class="form-control mask-zip" id="self_pers_zip" name="self_pers_zip"
                                                   value="<?= (isset($self_pers_zip_result)) ? $self_pers_zip_result : ""?>" placeholder="Zip">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="self_pers_phone">Phone Number:</label>
                                        <div class="col-sm-2 col">
                                            <input type="tel" class="form-control mask-phone feedback-icon" id="self_pers_phone" name="self_pers_phone"
                                                   value="<?= (isset($self_pers_phone_result)) ? $self_pers_phone_result : ""?>" placeholder="(999) 999-9999">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- 1st collapsible end -->
                        <br>
                        <!-- 2nd collapsible -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title" style="font-weight: normal;">
                                    <a data-toggle="collapse" data-parent="#accordion" onfocusin="section2()" class="form-header" href="#collapse2">Additional Information</a>
                                </h5>
                            </div>

                            <div id="collapse2" class="collapse">
                                <div class="card-body">
                                    <h5>Additional Participant Information</h5>
                                    <br>

                                    <!-- Begin Q: Involvement with CPS/Protective/Foster Care -->
                                    <div class="form-group radio-group row">
                                        <div class="col-sm-4">
                                            <label class="form-control-label"> Do you have any involvement with CPS/Protective/Foster Care?</label>
                                        </div>
                                        <label class="custom-control custom-radio" for="self_involvement_yes">
                                            <input class="custom-control-input" type="radio" id="self_involvement_yes" name="self_involvement"
                                                <?= (isset($self_involvement_result) && $self_involvement_result == "t") ? "checked" : "" ?> value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="self_involvement_no">
                                            <input class="custom-control-input" type="radio" id="self_involvement_no" name="self_involvement"
                                                <?= (isset($self_involvement_result) && $self_involvement_result == "f") ? "checked" : "" ?> value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Involvement with CPS/Protective/Foster Care -->

                                    <!-- Begin Q: PEP classes in past -->
                                    <div class="form-group radio-group row">
                                        <div class="col-sm-4">
                                            <label class="form-control-label"> Have you attended PEP parenting classes in the past?</label>
                                        </div>
                                        <label class="custom-control custom-radio" for="self_attended_yes">
                                            <input class="custom-control-input" type="radio" id="self_attended_yes" name="self_attended"
                                                <?php echo (isset($self_attended_result) && $self_attended_result === "t") ? "checked" : "" ?> value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="self_attended_no">
                                            <input class="custom-control-input" type="radio" id="self_attended_no" name="self_attended"
                                                <?php echo (isset($self_attended_result) && $self_attended_result === "f") ? "checked" : "" ?> value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: PEP classes in past -->

                                    <br>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 col-3" for="self_ref_source">Referral Source:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" id="self_ref_source" name="self_ref_source"
                                                   value="<?= (isset($self_ref_source_result)) ? $self_ref_source_result : "" ?>" placeholder="Referral Source">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 col-3" for="reason">Reason for attendance:</label>
                                        <div class="col-sm-3 col">
                                            <textarea style="resize: none;" class="form-control" rows=4 id="reason" name="reason" placeholder="Reason for attending classes"><?= (isset($reason_result)) ? $reason_result : "" ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>  <!-- 2nd collapsable end -->
                    </div>
                    <br>
                    <!-- 3rd collapsable -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title" style="font-weight: normal;">
                                <a data-toggle="collapse" data-parent="#accordion" onfocusin="section3()" class="form-header" href="#collapse3">Office Information</a>
                            </h5>
                        </div>

                        <div id="collapse3" class="collapse">
                            <div class="card-body">
                                <h5>For Office Use Only</h5>
                                <br>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2" for="self_office_firstCall">Date of First Call:</label>
                                    <div class="col-sm-2 col">
                                        <input type="date" class="form-control" id="self_office_firstCall" name="self_office_firstCall"
                                               value="<?= (isset($self_office_firstCall_result)) ? $self_office_firstCall_result : "" ?>">
                                    </div>

                                    <label class="col-form-label col-sm-2" for="self_office_returnedCall">Returned Call:</label>
                                    <div class="col-sm-2 col">
                                        <input type="date" class="form-control" id="self_office_returnedCall" name="self_office_returnedCall"
                                               value="<?= (isset($self_office_returnedCall_result)) ? $self_office_returnedCall_result : "" ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2" for="self_tentative_start">Tentative Start Date:</label>
                                    <div class="col-sm-2 col">
                                        <input type="date" class="form-control" id="self_tentative_start" name="self_tentative_start"
                                               value="<?= (isset($self_tentative_start_result)) ? $self_tentative_start_result : "" ?>">
                                    </div>

                                    <label class="col-form-label col-sm-2" for="self_letter_mailed">Letter Mailed:</label>
                                    <div class="col-sm-2 col">
                                        <input type="date" class="form-control" id="self_letter_mailed" name="self_letter_mailed"
                                               value="<?= (isset($self_letter_mailed_result)) ? $self_letter_mailed_result : "" ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2 col-3" for="self_assigned_to">Class Assigned to:</label>
                                    <div class="col-sm-2 col">
                                        <input type="text" class="form-control" id="self_assigned_to" name="self_assigned_to"
                                               value="<?= (isset($self_assigned_to_result)) ? $self_assigned_to_result : "" ?>" placeholder="Program">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2 col-auto" for="notes">Notes:</label>
                                    <div class="col-sm-3 col">
                                        <textarea style="resize: none;" class="form-control" rows=5 id="notes" name="notes" placeholder="Enter any notes here"><?= (isset($notes_result)) ? $notes_result : "" ?></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>   <!-- 3rd collapsible end -->
                    </div>
                </form>
            </div>  <!-- panel group end -->

            <?php
            if (isset($params[0]) && $params[0] == "edit")
                echo '<button id="btnUpdate" onclick="submitAllSelf()" class="cpca btn">Update</button>';
            else if(isset($params[0]) && $params[0] == "view")
                echo '<a href="/ps-view-participant/'.$params[1].'"><button id="btnView" class="cpca btn">Back To Participant</button></a>';
            else
                include('form_duplicate_check.php');
            ?>
        <br><br>
        </div>  <!-- /#container -->
    </div>  <!-- /#container-fluid class -->
<style>
@media print{
  .collapse {
    display: block !important;
    height: auto !important
  }
  .controls {
	  display: none !important;
  }
  .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
    float: left;
  }
  .col-sm-12 {
    width: 100%;
  }
  .col-sm-11 {
    width: 91.66666667%;
  }
  .col-sm-10 {
    width: 83.33333333%;
  }
  .col-sm-9 {
    width: 75%;
  }
  .col-sm-8 {
    width: 66.66666667%;
  }
  .col-sm-7 {
    width: 58.33333333%;
  }
  .col-sm-6 {
    width: 50%;
  }
  .col-sm-5 {
    width: 41.66666667%;
  }
  .col-sm-4 {
    width: 33.33333333%;
  }
  .col-sm-3 {
    width: 25%;
  }
  .col-sm-2 {
    width: 16.66666667%;
  }
  .col-sm-1 {
    width: 8.33333333%;
  }
  .col-sm-pull-12 {
    right: 100%;
  }
  .col-sm-pull-11 {
    right: 91.66666667%;
  }
  .col-sm-pull-10 {
    right: 83.33333333%;
  }
  .col-sm-pull-9 {
    right: 75%;
  }
  .col-sm-pull-8 {
    right: 66.66666667%;
  }
  .col-sm-pull-7 {
    right: 58.33333333%;
  }
  .col-sm-pull-6 {
    right: 50%;
  }
  .col-sm-pull-5 {
    right: 41.66666667%;
  }
  .col-sm-pull-4 {
    right: 33.33333333%;
  }
  .col-sm-pull-3 {
    right: 25%;
  }
  .col-sm-pull-2 {
    right: 16.66666667%;
  }
  .col-sm-pull-1 {
    right: 8.33333333%;
  }
  .col-sm-pull-0 {
    right: auto;
  }
  .col-sm-push-12 {
    left: 100%;
  }
  .col-sm-push-11 {
    left: 91.66666667%;
  }
  .col-sm-push-10 {
    left: 83.33333333%;
  }
  .col-sm-push-9 {
    left: 75%;
  }
  .col-sm-push-8 {
    left: 66.66666667%;
  }
  .col-sm-push-7 {
    left: 58.33333333%;
  }
  .col-sm-push-6 {
    left: 50%;
  }
  .col-sm-push-5 {
    left: 41.66666667%;
  }
  .col-sm-push-4 {
    left: 33.33333333%;
  }
  .col-sm-push-3 {
    left: 25%;
  }
  .col-sm-push-2 {
    left: 16.66666667%;
  }
  .col-sm-push-1 {
    left: 8.33333333%;
  }
  .col-sm-push-0 {
    left: auto;
  }
  .col-sm-offset-12 {
    margin-left: 100%;
  }
  .col-sm-offset-11 {
    margin-left: 91.66666667%;
  }
  .col-sm-offset-10 {
    margin-left: 83.33333333%;
  }
  .col-sm-offset-9 {
    margin-left: 75%;
  }
  .col-sm-offset-8 {
    margin-left: 66.66666667%;
  }
  .col-sm-offset-7 {
    margin-left: 58.33333333%;
  }
  .col-sm-offset-6 {
    margin-left: 50%;
  }
  .col-sm-offset-5 {
    margin-left: 41.66666667%;
  }
  .col-sm-offset-4 {
    margin-left: 33.33333333%;
  }
  .col-sm-offset-3 {
    margin-left: 25%;
  }
  .col-sm-offset-2 {
    margin-left: 16.66666667%;
  }
  .col-sm-offset-1 {
    margin-left: 8.33333333%;
  }
  .col-sm-offset-0 {
    margin-left: 0%;
  }
  .visible-xs {
    display: none !important;
  }
  .hidden-xs {
    display: block !important;
  }
  table.hidden-xs {
    display: table;
  }
  tr.hidden-xs {
    display: table-row !important;
  }
  th.hidden-xs,
  td.hidden-xs {
    display: table-cell !important;
  }
  .hidden-xs.hidden-print {
    display: none !important;
  }
  .hidden-sm {
    display: none !important;
  }
  .visible-sm {
    display: block !important;
  }
  table.visible-sm {
    display: table;
  }
  tr.visible-sm {
    display: table-row !important;
  }
  th.visible-sm,
  td.visible-sm {
    display: table-cell !important;
  }
}
</style>
<?php
if(isset($params[0]) && $params[0] == "view") { ?>
    <script type="text/javascript">
        disableFields();
    </script>
<?php }
include('footer.php'); ?>
?>