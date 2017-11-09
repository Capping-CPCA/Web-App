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
    $pers_firstname = !empty($_POST['pers_firstname']) ? trim($_POST['pers_firstname']) : NULL;
    $pers_lastname = !empty($_POST['pers_lastname']) ? trim($_POST['pers_lastname']) : NULL;
    $pers_middlein = !empty($_POST['pers_middlein']) ? trim($_POST['pers_middlein']) : NULL;
    $pers_dob = !empty($_POST['pers_dob']) ? trim($_POST['pers_dob']) : NULL;
    $pers_sex = !empty($_POST['pers_sex']) ? trim($_POST['pers_sex']) : NULL;
    $pers_race = !empty($_POST['pers_race']) ? trim($_POST['pers_race']) : NULL;
    $pers_address = !empty($_POST['pers_address']) ? trim($_POST['pers_address']) : NULL;
    $address_info = explode(" ", $pers_address);
    $pers_address_num = null;
    $pers_address_street = NULL;

    // Address separation logic
    for($i = 0; $i < sizeOf($address_info); $i++){
        if($i === 0){
            if($address_info[$i] !== "") {
                if(is_numeric($address_info[$i])){
                    $pers_address_num = $address_info[$i];
                } else {
                    $pers_address_street .= " ".$address_info[$i];
                }
            }
        } else {
            $pers_address_street .= " ".$address_info[$i];
        }
    }

    $pers_apartment_info = !empty($_POST['pers_apt_info']) ? trim($_POST['pers_apt_info']) : NULL;
    $pers_state = !empty($_POST['pers_state']) ? trim($_POST['pers_state']) : "New York";
    $pers_zip = !empty($_POST['pers_zip']) ? $_POST['pers_zip'] : 12601;
    $pers_city = !empty($_POST['pers_city']) ? trim($_POST['pers_city']) : "Poughkeepsie";
    $pers_primphone = !empty($_POST['pers_primphone']) ? phoneStrToNum($_POST['pers_primphone']) : NULL;
    $pers_secphone = !empty($_POST['pers_secphone']) ? phoneStrToNum($_POST['pers_secphone']) : NULL;
    $pers_reason = !empty($_POST['pers_reason']) ? trim($_POST['pers_reason']) : NULL;


    // Second card (Referring Party Information)
    $ref_party = !empty($_POST['ref_party']) ? trim($_POST['ref_party']) : NULL;
    $ref_date = !empty($_POST['ref_date']) ? trim($_POST['ref_date']) : NULL;
    $ref_firstname = !empty($_POST['ref_firstname']) ? trim($_POST['ref_firstname']) : NULL;
    $ref_lastname = !empty($_POST['ref_lastname']) ? trim($_POST['ref_lastname']) : NULL;
    $ref_phone = !empty($_POST['ref_phone']) ? phoneStrToNum($_POST['ref_phone']) : NULL;
    $ref_email = !empty($_POST['ref_email']) ? trim($_POST['ref_email']) : NULL;


    // Third card (Participant Household Information with all 5 members)
    $family_first_name_1 = !empty($_POST['family_first_name_1']) ? trim($_POST['family_first_name_1']) : NULL;
    $family_last_name_1 = !empty($_POST['family_last_name_1']) ? trim($_POST['family_last_name_1']) : NULL;
    $family_mi_1 = !empty($_POST['family_mi_1']) ? trim($_POST['family_mi_1']) : NULL;
    $family_dob_1 = !empty($_POST['family_dob_1']) ? trim($_POST['family_dob_1']) : NULL;
    $family_sex_1 = !empty($_POST['family_sex_1']) ? trim($_POST['family_sex_1']) : NULL;
    $family_race_1 = !empty($_POST['family_race_1']) ? trim($_POST['family_race_1']) : NULL;
    $family_relationship_1 = !empty($_POST['family_relationship_1']) ? trim($_POST['family_relationship_1']) : NULL;

    $family_first_name_2 = !empty($_POST['family_first_name_2']) ? trim($_POST['family_first_name_2']) : NULL;
    $family_last_name_2 = !empty($_POST['family_last_name_2']) ? trim($_POST['family_last_name_2']) : NULL;
    $family_mi_2 = !empty($_POST['family_mi_2']) ? trim($_POST['family_mi_2']) : NULL;
    $family_dob_2 = !empty($_POST['family_dob_2']) ? trim($_POST['family_dob_2']) : NULL;
    $family_sex_2 = !empty($_POST['family_sex_2']) ? trim($_POST['family_sex_2']) : NULL;
    $family_race_2 = !empty($_POST['family_race_2']) ? trim($_POST['family_race_2']) : NULL;
    $family_relationship_2 = !empty($_POST['family_relationship_2']) ? trim($_POST['family_relationship_2']) : NULL;
    $family_needs_2 = !empty($_POST['family_needs_2']) ? trim($_POST['family_needs_2']) : NULL;

    $family_first_name_3 = !empty($_POST['family_first_name_3']) ? trim($_POST['family_first_name_3']) : NULL;
    $family_last_name_3 = !empty($_POST['family_last_name_3']) ? trim($_POST['family_last_name_3']) : NULL;
    $family_mi_3 = !empty($_POST['family_mi_3']) ? trim($_POST['family_mi_3']) : NULL;
    $family_dob_3 = !empty($_POST['family_dob_3']) ? trim($_POST['family_dob_3']) : NULL;
    $family_sex_3 = !empty($_POST['family_sex_3']) ? trim($_POST['family_sex_3']) : NULL;
    $family_race_3 = !empty($_POST['family_race_3']) ? trim($_POST['family_race_3']) : NULL;
    $family_relationship_3 = !empty($_POST['family_relationship_3']) ? trim($_POST['family_relationship_3']) : NULL;

    $family_first_name_4 = !empty($_POST['family_first_name_4']) ? trim($_POST['family_first_name_4']) : NULL;
    $family_last_name_4 = !empty($_POST['family_last_name_4']) ? trim($_POST['family_last_name_4']) : NULL;
    $family_mi_4 = !empty($_POST['family_mi_4']) ? trim($_POST['family_mi_4']) : NULL;
    $family_dob_4 = !empty($_POST['family_dob_4']) ? trim($_POST['family_dob_4']) : NULL;
    $family_sex_4 = !empty($_POST['family_sex_4']) ? trim($_POST['family_sex_4']) : NULL;
    $family_race_4 = !empty($_POST['family_race_4']) ? trim($_POST['family_race_4']) : NULL;
    $family_relationship_4 = !empty($_POST['family_relationship_4']) ? trim($_POST['family_relationship_4']) : NULL;

    $family_first_name_5 = !empty($_POST['family_first_name_5']) ? trim($_POST['family_first_name_5']) : NULL;
    $family_last_name_5 = !empty($_POST['family_last_name_5']) ? trim($_POST['family_last_name_5']) : NULL;
    $family_mi_5 = !empty($_POST['family_mi_5']) ? trim($_POST['family_mi_5']) : NULL;
    $family_dob_5 = !empty($_POST['family_dob_5']) ? trim($_POST['family_dob_5']) : NULL;
    $family_sex_5 = !empty($_POST['family_sex_5']) ? trim($_POST['family_sex_5']) : NULL;
    $family_race_5 = !empty($_POST['family_race_5']) ? trim($_POST['family_race_5']) : NULL;
    $family_relationship_5 = !empty($_POST['family_relationship_5']) ? trim($_POST['family_relationship_5']) : NULL;


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
    $additional_info = !empty($_POST['additional_info']) ? trim($_POST['additional_info']) : NULL;

    $party_type_1 = !empty($_POST['party_type_1']) ? trim($_POST['party_type_1']) : NULL;
    $party_firstname_1 = !empty($_POST['party_firstname_1']) ? trim($_POST['party_firstname_1']) : NULL;
    $party_lastname_1 = !empty($_POST['party_lastname_1']) ? trim($_POST['party_lastname_1']) : NULL;
    $party_phone_1 = !empty($_POST['party_phone_1']) ? phoneStrToNum($_POST['party_phone_1']) : NULL;
    $party_email_1 = !empty($_POST['party_email_1']) ? trim($_POST['party_email_1']) : NULL;

    $party_type_2 = !empty($_POST['party_type_2']) ? trim($_POST['party_type_2']) : NULL;
    $party_firstname_2 = !empty($_POST['party_firstname_2']) ? trim($_POST['party_firstname_2']) : NULL;
    $party_lastname_2 = !empty($_POST['party_lastname_2']) ? trim($_POST['party_lastname_2']) : NULL;
    $party_phone_2 = !empty($_POST['party_phone_2']) ? phoneStrToNum($_POST['party_phone_2']) : NULL;
    $party_email_2 = !empty($_POST['party_email_2']) ? trim($_POST['party_email_2']) : NULL;

    $party_type_3 = !empty($_POST['party_type_3']) ? trim($_POST['party_type_3']) : NULL;
    $party_firstname_3 = !empty($_POST['party_firstname_3']) ? trim($_POST['party_firstname_3']) : NULL;
    $party_lastname_3 = !empty($_POST['party_lastname_3']) ? trim($_POST['party_lastname_3']) : NULL;
    $party_phone_3 = !empty($_POST['party_phone_3']) ? phoneStrToNum($_POST['party_phone_3']) : NULL;
    $party_email_3 = !empty($_POST['party_email_3']) ? trim($_POST['party_email_3']) : NULL;

    $party_type_4 = !empty($_POST['party_type_4']) ? trim($_POST['party_type_4']) : NULL;
    $party_firstname_4 = !empty($_POST['party_firstname_4']) ? trim($_POST['party_firstname_4']) : NULL;
    $party_lastname_4 = !empty($_POST['party_lastname_4']) ? trim($_POST['party_lastname_4']) : NULL;
    $party_phone_4 = !empty($_POST['party_phone_4']) ? phoneStrToNum($_POST['party_phone_4']) : NULL;
    $party_email_4 = !empty($_POST['party_email_4']) ? trim($_POST['party_email_4']) : NULL;

    $party_type_5 = !empty($_POST['party_type_5']) ? trim($_POST['party_type_5']) : NULL;
    $party_firstname_5 = !empty($_POST['party_firstname_5']) ? trim($_POST['party_firstname_5']) : NULL;
    $party_lastname_5 = !empty($_POST['party_lastname_5']) ? trim($_POST['party_lastname_5']) : NULL;
    $party_phone_5 = !empty($_POST['party_phone_5']) ? phoneStrToNum($_POST['party_phone_5']) : NULL;
    $party_email_5 = !empty($_POST['party_email_5']) ? trim($_POST['party_email_5']) : NULL;

    // Fourth Card (Office Information)
    $office_contact_date = !empty($_POST['office_contact_date']) ? trim($_POST['office_contact_date']) : NULL;
    $office_means = !empty($_POST['office_means']) ? trim($_POST['office_means']) : NULL;
    $office_initial_date = !empty($_POST['office_initial_date']) ? trim($_POST['office_initial_date']) : NULL;
    $office_location = !empty($_POST['office_location']) ? trim($_POST['office_location']) : NULL;
    $comments = !empty($_POST['comments']) ? trim($_POST['comments']) : NULL;
    $employeeID = $_SESSION['employeeid'];


    /*                 ---------------------
                       | STORED PROCEDURES |
                       ---------------------                  */


    /*                     - Main Procedures -
     Participant PeopleInsert and main addAgencyReferral stored procedures
     Primary and Secondary phone inserts into phone table              */

    $pIDResult = $db->query("SELECT PeopleInsert(
                                       fName := $1::TEXT,
                                       lName := $2::TEXT,
                                       mInit := $3::VARCHAR
                                       );", [$pers_firstname, $pers_lastname, $pers_middlein]);
    $pIDResult = pg_fetch_result($pIDResult, 0);

    // Run the main stored procedure
    $result = $db->query(
        'SELECT addAgencyReferral(
          agencyReferralParticipantID := $1::INT,
          agencyReferralParticipantDateOfBirth := $2::DATE,
          agencyReferralParticipantRace := $3::RACE,
          agencyReferralParticipantSex := $4::SEX,
          houseNum := $5::INTEGER,
          streetAddress := $6::TEXT,
          apartmentInfo := $7::TEXT,
          zipCode := $8::VARCHAR(5),
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
            $pIDResult,
            $pers_dob,
            $pers_race,
            $pers_sex,
            $pers_address_num,
            $pers_address_street,
            $pers_apartment_info,
            $pers_zip,
            $pers_city,
            $pers_state,
            $pers_reason,
            $chkSigned,
            $ref_party,
            $ref_date,
            $additional_info,
            $chkSpecialEd,
            $chkSubAbuse,
            $chkCPS,
            $chkPreg,
            $chkIQ,
            $chkMental,
            $chkViolence,
            $chkReside,
            $office_contact_date,
            $office_means,
            $office_initial_date,
            $office_location,
            $comments,
            $employeeID));
    $formID = pg_fetch_result($result, 0);

    if ($result) {
        $state = pg_result_error_field($result, PGSQL_DIAG_SQLSTATE);
        if ($state != 0) {
            $_SESSION['form-error'] = true;
            $_SESSION['error-state'] = $state;
            header("Location: /form-success");
            die();
        }
    }

    //Insert primary phone number
    if($pers_primphone !== NULL) {
        $phoneResults = $db->query("INSERT INTO FormPhoneNumbers(
                                        formID,
                                        phoneNumber,
                                        phoneType) VALUES ($1, $2, $3);"
            , [$formID, $pers_primphone, "Primary"]);
    }

    // Insert secondary phone number
    if($pers_secphone !== NULL) {
        $secPhoneResults = $db->query("INSERT INTO FormPhoneNumbers(
                                        formID,
                                        phoneNumber,
                                        phoneType) VALUES ($1, $2, $3);"
            , [$formID, $pers_secphone, "Secondary"]);
    }
    /*              - End Main Procedures -             */




    /*              - Agency Stored Procedures -
    Main agency contact member is run through PeopleInsert and then
    agencyMemberInsert with type as main. Other Agencies are run
    through a for loop the same way and added in only if the main
    agency is set.
                                                                    */

    // Insert Main agency contact
    if($ref_firstname !== NULL && $ref_lastname !== NULL){
        $pIDMainAgency = $db->query("SELECT PeopleInsert(
                                       fName := $1::TEXT,
                                       lName := $2::TEXT,
                                       mInit := $3::VARCHAR
                                       );", [$ref_firstname,
                                            $ref_lastname,
                                            NULL]);
        // Get the main agency PID
        $pIDMainAgency = pg_fetch_result($pIDMainAgency, 0);

        // agencyMemberInsert for the main agency contact
        $agencyMemberResult = $db->query("SELECT agencyMemberInsert(
                                agencyMemberID := $1::INT,
                                agen := $2::REFERRALTYPE,
                                phn := $3::TEXT,
                                em := $4::TEXT,
                                isMain := $5::BOOLEAN,
                                arID := $6::INT
                                );", [$pIDMainAgency,
                                    $ref_party,
                                    $ref_phone,
                                    $ref_email,
                                    TRUE,
                                    $formID]);

        // ADDITIONAL REFERRING AGENCIES

        for($i = 1; $i <= 5; $i++){
            $prt_type = "party_type_".$i;
            $prt_first_name = "party_firstname_".$i;
            $prt_last_name = "party_lastname_".$i;
            $prt_phone = "party_phone_".$i;
            $prt_email = "party_email_".$i;


            if($$prt_first_name !== NULL && $$prt_last_name !== NULL) {
                $pidParty = $db->query("SELECT PeopleInsert(
                                           fName := $1::TEXT,
                                           lName := $2::TEXT,
                                           mInit := $3::VARCHAR
                                           );", [$$prt_first_name,
                                                $$prt_last_name,
                                                NULL]);
                $pidParty = pg_fetch_result($pidParty, 0);

                $formatted_phone = phoneStrToNum($$prt_phone);

                $partyResult = $db->query("SELECT agencyMemberInsert(
                                agencyMemberID := $1::INT,
                                agen := $2::referraltype,
                                phn := $3::TEXT,
                                em := $4::text,
                                isMain := $5::boolean,
                                arID := $6::int
                                );", [$pidParty,
                                    $$prt_type,
                                    $formatted_phone,
                                    $$prt_email,
                                    0,
                                    $formID]);
            }
        }
    }
    /*            - End Agency Stored Procedures -              */



    /*          - Household Members Stored Procedure -
    Uses a for loop to iterate through every instance of family_
    variables, checks to see whether it should run through based
    off of if the first or last name is set
                                                                    */
    for($i = 1; $i <= 5; $i++){
        // Create variable names
        $fam_first_name = "family_first_name_".$i;
        $fam_last_name = "family_last_name_".$i;
        $fam_mi = "family_mi_".$i;
        $fam_relationship = "family_relationship_".$i;
        $fam_dob = "family_dob_".$i;
        $fam_race = "family_race_".$i;
        $fam_sex = "family_sex_".$i;

        // Check to see if the household member is a child
        if ($$fam_relationship === "Son" || $$fam_relationship === "Daughter")
            $isChild = 1;
        else
            $isChild = 0;

        // Run InsertPeople for current household member
        if($$fam_first_name !== NULL && $$fam_last_name !== NULL){
            // Run createFamilyMember for current household member
            $familyResult = $db->query("SELECT createFamilyMember(
                                            familyMemberFName := $1::TEXT,
                                            familyMemberLName := $2::TEXT,
                                            familyMemberMiddleInit := $3::VARCHAR(1),
                                            rel := $4::RELATIONSHIP,
                                            dob := $5::DATE,
                                            race := $6::RACE,
                                            sex := $7::SEX,
                                            child := $8::BOOLEAN,
                                            cust := $9::TEXT,
                                            loc := $10::TEXT,
                                            fID := $11::INT)", [$$fam_first_name, $$fam_last_name, $$fam_mi, $$fam_relationship, $$fam_dob, $$fam_race, $$fam_sex, $isChild, NULL, NULL, $formID]);


        }
    }
    /*              -  End Household Stored Procedures -        */

    /*                   END OF STORED PROCEDURES                */



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

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Race:</label>
                                        <div class="col-sm-2">
                                            <select class="form-control " name="pers_race" id="pers_race">
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
                                        <label class="col-sm-2 col-form-label">Sex:</label>
                                        <div class="col-sm-2">
                                            <select class="form-control" name="pers_sex" id="pers_sex">
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

                                    <h5>Contact Information</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="pers_address">Street Address:</label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" name="pers_address" id="pers_address" placeholder="Street address">
                                        </div>
                                        <label class="col-form-label col-sm-1" for="pers_apt_info">Apartment:</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" name="pers_apt_info" id="pers_apt_info" placeholder="Apartment Information">
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
                                        <label class="col-form-label col-sm-2" for="pers_zip">ZIP:</label>
                                        <div class="col-sm-1">
                                            <input type="text" class="form-control mask-zip" name="pers_zip" id="pers_zip" placeholder="Zip">
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
                                    <a data-toggle="collapse"  class="form-header" data-parent="#accordion" onfocusin="section2()" id="pers_referring_party_info" href="#collapse2">Referring Party Information</a>
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

                            <div class="modal fade" id="memberModal" tabindex="-1" role="dialog" aria-labelledby="memberModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="memberModalLabel">Remove Household Member?</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you wish to remove this household member? This action cannot be undone.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" id ="memberConfirm" class="btn cpca" data-dismiss="modal">OK</button>
                                            <button type="button" id ="memberCancel" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
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
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Add Member:</label>
                                        <div class="col-sm-1">
                                            <button class="btn btn-default" type="button" id="btnAddMember"><span class="fa fa-plus"></span></button> <!-- every other dropdown on this form uses down arrows -->
                                        </div>

                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Remove Member:</label>
                                        <div class="col-sm-1">
                                            <button class="btn btn-default" type="button" id="btnDelMember" disabled="disabled"><span class="fa fa-minus"></span></button> <!-- every other dropdown on this form uses down arrows -->
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

                                        <div class="modal fade" id="agencyModal" tabindex="-1" role="dialog" aria-labelledby="agencyModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="agencyModalLabel">Remove Party?</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you wish to remove this party? This action cannot be undone.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" id ="agencyConfirm" class="btn cpca" data-dismiss="modal">OK</button>
                                                        <button type="button" id ="agencyCancel" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    </div>
                                                </div>
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
                                            <button class="btn btn-default" type="button" id="btnAddParty"><span class="fa fa-plus"></span></button>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Remove Party:</label>
                                        <div class="col-sm-1">
                                            <button class="btn btn-default" type="button" id="btnDelParty"><span class="fa fa-minus"></span></button>
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