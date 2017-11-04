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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $form_type = "self referral";

    // First Card (Participant Information)
    $self_pers_firstname = !empty($_POST['self_pers_firstname']) ? trim($_POST['self_pers_firstname']) : "";
    $self_pers_lastname = !empty($_POST['self_pers_lastname']) ? trim($_POST['self_pers_lastname']) : "";
    $self_pers_middlein = !empty($_POST['self_pers_middlein']) ? trim($_POST['self_pers_middlein']) : "";
    $self_pers_dob = !empty($_POST['self_pers_dob']) ? $_POST['self_pers_dob'] : "";
    $self_pers_address = !empty($_POST['self_pers_address']) ? trim($_POST['self_pers_address']) : "";

    // Logic for parsing the address into the address number and street name.
    $self_address_info = explode(" ", $self_pers_address);
    $self_pers_street_num = NULL;
    $self_pers_street_name = "";

        // Loop to parse through the address array ($self_address_info)
        for($i = 0; $i < sizeOf($self_address_info); $i++){
            if($i === 0){
                if($self_address_info[$i] !== "") {
                    $self_pers_street_num = $self_address_info[$i];
                }
            } else {
                $self_pers_street_name .= $self_address_info[$i] . " ";
            }
        }

    $self_pers_zip = !empty($_POST['self_pers_zip']) ? $_POST['self_pers_zip'] : 12601; // Default Zipcode is Poughkeepsie.
    $self_pers_state = !empty($_POST['self_pers_state']) ? $_POST['self_pers_state'] : NULL;
    $self_pers_city = !empty($_POST['self_pers_city']) ? trim($_POST['self_pers_city']) : NULL;
    $self_apt_info = !empty($_POST['self_apt_info']) ? trim($_POST['self_apt_info']) : "";
    $self_pers_phone = !empty($_POST['self_pers_phone']) ? $_POST['self_pers_phone'] : NULL;

    // Second Card (Additional Information)
    // This is logic for if a user does NOT select Yes or No, the result in the DB will be NULL.
    if (!empty($_POST['self_involvement'])) {
        $self_involvement = $_POST['self_involvement'] === "Yes" ? 1 : 0;
    } else {
        $self_involvement = NULL;
    }
    // This is logic for if a user does NOT select Yes or No, the result in the DB will be NULL.
    if(!empty($_POST['self_attended'])) {
        $self_attended = $_POST['self_attended'] === "Yes" ? 1 : 0;
    } else {
        $self_attended = NULL;
    }

    $self_ref_source = !empty($_POST['self_ref_source']) ? $_POST['self_ref_source'] : "";
    $reason = !empty($_POST['reason']) ? trim($_POST['reason']) : "";

    // Third Card (Office Information)
    $self_office_firstCall = !empty($_POST['self_office_firstCall']) ? trim($_POST['self_office_firstCall']) : NULL;
    $self_office_returnedCall = !empty($_POST['self_office_returnedCall']) ? trim($_POST['self_office_returnedCall']) : NULL;
    $self_tentative_start = !empty($_POST['self_tentative_start']) ? trim($_POST['self_tentative_start']) : NULL;
    $self_letter_mailed = !empty($_POST['self_letter_mailed']) ? trim($_POST['self_letter_mailed']) : NULL;
    $self_assigned_to = !empty($_POST['self_assigned_to']) ? trim($_POST['self_assigned_to']) : "";
    $notes = !empty($_POST['notes']) ? trim($_POST['notes']) : "";
    $eID = $_SESSION['employeeid'];

    // Stored Procedures
    // Gets the participant ID related to the person who is filling out the form to associate it with the form ID.
    $pIDResult = $db->query("SELECT PeopleInsert(
                                       fName := $1::TEXT,
                                       lName := $2::TEXT,
                                       mInit := $3::VARCHAR
                                       );", [$self_pers_firstname, $self_pers_lastname, $self_pers_middlein]);

    $pIDResult = pg_fetch_result($pIDResult, 0);

    // Inserts self referral form data into DB and associates the form with a participant ID.
    $result = $db->query("SELECT addSelfReferral(  
                                    referralParticipantID := $1::INT,
                                    referralDOB := $2::DATE,
                                    houseNum := $3::INT,
                                    streetAddress := $4::TEXT,
                                    apartmentInfo := $5::TEXT,
                                    zip := $6::INT,
                                    cityName := $7::TEXT,
                                    stateName := $8::STATES,
                                    phoneNum := $9::TEXT,
                                    refSource := $10::TEXT,
                                    hasInvolvement := $11::BOOLEAN,
                                    hasAttended := $12::BOOLEAN,
                                    reasonAttending := $13::TEXT,
                                    firstCall := $14::DATE,
                                    returnCallDate := $15::DATE,
                                    startDate := $16::DATE,
                                    classAssigned := $17::TEXT,
                                    letterMailedDate := $18::DATE,
                                    extraNotes := $19::TEXT,
                                    eID := $20::INT
                                    );", [$pIDResult, $self_pers_dob, $self_pers_street_num, $self_pers_street_name, $self_apt_info, $self_pers_zip, $self_pers_city,
                                            $self_pers_state, $self_pers_phone, $self_ref_source, $self_involvement, $self_attended, $reason, $self_office_firstCall,
                                            $self_office_returnedCall, $self_tentative_start, $self_assigned_to, $self_letter_mailed, $notes, $eID]);

    // Redirect user to success page.
    $_SESSION['form-type'] = $form_type;
    header("Location: /form-success");
    die();

}

include('header.php');
?>

    <!-- Page Content -->
    <div id="page-content-wrapper" style="width:100%">

        <div class="container-fluid">

            <div class="dropdown">

                <button class="cpca btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>

                <form id="self_participant_info" action="/self-referral-form" method="post" novalidate>
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
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" id="self_pers_firstname" name="self_pers_firstname" placeholder="First name" required>
                                            <div id="fname_error" class="invalid-feedback">Enter first name</div>
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="self_pers_lastname">Last Name:</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" id="self_pers_lastname" name="self_pers_lastname" placeholder="Last name" required>
                                            <div id="lname_error" class="invalid-feedback">Enter last name</div>
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="self_pers_middlein">MInitial:</label>
                                        <div class="col-sm-1">
                                            <input type="text" class="form-control" id="self_pers_middlein" name="self_pers_middlein" placeholder="Initial" maxlength="1">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="self_pers_dob">Date of Birth:</label>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" id="self_pers_dob" name="self_pers_dob">
                                        </div>
                                    </div>

                                    <h5>Contact Information</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="self_pers_address">Street Address:</label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" id="self_pers_address" name="self_pers_address" placeholder="Street address">
                                        </div>

                                        <label class="col-form-label col-sm-1" for="self_apt_info">Apartment:</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" id="self_apt_info" name="self_apt_info" placeholder="Apt number">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="self_pers_state">State:</label>
                                        <div class="col-sm-3">
                                            <select class="form-control" id="self_pers_state" name="self_pers_state" >
                                                <option value="" selected="selected" disabled="disabled">Choose a state</option>
                                                <?php
                                                $res = $db->query("SELECT unnest(enum_range(NULL::states)) AS type", []);
                                                while ($enumtype = pg_fetch_assoc($res)) {
                                                    $t = $enumtype ['type'];
                                                    ?>
                                                    <option value="<?= $t ?>"><?= $t ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <label class="col-form-label col-sm-1" for="self_pers_city">City:</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" id="self_pers_city" name="self_pers_city" placeholder="City" data-error="Enter city.">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="self_pers_zip">ZIP:</label>
                                        <div class="col-sm-1">
                                            <input type="text" class="form-control mask-zip" id="self_pers_zip" name="self_pers_zip" placeholder="Zip">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="self_pers_phone">Phone Number:</label>
                                        <div class="col-sm-2">
                                            <input type="tel" class="form-control mask-phone feedback-icon" id="self_pers_phone" name="self_pers_phone" placeholder="(999) 999-9999">
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

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    Do you have any involvement with CPS/Protective/Foster Care?
                                                </label>
                                                &emsp;
                                                <label class="form-check-label" for="self_involvement_yes">
                                                    <input class="form-check-input" type="radio" id="self_involvement_yes" name="self_involvement" value="Yes"> Yes
                                                </label>
                                                &nbsp;
                                                <label class="form-check-label" for="self_involvement_no">
                                                    <input class="form-check-input" type="radio" id="self_involvement_no" name="self_involvement" value="No"> No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    Have you attended PEP parenting classes in the past?
                                                </label>
                                                &emsp;
                                                <label class="form-check-label" for="self_attended_yes">
                                                    <input class="form-check-input" type="radio" id="self_attended_yes" name="self_attended" value="Yes"> Yes
                                                </label>
                                                &nbsp;
                                                <label class="form-check-label" for="self_attended_no">
                                                    <input class="form-check-input" type="radio" id="self_attended_no" name="self_attended" value="No"> No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="self_ref_source">Referral Source:</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" id="self_ref_source" name="self_ref_source" placeholder="Referral Source">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="reason">Reason for attendance:</label>
                                        <div class="col-sm-3">
                                            <textarea style="resize: none;" class="form-control" rows=4 id="reason" name="reason" placeholder="Reason for attending classes"></textarea>
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
                                    <div class="col-sm-2">
                                        <input type="date" class="form-control" id="self_office_firstCall" name="self_office_firstCall">
                                    </div>

                                    <label class="col-form-label col-sm-2" for="self_office_returnedCall">Returned Call:</label>
                                    <div class="col-sm-2">
                                        <input type="date" class="form-control" id="self_office_returnedCall" name="self_office_returnedCall">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2" for="self_tentative_start">Tentative Start Date:</label>
                                    <div class="col-sm-2">
                                        <input type="date" class="form-control" id="self_tentative_start" name="self_tentative_start">
                                    </div>

                                    <label class="col-form-label col-sm-2" for="self_letter_mailed">Letter Mailed:</label>
                                    <div class="col-sm-2">
                                        <input type="date" class="form-control" id="self_letter_mailed" name="self_letter_mailed">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2" for="self_assigned_to">Class Assigned to:</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" id="self_assigned_to" name="self_assigned_to" placeholder="Program">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2" for="notes">Notes:</label>
                                    <div class="col-sm-3">
                                        <textarea style="resize: none;" class="form-control" rows=5 id="notes" name="notes" placeholder="Enter any notes here"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>   <!-- 3rd collapsible end -->
                    </div>
                </form>
            </div>  <!-- panel group end -->
            <br>

            <button id="btnRegister" onclick="submitAllSelf()" class="cpca btn" style="margin-bottom: 20px;">Submit</button>

        </div>  <!-- /#container -->
    </div>  <!-- /#container-fluid class -->
<?php include('footer.php'); ?>