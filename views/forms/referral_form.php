<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Referral Form that agencies fill out for PEP programs.
 *
 * The Referral Form is a form that is filled out by referring agencies before enrolling in a PEP parenting program.
 * The form is submitted when one or multiple agencies refer an individual to a program.
 * Since this is an in-house application, somebody from the CPCA will have to manually input the form
 * in order for the Referral form to make its way into the system.
 *
 * @author Christian Menk and Stephen Bohner
 * @copyright 2017 Marist College
 * @version 0.3.3
 * @since 0.3.2
 */

authorizedPage();
global $db, $params, $route, $view;

if ($_SERVER[ 'REQUEST_METHOD' ] == 'POST') {

    $form_type = "agency referral";

    // First card (Participant Information)
    $pers_firstname = !empty($_POST['pers_firstname']) ? trim($_POST['pers_firstname']) : "";
    $pers_lastname = !empty($_POST['pers_lastname']) ? trim($_POST['pers_lastname']) : "";
    $pers_middlein = !empty($_POST['pers_middlein']) ? trim($_POST['pers_middlein']) : "";
    $pers_dob = !empty($_POST['pers_dob']) ? trim($_POST['pers_dob']) : NULL;
    $pers_address = !empty($_POST['pers_address']) ? trim($_POST['pers_address']) : "";
    $address_info = explode(" ", $pers_address);
    $pers_address_num = null;
    $pers_address_street = "";

    // Address separation logic
    for($i = 0; $i < sizeOf($address_info); $i++){
        if($i === 0){
            if($address_info[$i] !== "") {
                $pers_address_num = $address_info[$i];
            }
        } else {
            $pers_address_street .= " ".$address_info[$i];
        }
    }


    $pers_state = !empty($_POST['pers_state']) ? trim($_POST['pers_state']) : NULL;
    $pers_zip = !empty($_POST['pers_zip']) ? $_POST['pers_zip'] : NULL;
    $pers_city = !empty($_POST['pers_city']) ? trim($_POST['pers_city']) : "";
    $pers_primphone = !empty($_POST['pers_primphone']) ? trim($_POST['pers_primphone']) : "";
    $pers_secphone = !empty($_POST['pers_secphone']) ? trim($_POST['pers_secphone']) : "";
    $pers_reason = !empty($_POST['pers_reason']) ? trim($_POST['pers_reason']) : "";


    // Second card (Referring Party Information)
    $ref_party = !empty($_POST['ref_party']) ? trim($_POST['ref_party']) : "";
    $ref_date = !empty($_POST['ref_date']) ? trim($_POST['ref_date']) : NULL;
    $ref_firstname = !empty($_POST['ref_firstname']) ? trim($_POST['ref_firstname']) : "";
    $ref_lastname = !empty($_POST['ref_lastname']) ? trim($_POST['ref_lastname']) : "";
    $ref_phone = !empty($_POST['ref_phone']) ? trim($_POST['ref_phone']) : "";
    $ref_email = !empty($_POST['ref_email']) ? trim($_POST['ref_email']) : "";


    // Third card (Participant Household Information with all 5 members)
    $family_first_name_1 = !empty($_POST['family_first_name_1']) ? trim($_POST['family_first_name_1']) : "";
    $family_last_name_1 = !empty($_POST['family_last_name_1']) ? trim($_POST['family_last_name_1']) : "";
    $family_mi_1 = !empty($_POST['family_mi_1']) ? trim($_POST['family_mi_1']) : "";
    $family_dob_1 = !empty($_POST['family_dob_1']) ? trim($_POST['family_dob_1']) : NULL;
    $family_sex_1 = !empty($_POST['family_sex_1']) ? trim($_POST['family_sex_1']) : "";
    $family_race_1 = !empty($_POST['family_race_1']) ? trim($_POST['family_race_1']) : "";
    $family_relationship_1 = !empty($_POST['family_relationship_1']) ? trim($_POST['family_relationship_1']) : "";
    $family_needs_1 = !empty($_POST['family_needs_1']) ? trim($_POST['family_needs_1']) : "";

    $family_first_name_2 = !empty($_POST['family_first_name_2']) ? trim($_POST['family_first_name_2']) : "";
    $family_last_name_2 = !empty($_POST['family_last_name_2']) ? trim($_POST['family_last_name_2']) : "";
    $family_mi_2 = !empty($_POST['family_mi_2']) ? trim($_POST['family_mi_2']) : "";
    $family_dob_2 = !empty($_POST['family_dob_2']) ? trim($_POST['family_dob_2']) : NULL;
    $family_sex_2 = !empty($_POST['family_sex_2']) ? trim($_POST['family_sex_2']) : "";
    $family_race_2 = !empty($_POST['family_race_2']) ? trim($_POST['family_race_2']) : "";
    $family_relationship_2 = !empty($_POST['family_relationship_2']) ? trim($_POST['family_relationship_2']) : "";
    $family_needs_2 = !empty($_POST['family_needs_2']) ? trim($_POST['family_needs_2']) : "";

    $family_first_name_3 = !empty($_POST['family_first_name_3']) ? trim($_POST['family_first_name_3']) : "";
    $family_last_name_3 = !empty($_POST['family_last_name_3']) ? trim($_POST['family_last_name_3']) : "";
    $family_mi_3 = !empty($_POST['family_mi_3']) ? trim($_POST['family_mi_3']) : "";
    $family_dob_3 = !empty($_POST['family_dob_3']) ? trim($_POST['family_dob_3']) : NULL;
    $family_sex_3 = !empty($_POST['family_sex_3']) ? trim($_POST['family_sex_3']) : "";
    $family_race_3 = !empty($_POST['family_race_3']) ? trim($_POST['family_race_3']) : "";
    $family_relationship_3 = !empty($_POST['family_relationship_3']) ? trim($_POST['family_relationship_3']) : "";
    $family_needs_3 = !empty($_POST['family_needs_3']) ? trim($_POST['family_needs_3']) : "";

    $family_first_name_4 = !empty($_POST['family_first_name_4']) ? trim($_POST['family_first_name_4']) : "";
    $family_last_name_4 = !empty($_POST['family_last_name_4']) ? trim($_POST['family_last_name_4']) : "";
    $family_mi_4 = !empty($_POST['family_mi_4']) ? trim($_POST['family_mi_4']) : "";
    $family_dob_4 = !empty($_POST['family_dob_4']) ? trim($_POST['family_dob_4']) : NULL;
    $family_sex_4 = !empty($_POST['family_sex_4']) ? trim($_POST['family_sex_4']) : "";
    $family_race_4 = !empty($_POST['family_race_4']) ? trim($_POST['family_race_4']) : "";
    $family_relationship_4 = !empty($_POST['family_relationship_4']) ? trim($_POST['family_relationship_4']) : "";
    $family_needs_4 = !empty($_POST['family_needs_4']) ? trim($_POST['family_needs_4']) : "";

    $family_first_name_5 = !empty($_POST['family_first_name_5']) ? trim($_POST['family_first_name_5']) : "";
    $family_last_name_5 = !empty($_POST['family_last_name_5']) ? trim($_POST['family_last_name_5']) : "";
    $family_mi_5 = !empty($_POST['family_mi_5']) ? trim($_POST['family_mi_5']) : "";
    $family_dob_5 = !empty($_POST['family_dob_5']) ? trim($_POST['family_dob_5']) : NULL;
    $family_sex_5 = !empty($_POST['family_sex_5']) ? trim($_POST['family_sex_5']) : "";
    $family_race_5 = !empty($_POST['family_race_5']) ? trim($_POST['family_race_5']) : "";
    $family_relationship_5 = !empty($_POST['family_relationship_5']) ? trim($_POST['family_relationship_5']) : "";
    $family_needs_5 = !empty($_POST['family_needs_5']) ? trim($_POST['family_needs_5']) : "";


    // Fourth Card (Additional Information with all 5 parties)
    $chkSpecialEd = !empty($_POST['chkSpecialEd']) ? 1 : 0;
    $chkCPS = !empty($_POST['chkCPS']) ? 1 : 0;
    $chkSubAbuse = !empty($_POST['chkSubAbuse']) ? 1 : 0;
    $chkMental = !empty($_POST['chkMental']) ? 1 : 0;
    $chkPreg = !empty($_POST['chkPreg']) ? 1 : 0;
    $chkIQ = !empty($_POST['chkIQ']) ? 1 : 0;
    $chkViolence = !empty($_POST['chkViolence']) ? 1 : 0;
    $chkReside = !empty($_POST['chkReside']) ? 1 : 0;
    $chkSigned = !empty($_POST['chkSigned']) ? 1 : 0;
    $additional_info = !empty($_POST['additional_info']) ? trim($_POST['additional_info']) : "";

    $party_type_1 = !empty($_POST['party_type_1']) ? trim($_POST['party_type_1']) : "";
    $party_firstname_1 = !empty($_POST['party_firstname_1']) ? trim($_POST['party_firstname_1']) : "";
    $party_lastname_1 = !empty($_POST['party_lastname_1']) ? trim($_POST['party_lastname_1']) : "";
    $party_phone_1 = !empty($_POST['party_phone_1']) ? trim($_POST['party_phone_1']) : "";
    $party_email_1 = !empty($_POST['party_email_1']) ? trim($_POST['party_email_1']) : "";

    $party_type_2 = !empty($_POST['party_type_2']) ? trim($_POST['party_type_2']) : "";
    $party_firstname_2 = !empty($_POST['party_firstname_2']) ? trim($_POST['party_firstname_2']) : "";
    $party_lastname_2 = !empty($_POST['party_lastname_2']) ? trim($_POST['party_lastname_2']) : "";
    $party_phone_2 = !empty($_POST['party_phone_2']) ? trim($_POST['party_phone_2']) : "";
    $party_email_2 = !empty($_POST['party_email_2']) ? trim($_POST['party_email_2']) : "";

    $party_type_3 = !empty($_POST['party_type_3']) ? trim($_POST['party_type_3']) : "";
    $party_firstname_3 = !empty($_POST['party_firstname_3']) ? trim($_POST['party_firstname_3']) : "";
    $party_lastname_3 = !empty($_POST['party_lastname_3']) ? trim($_POST['party_lastname_3']) : "";
    $party_phone_3 = !empty($_POST['party_phone_3']) ? trim($_POST['party_phone_3']) : "";
    $party_email_3 = !empty($_POST['party_email_3']) ? trim($_POST['party_email_3']) : "";

    $party_type_4 = !empty($_POST['party_type_4']) ? trim($_POST['party_type_4']) : "";
    $party_firstname_4 = !empty($_POST['party_firstname_4']) ? trim($_POST['party_firstname_4']) : "";
    $party_lastname_4 = !empty($_POST['party_lastname_4']) ? trim($_POST['party_lastname_4']) : "";
    $party_phone_4 = !empty($_POST['party_phone_4']) ? trim($_POST['party_phone_4']) : "";
    $party_email_4 = !empty($_POST['party_email_4']) ? trim($_POST['party_email_4']) : "";

    $party_type_5 = !empty($_POST['party_type_5']) ? trim($_POST['party_type_5']) : "";
    $party_firstname_5 = !empty($_POST['party_firstname_5']) ? trim($_POST['party_firstname_5']) : "";
    $party_lastname_5 = !empty($_POST['party_lastname_5']) ? trim($_POST['party_lastname_5']) : "";
    $party_phone_5 = !empty($_POST['party_phone_5']) ? trim($_POST['party_phone_5']) : "";
    $party_email_5 = !empty($_POST['party_email_5']) ? trim($_POST['party_email_5']) : "";

    // Fourth Card (Office Information)
    $office_contact_date = !empty($_POST['office_contact_date']) ? trim($_POST['office_contact_date']) : NULL;
    $office_means = !empty($_POST['office_means']) ? trim($_POST['office_means']) : "";
    $office_initial_date = !empty($_POST['office_initial_date']) ? trim($_POST['office_initial_date']) : NULL;
    $office_time = !empty($_POST['office_time']) ? trim($_POST['office_time']) : "";
    $office_location = !empty($_POST['office_location']) ? trim($_POST['office_location']) : "";
    $comments = !empty($_POST['comments']) ? trim($_POST['comments']) : "";




    // TODO: stored procedure

    /*              This is stored procedure work that is being commented out for now so we can at least have our forms in develop for the rest of the class to see.

    $con = pg_connect('host=10.11.12.33 port=5432 user=postgres password=@lgozzineIsTheBest dbname=PEP_DB');
    pg_query($con, 'BEGIN;');
    $result = pg_query_params($con,
        'SELECT addAgencyReferral(
          fName := $1::TEXT,
          lName := $2::TEXT,
          mInit := $3::VARCHAR,
          dob :=  $4::DATE,
          houseNum := $5::INTEGER,
          streetAddress := $6::TEXT,
          apartmentInfo := $7::TEXT,
          zipCode := $8::INTEGER,
          city := $9::TEXT,
          state := $10::STATES,
          referralReason := $11::TEXT,
          hasAgencyConsentForm := $12::BOOLEAN,
          referringAgency := $13::TEXT,
          referringAgencyDate := $14::DATE,
          additionalInfo := $15::TEXT,
          hasSpecialNeeds :=$16::BOOLEAN,
          hasSubstanceAbuseHistory :=$17::BOOLEAN,
          hasInvolvementCPS :=$18::BOOLEAN,
          isPregnant :=$19::BOOLEAN,
          hasIQDoc := $20::BOOLEAN,
          mentalHealthIssue := $21::BOOLEAN,
          hasDomesticViolenceHistory := $22::BOOLEAN,
          childrenLiveWithIndividual := $23::BOOLEAN,
          dateFirstContact := $24::DATE,
          meansOfContact := $25::TEXT,
          dateOfInitialMeeting := $26::DATE,
          location := $27::TEXT,
          comments := $28::TEXT,
          eID := $29::INTEGER)',
        array(
            '$pers_firstname',
            '$pers_lastname',
            '$pers_middlein',
            $pers_dob,
            $pers_address_num,
            '$pers_address_street',
            '$pers_apartment',
            $pers_zip,
            '$pers_city',
            $pers_state,
            '$pers_reason',
            $chkSigned,
            '$ref_party',
            $ref_date,
            '$additional_info',
            $chkSpecialEd,
            $chkSubAbuse,
            $chkCPS,
            $chkPreg,
            $chkIQ,
            $chkMental,
            $chkViolence,
            $chkReside,
            $office_contact_date,
            '$office_means',
            $office_initial_date,
            '$office_location',
            '$comments',
            1)); */


    $_SESSION['form-type'] = $form_type;
    header("Location: /form-success");
    die();
    
    // Used for inspecting input variables
//    $arr = get_defined_vars();
//    print_r($arr);


}

include('header.php');
?>

    <!-- Page Content -->
    <div id="page-content-wrapper" style="width:100%">

        <div class="container-fluid">
            <button class="cpca btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>

            <div class="dropdown">

                <form id="participant_info" action="/referral-form" method="post" novalidate>
                    <div id="accordion" role="tablist" aria-multiselectable="true">
                        <br>
                        <!-- first collapsable -->
                        <div class="card">
                            <div class="card-header" role="tab">
                                <h5 class="card-title" style="font-weight: normal;">
                                    <a data-toggle="collapse" id="pers_title" class="form-header" data-parent="#accordion" onfocusin="section1()" href="#collapse1">Participant Information</a>
                                </h5>
                            </div>

                            <div id="collapse1" class="collapse show" role="tabpanel">
                                <div class="card-body">
                                    <!-- Participant info form -->

                                    <h5>Personal Information</h5>
                                    <br>
                                    <div class="form-group row ">
                                        <label class="col-form-label col-sm-2" for="pers_firstname">Participant Name:</label>

                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" tabindex="0" id="pers_firstname" name="pers_firstname" placeholder="First name" required>
                                            <div class="invalid-feedback">Enter first name</div>
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="pers_lastname">Last Name:</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" id="pers_lastname" name="pers_lastname" placeholder="Last name" required>
                                            <div class="invalid-feedback">Enter last name</div>
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="pers_middlein">MInitial:</label>
                                        <div class="col-sm-1">
                                            <input type="text" class="form-control" maxlength="1" id="pers_middlein" name="pers_middlein" placeholder="Initial">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="pers_dob">Date of Birth:</label>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" name="pers_dob" id="pers_dob" placeholder="Enter DOB">
                                        </div>
                                    </div>

                                    <h5>Contact Information</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="pers_address">Street Address:</label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" name="pers_address" id="pers_address" placeholder="Street address">
                                        </div>
                                        <label class="col-form-label col-sm-1" for="pers_zip">ZIP:</label>
                                        <div class="col-sm-1">
                                            <input type="text" class="form-control mask-zip" name="pers_zip" id="pers_zip" placeholder="Zip">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="pers_state">State:</label>
                                        <div class="col-sm-3">
                                            <select class="form-control" name="pers_state" id="pers_state" >
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
                                        <label class="col-form-label col-sm-1" for="pers_city">City:</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" name="pers_city" id="pers_city" placeholder="City" data-error="Enter city.">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="pers_primphone">Primary Phone:</label>
                                        <div class="col-sm-2">
                                            <input type="tel" class="form-control mask-phone feedback-icon" name="pers_primphone" id="pers_primphone" placeholder="(999) 999-9999">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="pers_secphone">Secondary Phone:</label>
                                        <div class="col-sm-2">
                                            <input type="tel" class="form-control mask-phone" name="pers_secphone" id="pers_secphone" placeholder="(999) 999-9999">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="comment">Reason for Referral:</label>
                                        <div class="col-sm-3">
                                            <textarea style="resize: none;" class="form-control" rows=4 name="pers_reason" id="pers_reason" placeholder="Reason for referral"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <!-- 2nd collapsable -->
                        <div class="card" id="section2">
                            <div class="card-header">
                                <h5 class="card-title" style="font-weight: normal;">
                                    <a data-toggle="collapse"  class="form-header" data-parent="#accordion" onfocusin="section2()" id="section2Header" href="#collapse2">Referring Party Information</a>
                                </h5>
                            </div>

                            <div id="collapse2" class="collapse">
                                <div class="card-body">
                                    <h5>Referral Information</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="ref_party">Referring Party:</label>
                                        <div class="col-sm-2">
                                            <select class="form-control" name="ref_party" id="ref_party" placeholder="Enter party">
                                                <option value="" selected="selected" disabled="disabled">Choose a party</option>
                                                <?php
                                                $res = $db->query("SELECT unnest(enum_range(NULL::referraltype)) AS type", []);
                                                while ($enumtype = pg_fetch_assoc($res)) {
                                                    $t = $enumtype ['type'];
                                                    ?>
                                                    <option value="<?= $t ?>"><?= $t ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <label class="col-form-label col-sm-2" for="ref_date">Date of Referral:</label>
                                        <div class="col-sm-2">
                                            <input type="date" class="form-control" name="ref_date" id="ref_date">
                                        </div>
                                    </div>
                                    <br>
                                    <h5>Referring Party Contact Information</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="ref_firstname">Referring Party Name:</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" name="ref_firstname" id="ref_firstname" placeholder="First Name">
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="ref_lastname">Last Name:</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" name="ref_lastname" id="ref_lastname" placeholder="Last Name">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="ref_phone">Phone Number:</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control mask-phone" name="ref_phone" id="ref_phone" placeholder="(999) 999-9999">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="ref_email">Email:</label>
                                        <div class="col-sm-2">
                                            <input type="email" class="form-control" name="ref_email" id="ref_email" placeholder="cpca@cpca.com">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <!-- 3rd collapsable -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title" style="font-weight: normal;">
                                    <a data-toggle="collapse" class="form-header" onfocusin="section3()" data-parent="#accordion" href="#collapse3">Participant Household Information</a>
                                </h5>
                            </div>

                            <div id="collapse3" class="collapse">
                                <div class="card-body">
                                    <div id="familyEntry_1" class="clonedFamily">
                                        <h5 class="heading-reference">Household Member 1</h5>
                                        <br>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label label_fn">Member Name:</label>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control input_fn" name="family_first_name_1" maxlength="255" placeholder="First name">
                                            </div>
                                            <label class="col-sm-0 col-form-label sr-only label_ln">Last Name:</label>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control input_ln" name="family_last_name_1" maxlength="255" placeholder="Last name">
                                            </div>
                                            <label class="col-sm-0 col-form-label sr-only label_mi">Middle Initial:</label>
                                            <div class="col-sm-1">
                                                <input type="text" class="form-control input_mi" name="family_mi_1" maxlength="1" placeholder="Initial">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label label_dob" for="family_dob_1">Date of Birth:</label>
                                            <div class="col-sm-2">
                                                <input type="date" class="form-control input_dob" name="family_dob_1" id="family_dob_1" maxlength="255">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label label_sex">Sex:</label>
                                            <div class="col-sm-2">
                                                <select class="form-control select_sex" name="family_sex_1" id="family_sex_1">
                                                    <option value="" selected="selected" disabled="disabled">Choose one</option>
                                                    <?php
                                                    $res = $db->query("SELECT unnest(enum_range(NULL::sex)) AS type", []);
                                                    while ($enumtype = pg_fetch_assoc($res)) {
                                                        $t = $enumtype ['type'];
                                                        ?>
                                                        <option value="<?= $t ?>"><?= $t ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label label_race">Race:</label>
                                            <div class="col-sm-2">
                                                <select class="form-control select_race" name="family_race_1" id="family_race_1">
                                                    <option value="" selected="selected" disabled="disabled">Choose one</option>
                                                    <?php
                                                    $res = $db->query("SELECT unnest(enum_range(NULL::race)) AS type", []);
                                                    while ($enumtype = pg_fetch_assoc($res)) {
                                                        $t = $enumtype ['type'];
                                                        ?>
                                                        <option value="<?= $t ?>"><?= $t ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label label_relationship">Relationship:</label>
                                            <div class="col-sm-2">
                                                <select class="form-control select_relationship" name="family_relationship_1" id="family_relationship_1">
                                                    <option value="" selected="selected" disabled="disabled">Choose one</option>
                                                    <?php
                                                    $res = $db->query("SELECT unnest(enum_range(NULL::relationship)) AS type", []);
                                                    while ($enumtype = pg_fetch_assoc($res)) {
                                                        $t = $enumtype ['type'];
                                                        ?>
                                                        <option value="<?= $t ?>"><?= $t ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label label_needs">Specific Needs:</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control input_needs" name="family_needs_1" id="family_needs_1" placeholder="Ex: allergies">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Add Member:</label>
                                        <div class="col-sm-1">
                                            <button class="btn btn-default" type="button" id="btnAddMember">+</button> <!-- every other dropdown on this form uses down arrows -->
                                        </div>

                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Remove Member:</label>
                                        <div class="col-sm-1">
                                            <button class="btn btn-default" type="button" id="btnDelMember" disabled="disabled">-</button> <!-- every other dropdown on this form uses down arrows -->
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <br>
                        <!-- 4th collapsable -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title" style="font-weight: normal;">
                                    <a data-toggle="collapse" data-parent="#accordion" onfocusin="section4()" class="form-header" href="#collapse4">Additional Information</a>
                                </h5>
                            </div>

                            <div id="collapse4" class="collapse">
                                <div class="card-body">
                                    <!--                            <form class="form-horizontal" id="referral_info">-->
                                    <h5>Additional Participant Information</h5>
                                    <span>Please check all that apply to the participant:</span>
                                    <br><br>
                                    <div style="padding-left:100px;">
                                        <div class="form-check">
                                            <label class="form-check-label col-sm-3">
                                                <input class="form-check-input" id="chkSpecialEd" name="chkSpecialEd" type="checkbox" value="">
                                                Special Education/IEP/Resource Services
                                            </label>
                                            <label class="form-check-label col-sm-4">
                                                <input class="form-check-input" id="chkCPS" name="chkCPS" type="checkbox" value="">
                                                Involved with CPS/Foster Care/Preventive Services
                                            </label>

                                        </div>

                                        <div class="form-check">
                                            <label class="form-check-label col-sm-3">
                                                <input class="form-check-input" id="chkSubAbuse" name="chkSubAbuse" type="checkbox" value="">
                                                Substance Use/Abuse History
                                            </label>
                                            <label class="form-check-label col-sm-3">
                                                <input class="form-check-input" id="chkMental" name="chkMental" type="checkbox" value="">
                                                Mental Health/Dual Diagnosis
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <label class="form-check-label col-sm-3">
                                                <input class="form-check-input" id="chkPreg" name="chkPreg" type="checkbox" value="">
                                                Pregnant
                                            </label>
                                            <label class="form-check-label col-sm-3">
                                                <input class="form-check-input" id="chkIQ" name="chkIQ" type="checkbox" value="">
                                                IQ Documentation
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <label class="form-check-label col-sm-3">
                                                <input class="form-check-input" id="chkViolence" name="chkViolence" type="checkbox" value="">
                                                Domestic Violence History
                                            </label>
                                            <label class="form-check-label col-sm-4">
                                                <input class="form-check-input" id="chkReside" name="chkReside" type="checkbox" value="">
                                                Child/Children do not reside with Referred Individual
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <label class="form-check-label col-sm-4">
                                                <input class="form-check-input" id="chkSigned" name="chkSigned" type="checkbox" value="">
                                                Signed consent form for release of information
                                            </label>
                                        </div>
                                    </div>
                                    <br>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="comments">Additional Information:</label>
                                        <div class="col-sm-3">
                                            <textarea style="resize: none;" class="form-control" rows=5 name="additional_info" id="additional_info" placeholder="Enter any additional information"></textarea>
                                        </div>
                                    </div>


                                    <div id="partyEntry_1" class="clonedParty">
                                        <h5 class="heading-reference">Additional Parties Involved</h5>
                                        <br>
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-2 label_type" for="ag_name">Party Type:</label>
                                            <div class="col-sm-2">
                                                <select class="form-control select_type" name="party_type_1" id="party_type_1" placeholder="Enter party">
                                                    <option value="" selected="selected" disabled="disabled">Choose a party</option>
                                                    <?php
                                                    $res = $db->query("SELECT unnest(enum_range(NULL::referraltype)) AS type", []);
                                                    while ($enumtype = pg_fetch_assoc($res)) {
                                                        $t = $enumtype ['type'];
                                                        ?>
                                                        <option value="<?= $t ?>"><?= $t ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-2 label_fn" for="party_firstname_1">Contact Name:</label>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control input_fn" name="party_firstname_1" id="party_firstname_1" placeholder="First Name">
                                            </div>

                                            <label class="col-form-label col-sm-0 sr-only label_ln" for="party_lastname_1">Last Name:</label>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control input_ln" name="party_lastname_1" id="party_lastname_1" placeholder="Last Name">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-2 label_phone" for="party_phone_1">Contact Phone:</label>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control mask-phone input_phone" name="party_phone_1" id="party_phone_1" placeholder="(999) 999-9999">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-2 label_email" for="party_email_1">Contact Email:</label>
                                            <div class="col-sm-2">
                                                <input type="email" class="form-control input_email" name="party_email_1" id="party_email_1" placeholder="cpca@cpca.com">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Add Another Party:</label>
                                        <div class="col-sm-1">
                                            <button class="btn btn-default" type="button" id="btnAddParty">+</button>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Remove Party:</label>
                                        <div class="col-sm-1">
                                            <button class="btn btn-default" type="button" id="btnDelParty">-</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>  <!-- 4th collapsable end -->
                    </div>
                    <br>
                    <!-- 5th collapsable begin -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title" style="font-weight: normal;">
                                <a data-toggle="collapse" data-parent="#accordion" onfocusin="section5()" class="form-header" href="#collapse5">Office Information</a>
                            </h5>
                        </div>

                        <div id="collapse5" class="collapse">
                            <div class="card-body">
                                <h5>For Office Use Only</h5>
                                <br>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2" for="office_contact_date">Date of First Contact:</label>
                                    <div class="col-sm-2">
                                        <input type="date" class="form-control" name="office_contact_date" id="office_contact_date">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2" for="office_means">Means of Contact:</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" name="office_means" id="office_means" placeholder="Email, Phone, etc...">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2" for="office_initial_date">Initial Meeting Info:</label>
                                    <div class="col-sm-2">
                                        <input type="date" class="form-control" name="office_initial_date" id="office_initial_date">
                                    </div>

                                    <label class="col-form-label col-sm-0 sr-only" for="office_time">Time:</label>
                                    <div class="col-sm-2">
                                        <input type="time" class="form-control" name="office_time" id="office_time" placeholder="Time">
                                    </div>

                                    <label class="col-form-label col-sm-0 sr-only" for="office_location">Location:</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" name="office_location" id="office_location" placeholder="Location">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2" for="comments">Comments:</label>
                                    <div class="col-sm-3">
                                        <textarea style="resize: none;" class="form-control" rows=5 name="comments" id="comments" placeholder="Enter any comments here"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>  <!-- panel group end -->
            <br>

            <button id="btnRegister" onclick="submitAll()" class="cpca btn" style="margin-bottom: 20px;">Submit</button>

        </div>  <!-- /#container -->
    </div>  <!-- /#container-fluid class -->
<?php include('footer.php'); ?>