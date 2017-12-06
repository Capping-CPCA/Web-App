<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Intake-Packet that participants fill out upon enrolling in PEP programs.
 *
 * The Intake-Packet Form is a form that is filled out and submitted by a participant before enrolling in a PEP parenting program.
 *
 * @author Stephen Bohner and Christian Menk
 * @copyright 2017 Marist College
 * @version 1.0.0
 * @since 1.0.0
 */

authorizedPage();
global $db, $params, $route, $view;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $form_type = "intake packet";

    // First Card (Participant Information)
    $intake_firstname = !empty($_POST['intake_firstname']) ? trim($_POST['intake_firstname']) : NULL;
    $intake_lastname = !empty($_POST['intake_lastname']) ? trim($_POST['intake_lastname']) : NULL;
    $intake_middlein = !empty($_POST['intake_middlein']) ? trim($_POST['intake_middlein']) : NULL;
    $intake_dob = !empty($_POST['intake_dob']) ? trim($_POST['intake_dob']) : NULL;
    $intake_religion = !empty($_POST['intake_religion']) ? trim($_POST['intake_religion']) : NULL;
    $intake_ethnicity = !empty($_POST['intake_ethnicity']) ? $_POST['intake_ethnicity'] : NULL;
    $intake_sex = !empty($_POST['intake_sex']) ? $_POST['intake_sex'] : NULL;
    $intake_occupation = !empty($_POST['intake_occupation']) ? trim($_POST['intake_occupation']) : NULL;
    $intake_last_year_school = !empty($_POST['intake_last_year_school']) ? trim($_POST['intake_last_year_school']) : NULL;
    $intake_languages_spoken = !empty($_POST['intake_languages_spoken']) ? trim($_POST['intake_languages_spoken']) : NULL;
    $intake_handicap_medication = !empty($_POST['handicap_medication']) ? trim($_POST['handicap_medication']) : NULL;
    $intake_address = !empty($_POST['intake_address']) ? trim($_POST['intake_address']) : NULL;
    $intake_intake_apt_info = !empty($_POST['intake_apt_info']) ? trim($_POST['intake_apt_info']) : NULL;

    // Logic for parsing the address into the address number and street name.
    $intake_street_num = NULL;
    $intake_street_name = NULL;

    if ($intake_address !== NULL) {
        $intake_address_info = explode(" ", $intake_address);

        // Loop to parse through the address array ($self_address_info)
        for($i = 0; $i < sizeOf($intake_address_info); $i++){
            if($i === 0){
                if($intake_address_info[$i] !== "") {
                    if(is_numeric($intake_address_info[$i])){
                        $intake_street_num = $intake_address_info[$i];
                    } else {
                        $intake_street_name .= " ".$intake_address_info[$i];
                    }
                }
            } else {
                $intake_street_name .= $intake_address_info[$i] . " ";
            }
        }
    }

    $intake_state = !empty($_POST['intake_state']) ? $_POST['intake_state'] : "New York";
    $intake_city = !empty($_POST['intake_city']) ? trim($_POST['intake_city']) : "Poughkeepsie";
    $intake_zip = !empty($_POST['intake_zip']) ? $_POST['intake_zip'] : 12601;
    $intake_phone_day = !empty($_POST['intake_phone_day']) ? phoneStrToNum($_POST['intake_phone_day']) : NULL;
    $intake_phone_night = !empty($_POST['intake_phone_night']) ? phoneStrToNum($_POST['intake_phone_night']) : NULL;
    // Emergency Contact
    $contact_relationship = !empty($_POST['contact_relationship']) ? $_POST['contact_relationship'] : NULL;
    $contact_phone = !empty($_POST['contact_phone']) ? phoneStrToNum($_POST['contact_phone']) : NULL;

    // Second Card (Participant Children Information)
    $child_first_name_1 = !empty($_POST['child_first_name_1']) ? trim($_POST['child_first_name_1']) : NULL;
    $child_last_name_1 = !empty($_POST['child_last_name_1']) ? trim($_POST['child_last_name_1']) : NULL;
    $child_mi_1 = !empty($_POST['child_mi_1']) ? trim($_POST['child_mi_1']) : NULL;
    $child_dob_1 = !empty($_POST['child_dob_1']) ? $_POST['child_dob_1'] : NULL;
    $child_sex_1 = !empty($_POST['child_sex_1']) ? $_POST['child_sex_1'] : NULL;
    $child_race_1 = !empty($_POST['child_race_1']) ? $_POST['child_race_1'] : NULL;
    $child_live_1 = !empty($_POST['child_live_1']) ? trim($_POST['child_live_1']) : NULL;
    $child_custody_1 = !empty($_POST['child_custody_1']) ? trim($_POST['child_custody_1']) : NULL;
    // 2nd Child Clone
    $child_first_name_2 = !empty($_POST['child_first_name_2']) ? trim($_POST['child_first_name_2']) : NULL;
    $child_last_name_2 = !empty($_POST['child_last_name_2']) ? trim($_POST['child_last_name_2']) : NULL;
    $child_mi_2 = !empty($_POST['child_mi_2']) ? trim($_POST['child_mi_2']) : NULL;
    $child_dob_2 = !empty($_POST['child_dob_2']) ? $_POST['child_dob_2'] : NULL;
    $child_sex_2 = !empty($_POST['child_sex_2']) ? $_POST['child_sex_2'] : NULL;
    $child_race_2 = !empty($_POST['child_race_2']) ? $_POST['child_race_2'] : NULL;
    $child_live_2 = !empty($_POST['child_live_2']) ? trim($_POST['child_live_2']) : NULL;
    $child_custody_2 = !empty($_POST['child_custody_2']) ? trim($_POST['child_custody_2']) : NULL;
    // 3rd Child Clone
    $child_first_name_3 = !empty($_POST['child_first_name_3']) ? trim($_POST['child_first_name_3']) : NULL;
    $child_last_name_3 = !empty($_POST['child_last_name_3']) ? trim($_POST['child_last_name_3']) : NULL;
    $child_mi_3 = !empty($_POST['child_mi_3']) ? trim($_POST['child_mi_3']) : NULL;
    $child_dob_3 = !empty($_POST['child_dob_3']) ? $_POST['child_dob_3'] : NULL;
    $child_sex_3 = !empty($_POST['child_sex_3']) ? $_POST['child_sex_3'] : NULL;
    $child_race_3 = !empty($_POST['child_race_3']) ? $_POST['child_race_3'] : NULL;
    $child_live_3 = !empty($_POST['child_live_3']) ? trim($_POST['child_live_3']) : NULL;
    $child_custody_3 = !empty($_POST['child_custody_3']) ? trim($_POST['child_custody_3']) : NULL;
    // 4th Child Clone
    $child_first_name_4 = !empty($_POST['child_first_name_4']) ? trim($_POST['child_first_name_4']) : NULL;
    $child_last_name_4 = !empty($_POST['child_last_name_4']) ? trim($_POST['child_last_name_4']) : NULL;
    $child_mi_4 = !empty($_POST['child_mi_4']) ? trim($_POST['child_mi_4']) : NULL;
    $child_dob_4 = !empty($_POST['child_dob_4']) ? $_POST['child_dob_4'] : NULL;
    $child_sex_4 = !empty($_POST['child_sex_4']) ? $_POST['child_sex_4'] : NULL;
    $child_race_4 = !empty($_POST['child_race_4']) ? $_POST['child_race_4'] : NULL;
    $child_live_4 = !empty($_POST['child_live_4']) ? trim($_POST['child_live_4']) : NULL;
    $child_custody_4 = !empty($_POST['child_custody_4']) ? trim($_POST['child_custody_4']) : NULL;
    // 5th Child Clone
    $child_first_name_5 = !empty($_POST['child_first_name_5']) ? trim($_POST['child_first_name_5']) : NULL;
    $child_last_name_5 = !empty($_POST['child_last_name_5']) ? trim($_POST['child_last_name_5']) : NULL;
    $child_mi_5 = !empty($_POST['child_mi_5']) ? trim($_POST['child_mi_5']) : NULL;
    $child_dob_5 = !empty($_POST['child_dob_5']) ? $_POST['child_dob_5'] : NULL;
    $child_sex_5 = !empty($_POST['child_sex_5']) ? $_POST['child_sex_5'] : NULL;
    $child_race_5 = !empty($_POST['child_race_5']) ? $_POST['child_race_5'] : NULL;
    $child_live_5 = !empty($_POST['child_live_5']) ? trim($_POST['child_live_5']) : NULL;
    $child_custody_5 = !empty($_POST['child_custody_5']) ? trim($_POST['child_custody_5']) : NULL;

    // Third Card (Participant Family Questions)
    if (!empty($_POST['drug_alcohol_abuse'])) {
        $drug_alcohol_abuse = $_POST['drug_alcohol_abuse'] === "Yes" ? 1 : 0;
    } else {
        $drug_alcohol_abuse = NULL;
    }
    $drug_alcohol_abuse_explain = !empty($_POST['drug_alcohol_abuse_explain']) ? trim($_POST['drug_alcohol_abuse_explain']) : NULL;
    if (!empty($_POST['live_with_children'])) {
        $live_with_children = $_POST['live_with_children'] === "Yes" ? 1 : 0;
    } else {
        $live_with_children = NULL;
    }
    $live_with_children_separated = !empty($_POST['live_with_children_separated']) ? trim($_POST['live_with_children_separated']) : NULL;
    if (!empty($_POST['parent_separated'])) {
        $parent_separated = $_POST['parent_separated'] === "Yes" ? 1 : 0;
    } else {
        $parent_separated = NULL;
    }
    $separated_length = !empty($_POST['separated_length']) ? trim($_POST['separated_length']) : NULL;
    $relationship = !empty($_POST['relationship']) ? trim($_POST['relationship']) : NULL;
    if (!empty($_POST['parenting'])) {
        $parenting = $_POST['parenting'] === "Yes" ? 1 : 0;
    } else {
        $parenting = NULL;
    }
    if (!empty($_POST['child_protective'])) {
        $child_protective = $_POST['child_protective'] === "Yes" ? 1 : 0;
    } else {
        $child_protective = NULL;
    }
    if (!empty($_POST['previous_child_protective'])) {
        $previous_child_protective = $_POST['previous_child_protective'] === "Yes" ? 1 : 0;
    } else {
        $previous_child_protective = NULL;
    }
    if (!empty($_POST['mandated'])) {
        $mandated = $_POST['mandated'] === "Yes" ? 1 : 0;
    } else {
        $mandated = NULL;
    }
    $mandated_by = !empty($_POST['mandated_by']) ? trim($_POST['mandated_by']) : NULL;
    $reason_mandated = !empty($_POST['reason_mandated']) ? trim($_POST['reason_mandated']) : NULL;
    $reason_for_taking_class = !empty($_POST['reason_for_taking_class']) ? trim($_POST['reason_for_taking_class']) : NULL;
    // Checks for whether reason mandated, or reason for taking class should be recorded.
    $reason_for_attendance = NULL;
    if ($reason_mandated !== NULL) {
        $reason_for_attendance = $reason_mandated;
    } else {
        $reason_for_attendance = $reason_for_taking_class;
    }
    $class_participation = !empty($_POST['class_participation']) ? trim($_POST['class_participation']) : NULL;
    $parenting_opinion = !empty($_POST['parenting_opinion']) ? trim($_POST['parenting_opinion']) : NULL;
    if (!empty($_POST['other_classes'])) {
        $other_classes = $_POST['other_classes'] === "Yes" ? 1 : 0;
    } else {
        $other_classes = NULL;
    }
    $other_classes_where_when = !empty($_POST['other_classes_where_when']) ? trim($_POST['other_classes_where_when']) : NULL;
    if (!empty($_POST['victim_of_abuse'])) {
        $victim_of_abuse = $_POST['victim_of_abuse'] === "Yes" ? 1 : 0;
    } else {
        $victim_of_abuse = NULL;
    }
    $form_of_abuse = !empty($_POST['form_of_abuse']) ? trim($_POST['form_of_abuse']) : NULL;
    if (!empty($_POST['abuse_therapy'])) {
        $abuse_therapy = $_POST['abuse_therapy'] === "Yes" ? 1 : 0;
    } else {
        $abuse_therapy = NULL;
    }
    if (!empty($_POST['childhood_abuse_relating'])) {
        $childhood_abuse_relating = $_POST['childhood_abuse_relating'] === "Yes" ? 1 : 0;
    } else {
        $childhood_abuse_relating = NULL;
    }
    $class_takeaway = !empty($_POST['class_takeaway']) ? trim($_POST['class_takeaway']) : NULL;
    
    // Fourth Card (Participant History Questions)
    if (!empty($_POST['domestic_violence'])) {
        $domestic_violence = $_POST['domestic_violence'] === "Yes" ? 1 : 0;
    } else {
        $domestic_violence = NULL;
    }
    if (!empty($_POST['domestic_violence_discussed'])) {
        $domestic_violence_discussed = $_POST['domestic_violence_discussed'] === "Yes" ? 1 : 0;
    } else {
        $domestic_violence_discussed = NULL;
    }
    if (!empty($_POST['history_violence_family'])) {
        $history_violence_family = $_POST['history_violence_family'] === "Yes" ? 1 : 0;
    } else {
        $history_violence_family = NULL;
    }
    if (!empty($_POST['history_violence_nuclear'])) {
        $history_violence_nuclear = $_POST['history_violence_nuclear'] === "Yes" ? 1 : 0;
    } else {
        $history_violence_nuclear = NULL;
    }
    if (!empty($_POST['protection_order'])) {
        $protection_order = $_POST['protection_order'] === "Yes" ? 1 : 0;
    } else {
        $protection_order = NULL;
    }
    $protection_order_explain = !empty($_POST['protection_order_explain']) ? trim($_POST['protection_order_explain']) : NULL;
    if (!empty($_POST['crime_arrested'])) {
        $crime_arrested = $_POST['crime_arrested'] === "Yes" ? 1 : 0;
    } else {
        $crime_arrested = NULL;
    }
    if (!empty($_POST['crime_convicted'])) {
        $crime_convicted = $_POST['crime_convicted'] === "Yes" ? 1 : 0;
    } else {
        $crime_convicted = NULL;
    }
    $crime_explain = !empty($_POST['crime_explain']) ? trim($_POST['crime_explain']) : NULL;
    if (!empty($_POST['jail_prison_record'])) {
        $jail_prison_record = $_POST['jail_prison_record'] === "Yes" ? 1 : 0;
    } else {
        $jail_prison_record = NULL;
    }
    $jail_prison_explain = !empty($_POST['jail_prison_explain']) ? trim($_POST['jail_prison_explain']) : NULL;
    if (!empty($_POST['parole_probation'])) {
        $parole_probation = $_POST['parole_probation'] === "Yes" ? 1 : 0;
    } else {
        $parole_probation = NULL;
    }
    $parole_probation_explain = !empty($_POST['parole_probation_explain']) ? trim($_POST['parole_probation_explain']) : NULL;
    if (!empty($_POST['family_members_taking_class'])) {
        $family_members_taking_class = $_POST['family_members_taking_class'] === "Yes" ? 1 : 0;
    } else {
        $family_members_taking_class = NULL;
    }
    $family_members = !empty($_POST['family_members']) ? trim($_POST['family_members']) : NULL;
    $datestamp = date("Y-m-d");
    $eID = $_SESSION['employeeid'];

    // Stored Procedures
    // Gets the participant ID related to the person who is filling out the form to associate it with the form ID.
   
        $pIDResult = checkForDuplicates($db, $intake_firstname, $intake_lastname, $intake_middlein);

    // Inserts the intake packet data into the database and associates the form with a participant ID.
    $formID = $db->query("SELECT registerParticipantIntake(
                                      intakeParticipantID := $1::INT,
                                      intakeParticipantDOB := $2::DATE,
                                      intakeParticipantRace := $3::RACE,
                                      intakeParticipantSex := $4::SEX,
                                      housenum := $5::INT,
                                      streetaddress := $6::TEXT,
                                      apartmentInfo := $7::TEXT,
                                      zipcode := $8::VARCHAR(5),
                                      city := $9::TEXT,
                                      state := $10::STATES,
                                      occupation := $11::TEXT,
                                      religion := $12::TEXT,
                                      handicapsormedication := $13::TEXT,
                                      lastyearschool := $14::TEXT,
                                      hasdrugabusehist := $15::BOOLEAN,
                                      substanceabusedescr := $16::TEXT,
                                      timeSeparatedFromChildren := $17::TEXT,
                                      timeseparatedfrompartner := $18::TEXT,
                                      relationshiptootherparent := $19::TEXT,
                                      hasparentingpartnershiphistory := $20::BOOLEAN,
                                      hasInvolvementCPS := $21::BOOLEAN,
                                      hasprevinvolvmentcps := $22::TEXT,
                                      ismandatedtotakeclass := $23::BOOLEAN,
                                      whomandatedclass := $24::TEXT,
                                      reasonforattendence := $25::TEXT,
                                      safeparticipate := $26::TEXT,
                                      preventparticipate := $27::TEXT,
                                      hasattendedotherparenting := $28::BOOLEAN,
                                      kindofparentingclasstaken := $29 ::TEXT,
                                      victimchildabuse := $30::BOOLEAN,
                                      formofchildhoodabuse := $31::TEXT,
                                      hashadtherapy := $32::BOOLEAN,
                                      stillissuesfromchildabuse := $33::BOOLEAN,
                                      mostimportantliketolearn := $34::TEXT,
                                      hasdomesticviolencehistory := $35::BOOLEAN,
                                      hasdiscusseddomesticviolence := $36::BOOLEAN,
                                      hashistorychildabuseoriginfam := $37::BOOLEAN,
                                      hashistoryviolencenuclearfamily := $38::BOOLEAN,
                                      ordersofprotectioninvolved := $39::BOOLEAN,
                                      reasonforordersofprotection := $40::TEXT,
                                      hasbeenarrested := $41::BOOLEAN,
                                      hasbeenconvicted := $42::BOOLEAN,
                                      reasonforarrestorconviction := $43::TEXT,
                                      hasJailPrisonRecord := $44::BOOLEAN,
                                      offensejailprisonrec := $45::TEXT,
                                      currentlyonparole := $46::BOOLEAN,
                                      onparoleforwhatoffense := $47::TEXT,
                                      lang := $48::TEXT,
                                      ptpmainformsigneddate := $49::DATE,
                                      ptpenrollmentsigneddate := $50::DATE,
                                      familyMembersTakingClass := $51::BOOLEAN,
                                      familyMemberNamesTakingClass := $52::TEXT,
                                      ptpconstentreleaseformsigneddate := $53::DATE,
                                      eID := $54::INT
                                      );", [$pIDResult, $intake_dob, $intake_ethnicity, $intake_sex, $intake_street_num, $intake_street_name, $intake_intake_apt_info, $intake_zip, $intake_city, $intake_state,
                                            $intake_occupation, $intake_religion, $intake_handicap_medication, $intake_last_year_school, $drug_alcohol_abuse, $drug_alcohol_abuse_explain, $live_with_children_separated,
                                            $separated_length, $relationship, $parenting, $child_protective, $previous_child_protective, $mandated, $mandated_by, $reason_for_attendance, $class_participation, $parenting_opinion,
                                            $other_classes, $other_classes_where_when, $victim_of_abuse, $form_of_abuse, $abuse_therapy, $childhood_abuse_relating, $class_takeaway, $domestic_violence, $domestic_violence_discussed,
                                            $history_violence_family, $history_violence_nuclear, $protection_order, $protection_order_explain, $crime_arrested, $crime_convicted, $crime_explain,
                                            $jail_prison_record, $jail_prison_explain, $parole_probation, $parole_probation_explain, $intake_languages_spoken, $datestamp, $datestamp, $family_members_taking_class, $family_members, $datestamp, $eID]);

    if ($formID) {
        $state = pg_result_error_field($formID, PGSQL_DIAG_SQLSTATE);
        if ($state != 0) {
            die(pg_result_error($formID));
            $_SESSION['form-error'] = true;
            $_SESSION['error-state'] = $state;
            header("Location: /form-success");
            die();
        }
    }

    $formID = pg_fetch_result($formID, 0);

    // Inserts the day, evening, and emergency contact phone numbers into the FormPhoneNumbers table.
    if ($intake_phone_day !== NULL) {
        $dayPhoneResult = $db->query("INSERT INTO FormPhoneNumbers (formID, phoneNumber, phoneType)
                                    VALUES ($1, $2, $3);", [$formID, $intake_phone_day, 'Day']);
    }

    if ($intake_phone_night !== NULL) {
        $eveningPhoneResult = $db->query("INSERT INTO FormPhoneNumbers (formID, phoneNumber, phoneType)
                                    VALUES ($1, $2, $3);", [$formID, $intake_phone_night, 'Evening']);
    }

    if ($contact_phone !== NULL) {
        $emergencyContact = $db->query("INSERT INTO FormPhoneNumbers (formID, phoneNumber, phoneType)
                                    VALUES ($1, $2, $3);", [$formID, $contact_phone, 'Primary']);
    }

    // Child stored procedures (handles entering multiple children for an intake packet).
    for($i = 1; $i <= 5; $i++){
        // Create variable names
        $chd_first_name = "child_first_name_".$i;
        $chd_last_name = "child_last_name_".$i;
        $chd_mi = "child_mi_".$i;
        $chd_dob = "child_dob_".$i;
        $chd_race = "child_race_".$i;
        $chd_sex = "child_sex_".$i;
        $chd_live = "child_live_".$i;
        $chd_custody = "child_custody_".$i;

        // Run InsertPeople for current child
        if($$chd_first_name !== NULL && $$chd_last_name !== NULL){

            if($$chd_first_name !== NULL && $$chd_last_name !== NULL){
                $childResult = $db->query("SELECT createFamilyMember(
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
                                            fID := $11::INT)", [$$chd_first_name, $$chd_last_name, $$chd_mi, NULL, $$chd_dob, $$chd_race, $$chd_sex, TRUE, $$chd_custody, $$chd_live, $formID]);
            }

        }

    }

    // Redirect user to success page.
    $_SESSION['form-type'] = $form_type;
    header("Location: /form-success");
    die();

}


include('header.php');
?>

    <!-- Page Content -->
    <div id="page-content-wrapper" style="width:100%">
		<div class="container-fluid controls" align="right">
			<button type="button" class="btn cpca" onclick="window.print()"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
		</div>
        <div class="container-fluid">

            <div class="dropdown">

                <form id="intake_packet" action="/intake-packet" method="post" novalidate>
                    <div id="accordion" role="tablist" aria-multiselectable="true">
                        <br>
                        <!-- first collapsible -->
                        <div class="card">
                            <div class="card-header" role="tab">
                                <h5 class="card-title" style="font-weight: normal;">
                                    <a data-toggle="collapse" id="intake_participantInfo_title" class="form-header" data-parent="#accordion" onfocusin="section1()" href="#collapse1">Participant Information</a>
                                </h5>
                            </div>

                            <div id="collapse1" class="collapse show" role="tabpanel">
                                <div class="card-body">
                                    <h5>Personal Information</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="intake_firstname">Participant Name:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" id="intake_firstname" name="intake_firstname" placeholder="First name" required>
                                            <div class="invalid-feedback">Enter first name</div>
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="intake_lastname">Last Name:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" id="intake_lastname" name="intake_lastname" placeholder="Last name" required>
                                            <div class="invalid-feedback">Enter last name</div>
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="intake_middlein">MInitial:</label>
                                        <div class="col-sm-1 col">
                                            <input type="text" class="form-control" id="intake_middlein" name="intake_middlein" placeholder="Initial" maxlength="1">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="intake_dob">Date of Birth:</label>
                                        <div class="col-sm-2 col">
                                            <input type="date" class="form-control" id="intake_dob" name="intake_dob">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="intake_religion">Religion:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" id="intake_religion" name="intake_religion" placeholder="Religion">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="intake_ethnicity">Race:</label>
                                        <div class="col-sm-2 col">
                                            <select class="form-control select_sex" name="intake_ethnicity" id="intake_ethnicity">
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
                                        <label class="col-form-label col-sm-2" for="intake_sex">Sex:</label>
                                        <div class="col-sm-2 col">
                                            <select class="form-control select_sex" name="intake_sex" id="intake_sex">
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
                                        <label class="col-form-label col-sm-2" for="intake_occupation">Occupation:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" id="intake_occupation" name="intake_occupation" placeholder="Enter an occupation">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="intake_last_year_school">Last Year of School:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" id="intake_last_year_school" name="intake_last_year_school" placeholder="example: 1988">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="intake_languages_spoken">Languages Spoken:</label>
                                        <div class="col-sm-3 col">
                                            <input type="text" class="form-control" id="intake_languages_spoken" name="intake_languages_spoken" placeholder="English, Spanish, etc...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="handicap_medication">Handicap/Medication:</label>
                                        <div class="col-sm-3 col">
                                            <textarea style="resize: none;" class="form-control" rows=4 id="handicap_medication" name="handicap_medication" placeholder="Any handicapping conditions or medications"></textarea>
                                        </div>
                                    </div>

                                    <h5>Contact Information</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 col-2" for="intake_address">Street Address:</label>
                                        <div class="col-sm-3 col">
                                            <input type="text" class="form-control" id="intake_address" name="intake_address" placeholder="Street address">
                                        </div>
                                        <label class="col-form-label col-sm-1 col-2" for="intake_apt_info">Apartment:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" id="intake_apt_info" name="intake_apt_info" placeholder="Apartment Information">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="intake_state">State:</label>
                                        <div class="col-sm-3 col">
                                            <select class="form-control" id="intake_state" name="intake_state" >
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

                                        <label class="col-form-label col-sm-1 col-2" for="intake_city">City:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" id="intake_city" name="intake_city" placeholder="City" data-error="Enter city.">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="intake_zip">ZIP:</label>
                                        <div class="col-sm-1 col">
                                            <input type="text" class="form-control mask-zip" id="intake_zip" name="intake_zip" placeholder="Zip">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="intake_phone_day">Daytime Phone:</label>
                                        <div class="col-sm-2 col">
                                            <input type="tel" class="form-control mask-phone feedback-icon" id="intake_phone_day" name="intake_phone_day" placeholder="(999) 999-9999">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="intake_phone_night">Evening Phone:</label>
                                        <div class="col-sm-2 col">
                                            <input type="tel" class="form-control mask-phone feedback-icon" id="intake_phone_night" name="intake_phone_night" placeholder="(999) 999-9999">
                                        </div>
                                    </div>
                                    <br>

                                    <h5>Emergency Contact</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label col-3" for="contact_relationship">Relationship:</label>
                                        <div class="col-sm-2 col">
                                            <select class="form-control" name="contact_relationship" id="contact_relationship">
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
                                        <label class="col-form-label col-sm-2 col-3" for="contact_phone">Phone Number:</label>
                                        <div class="col-sm-2 col">
                                            <input type="tel" class="form-control mask-phone feedback-icon" id="contact_phone" name="contact_phone" placeholder="(999) 999-9999">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- 1st collapsible end -->
                        <br>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title" style="font-weight: normal;">
                                    <a data-toggle="collapse" class="form-header" onfocusin="section2()" data-parent="#accordion" href="#collapse2">Participant Children Information</a>
                                </h5>
                            </div>

                            <div class="modal fade" id="childModal" tabindex="-1" role="dialog" aria-labelledby="childModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="childModalLabel">Remove Child?</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you wish to remove this child? This action cannot be undone.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" id ="childConfirm" class="btn cpca" data-dismiss="modal">OK</button>
                                            <button type="button" id ="childCancel" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="collapse2" class="collapse">
                                <div class="card-body">
                                    <div id="childEntry_1" class="clonedChild">
                                        <h5 class="heading-reference">Child 1</h5>
                                        <br>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label label_fn">Child Name:</label>
                                            <div class="col-sm-2 col">
                                                <input type="text" class="form-control input_fn" name="child_first_name_1" maxlength="255" placeholder="First name">
                                            </div>
                                            <label class="col-sm-0 col-form-label sr-only label_ln">Last Name:</label>
                                            <div class="col-sm-2 col">
                                                <input type="text" class="form-control input_ln" name="child_last_name_1" maxlength="255" placeholder="Last name">
                                            </div>
                                            <label class="col-sm-0 col-form-label sr-only label_mi">Middle Initial:</label>
                                            <div class="col-sm-1 col">
                                                <input type="text" class="form-control input_mi" name="child_mi_1" maxlength="1" placeholder="Initial">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label label_dob" for="child_dob_1">Date of Birth:</label>
                                            <div class="col-sm-2 col">
                                                <input type="date" class="form-control input_dob" name="child_dob_1" id="child_dob_1">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label label_sex">Sex:</label>
                                            <div class="col-sm-2 col">
                                                <select class="form-control select_sex" name="child_sex_1" id="child_sex_1">
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
                                            <div class="col-sm-2 col">
                                                <select class="form-control select_race" name="child_race_1" id="child_race_1">
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
                                            <label class="col-sm-2 col-form-label label_live">Residence:</label>
                                            <div class="col-sm-4 col">
                                                <input type="text" class="form-control input_live" name="child_live_1" id="child_live_1" placeholder="Where does this child live?">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label label_custody">Custody:</label>
                                            <div class="col-sm-4 col">
                                                <input type="text" class="form-control input_custody" name="child_custody_1" id="child_custody_1" placeholder="Who has custody of this child?">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group row controls">
                                        <label class="col-sm-2 col-form-label">Add Child:</label>
                                        <div class="col-sm-1 col">
                                            <button class="btn btn-default" type="button" id="btnAddChild"><span class="fa fa-plus"></span></button>
                                        </div>

                                    </div>

                                    <div class="form-group row controls">
                                        <label class="col-sm-2 col-form-label">Remove Child:</label>
                                        <div class="col-sm-1 col">
                                            <button class="btn btn-default" type="button" id="btnDelChild" disabled="disabled"><span class="fa fa-minus"></span></button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <br>

                        <!-- 3rd collapsible -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title" style="font-weight: normal;">
                                    <a data-toggle="collapse" data-parent="#accordion" onfocusin="section3()" class="form-header" href="#collapse3">Participant Family Questions</a>
                                </h5>
                            </div>

                            <div id="collapse3" class="collapse">
                                <div class="card-body" align="left">
                                    <h5>Additional Participant Information</h5>
                                    <br>
                                    <div class="form-group radio-group row">
                                        <div class="col-sm-4">
                                            <label class="form-control-label">Do you now, or have you ever had a problem with drug/alcohol abuse?</label>
                                        </div>
                                        <label class="custom-control custom-radio" for="drug_alcohol_abuse_yes">
                                            <input type="radio" id="drug_alcohol_abuse_yes" name="drug_alcohol_abuse" class="custom-control-input" value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="drug_alcohol_abuse_no">
                                            <input type="radio" id="drug_alcohol_abuse_no" name="drug_alcohol_abuse" class="custom-control-input" value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Drug/Alcohol Abuse -->

                                    <div class="form-group hidden-field row drug_alcohol_abuse_div_yes answer_yes">
                                        <label class="col-form-label col-sm-2" for="drug_alcohol_abuse_explain">Please explain:</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="drug_alcohol_abuse_explain" name="drug_alcohol_abuse_explain" placeholder="Please describe your past with drug/alcohol abuse">
                                        </div>
                                    </div>

                                    <!-- Begin Q: Live With Children -->
                                    <div class="form-group radio-group row">
                                        <div class="col-sm-4">
                                            <label class="form-control-label">Do you currently live with your child(ren)?</label>
                                        </div>
                                        <label class="custom-control custom-radio" for="live_with_children_yes">
                                            <input  type="radio" id="live_with_children_yes" name="live_with_children" class="custom-control-input" value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="live_with_children_no">
                                            <input type="radio" id="live_with_children_no" name="live_with_children" class="custom-control-input" value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Live With Children -->

                                    <div class="form-group hidden-field row live_with_children_div answer_no">
                                        <label class="col-form-label col-sm-4" for="live_with_children_separated">Length of Separation:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="live_with_children_separated" name="live_with_children_separated" placeholder="For how long have you been separated from your child(ren)?">
                                        </div>
                                    </div>

                                    <!-- Begin Q: Separated With Children -->
                                    <div class="form-group radio-group row">
                                        <div class="col-sm-4">
                                            <label class="form-control-label">Are you separated with your child(ren)'s other biological parent?</label>
                                        </div>
                                        <label class="custom-control custom-radio" for="parent_separated_yes">
                                            <input  type="radio" id="parent_separated_yes" name="parent_separated" class="custom-control-input" value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="parent_separated_no">
                                            <input type="radio" id="parent_separated_no" name="parent_separated" class="custom-control-input" value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Separated With Children-->

                                    <div class="form-group hidden-field row parent_separated_div_yes answer_yes">
                                        <label class="col-form-label col-sm-2" for="separated_length">Please explain:</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="separated_length" name="separated_length" placeholder="For how long have you been separated?">
                                        </div>
                                    </div>

                                    <div  class="form-group hidden-field row parent_separated_div_yes answer_yes">
                                        <label class="col-form-label col-sm-2" for="relationship">Relationship status:</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="relationship" name="relationship" placeholder="What is your relationship like?">
                                        </div>
                                    </div>

                                    <!-- Begin Q: Parent Together -->
                                    <div class="form-group radio-group row">
                                        <div class="col-sm-4">
                                            <label class="form-control-label">Have you and your child(ren)'s parent been able to parent together?</label>
                                        </div>
                                        <label class="custom-control custom-radio" for="parenting_yes">
                                            <input  type="radio" id="parenting_yes" name="parenting" class="custom-control-input" value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="parenting_no">
                                            <input type="radio" id="parenting_no" name="parenting" class="custom-control-input" value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Parent Together -->

                                    <!-- Begin Q: Currently involved CPS -->
                                    <div class="form-group radio-group row">
                                        <div class="col-sm-4">
                                            <label class="form-control-label">Are you involved with Child Protective Services?</label>
                                        </div>
                                        <label class="custom-control custom-radio" for="child_protective_yes">
                                            <input  type="radio" id="child_protective_yes" name="child_protective" class="custom-control-input" value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="child_protective_no">
                                            <input type="radio" id="child_protective_no" name="child_protective" class="custom-control-input" value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Currently involved CPS

                                    <!-- Begin Q: Previously involved CPS -->
                                    <div class="form-group radio-group row">
                                        <div class="col-sm-4">
                                            <label class="form-control-label">Have you previously been involved with Child Protective Services?</label>
                                        </div>
                                        <label class="custom-control custom-radio" for="previous_child_protective_yes">
                                            <input  type="radio" id="previous_child_protective_yes" name="previous_child_protective" class="custom-control-input" value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="previous_child_protective_no">
                                            <input type="radio" id="previous_child_protective_no" name="previous_child_protective" class="custom-control-input" value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Previously Involved CPS -->

                                    <!-- Begin Q: Mandated To Take Class -->
                                    <div class="form-group radio-group row">
                                        <div class="col-sm-4">
                                            <label class="form-control-label">Have you been mandated to take this class?</label>
                                        </div>
                                        <label class="custom-control custom-radio" for="mandated_yes">
                                            <input  type="radio" id="mandated_yes" name="mandated" class="custom-control-input" value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="mandated_no">
                                            <input type="radio" id="mandated_no" name="mandated" class="custom-control-input" value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Mandated To Take Class -->

                                    <div  class="form-group hidden-field row mandated_div_yes answer_yes">
                                        <label class="col-form-label col-sm-2" for="mandated_by">Mandated by:</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="mandated_by" name="mandated_by" placeholder="Who mandated you?">
                                        </div>
                                    </div>

                                    <div  class="form-group hidden-field row mandated_div_yes answer_yes">
                                        <label class="col-form-label col-sm-2" for="reason_mandated">Mandate Reason:</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="reason_mandated" name="reason_mandated" placeholder="Reason you were mandated (be specific)">
                                        </div>
                                    </div>

                                    <div class="form-group hidden-field row mandated_div_no answer_no">
                                        <label class="col-form-label col-sm-2" for="reason_for_taking_class">Reason For Taking Class:</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="reason_for_taking_class" name="reason_for_taking_class" placeholder="Please explain...">
                                        </div>
                                    </div>

                                    <!-- Begin Q: Other Parenting Classes -->
                                    <div class="form-group radio-group row">
                                        <div class="col-sm-4">
                                            <label class="form-control-label">Have you attended any other parenting classes?</label>
                                        </div>
                                        <label class="custom-control custom-radio" for="other_classes_yes">
                                            <input  type="radio" id="other_classes_yes" name="other_classes" class="custom-control-input" value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="other_classes_no">
                                            <input type="radio" id="other_classes_no" name="other_classes" class="custom-control-input" value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Other Parenting Classes -->

                                    <div  class="form-group hidden-field row other_classes_div_yes answer_yes">
                                        <label class="col-form-label col-sm-2" for="other_classes_where_when">Please explain:</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="other_classes_where_when" name="other_classes_where_when" placeholder="Where did you take classes and how long ago?">
                                        </div>
                                    </div>

                                    <!-- Begin Q: The Victim Of Abuse Or Neglect -->
                                    <div class="form-group radio-group row">
                                        <div class="col-sm-4">
                                            <label class="form-control-label">Were you the victim of abuse or neglect in your own childhood?</label>
                                        </div>
                                        <label class="custom-control custom-radio" for="victim_of_abuse_yes">
                                            <input  type="radio" id="victim_of_abuse_yes" name="victim_of_abuse" class="custom-control-input" value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="victim_of_abuse_no">
                                            <input type="radio" id="victim_of_abuse_no" name="victim_of_abuse" class="custom-control-input" value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: The Victim Of Abuse Or Neglect -->

                                    <div  class="form-group hidden-field row victim_of_abuse_div_yes answer_yes">
                                        <label class="col-form-label col-sm-2" for="form_of_abuse">Please explain:</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="form_of_abuse" name="form_of_abuse" placeholder="What form of abuse did you take?">
                                        </div>
                                    </div>

                                    <!-- Begin Q: Abuse In Therapy -->
                                    <div class="form-group radio-group row">
                                        <div class="col-sm-4">
                                            <label class="form-control-label">Did you ever deal with your abuse in therapy?</label>
                                        </div>
                                        <label class="custom-control custom-radio" for="abuse_therapy_yes">
                                            <input  type="radio" id="abuse_therapy_yes" name="abuse_therapy" class="custom-control-input" value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="abuse_therapy_no">
                                            <input type="radio" id="abuse_therapy_no" name="abuse_therapy" class="custom-control-input" value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Abuse In Therapy -->

                                    <!-- Begin Q: Issues Relating To Your Childhood Abuse -->
                                    <div class="form-group radio-group row">
                                        <div class="col-sm-4">
                                            <label class="form-control-label">Do you feel you still have some issues relating to childhood abuse?</label>
                                        </div>
                                        <label class="custom-control custom-radio" for="childhood_abuse_relating_yes">
                                            <input  type="radio" id="childhood_abuse_relating_yes" name="childhood_abuse_relating" class="custom-control-input" value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="childhood_abuse_relating_no">
                                            <input type="radio" id="childhood_abuse_relating_no" name="childhood_abuse_relating" class="custom-control-input" value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Issues Relating To Your Childhood Abuse -->

                                    <br>
                                    <h5>Class Questions</h5>
                                    <br>

                                    <!-- Begin Class Participation -->
                                    <div class="row">
                                        <label class="col-form-label col-sm-12 col-12" for="class_participation" style="text-align: left">What do you need from this class to feel safe and fully participate?</label>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-8 col-8">
                                            <input type="text" class="form-control" id="class_participation" name="class_participation" placeholder="Please explain...">
                                        </div>
                                    </div>
                                    <!-- End Class participation -->

                                    <!-- Begin Parenting -->
                                    <div class="row">
                                        <label class="col-form-label col-sm-12 col-12" for="parenting_opinion" style="text-align: left;">What behaviors would keep you from voicing your opinion on your parenting style?</label>
                                    </div>
                                    <div class="form-group row">

                                        <div class="col-sm-8 col-8">
                                            <input type="text" class="form-control" id="parenting_opinion" name="parenting_opinion" placeholder="Please explain...">
                                        </div>
                                    </div>
                                    <!-- End Parenting -->

                                    <!-- Begin Class Takeaway -->
                                    <div class="row">
                                        <label class="col-form-label col-sm-12 col-12" for="class_takeaway" style="text-align: left;">What is the most important thing you would like to learn from this class?</label>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-8 col-8">
                                            <input type="text" class="form-control" id="class_takeaway" name="class_takeaway" placeholder="Please explain...">
                                        </div>
                                    </div>
                                    <!-- End Class Takeaway -->

                                </div>
                            </div>
                        </div>  <!-- 3rd collapsable end -->

                    <br>
                    <!-- 4th collapsable -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title" style="font-weight: normal;">
                                <a data-toggle="collapse" data-parent="#accordion" onfocusin="section4()" class="form-header" href="#collapse4">Participant History Questions</a>
                            </h5>
                        </div>

                        <div id="collapse4" class="collapse">
                            <div class="card-body">
                                <h5>Additional Participant Information</h5>
                                <br>
                                <!-- Begin Q: Domestic Violence -->
                                <div class="form-group radio-group row">
                                    <div class="col-sm-4">
                                        <label class="form-control-label">Have you ever had any involvement with domestic violence?</label>
                                    </div>
                                    <label class="custom-control custom-radio" for="domestic_violence_yes">
                                        <input  type="radio" id="domestic_violence_yes" name="domestic_violence" class="custom-control-input" value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="domestic_violence_no">
                                        <input type="radio" id="domestic_violence_no" name="domestic_violence" class="custom-control-input" value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Domestic Violence -->

                                <div  style="margin-right: 20%" class="form-group hidden-field radio-group row domestic_violence_div_yes answer_yes">
                                    <div class="col-sm-4">
                                        <label class="form-control-label">Have you discussed it with someone?</label>
                                    </div>
                                    <label class="custom-control custom-radio" for="domestic_violence_discussed_yes">
                                        <input  type="radio" id="domestic_violence_discussed_yes" name="domestic_violence_discussed" class="custom-control-input" value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="domestic_violence_discussed_no">
                                        <input type="radio" id="domestic_violence_discussed_no" name="domestic_violence_discussed" class="custom-control-input" value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>

                                <!-- Begin Q: History Of Family Violence -->
                                <div class="form-group radio-group row">
                                    <div class="col-sm-4">
                                        <label class="form-control-label">Is there any history of violence in your family of origin?</label>
                                    </div>
                                    <label class="custom-control custom-radio" for="history_violence_family_yes">
                                        <input  type="radio" id="history_violence_family_yes" name="history_violence_family" class="custom-control-input" value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="history_violence_family_no">
                                        <input type="radio" id="history_violence_family_no" name="history_violence_family" class="custom-control-input" value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: History Of Family Violence -->

                                <!-- Begin Q: Nuclear Family Violence -->
                                <div class="form-group radio-group row">
                                    <div class="col-sm-4">
                                        <label class="form-control-label">Is there any history of violence in your nuclear family?</label>
                                    </div>
                                    <label class="custom-control custom-radio" for="history_violence_nuclear_yes">
                                        <input  type="radio" id="history_violence_nuclear_yes" name="history_violence_nuclear" class="custom-control-input" value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="history_violence_nuclear_no">
                                        <input type="radio" id="history_violence_nuclear_no" name="history_violence_nuclear" class="custom-control-input" value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Nuclear Family Violence -->

                                <!-- Begin Q: Protection Orders -->
                                <div class="form-group radio-group row">
                                    <div class="col-sm-4">
                                        <label class="form-control-label">Are there any orders of protection involved?</label>
                                    </div>
                                    <label class="custom-control custom-radio" for="protection_order_yes">
                                        <input  type="radio" id="protection_order_yes" name="protection_order" class="custom-control-input" value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="protection_order_no">
                                        <input type="radio" id="protection_order_no" name="protection_order" class="custom-control-input" value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Protection Orders-->

                                <div  class="form-group hidden-field row protection_order_div_yes answer_yes">
                                    <label class="col-form-label col-sm-2" for="protection_order_explain">Please explain:</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" id="protection_order_explain" name="protection_order_explain" placeholder="Why and who are they against?">
                                    </div>
                                </div>

                                <!-- Begin Q: Arrested For A Crime -->
                                <div class="form-group radio-group row">
                                    <div class="col-sm-4">
                                        <label class="form-control-label">Have you ever been arrested for a crime?</label>
                                    </div>
                                    <label class="custom-control custom-radio" for="crime_arrested_yes">
                                        <input  type="radio" id="crime_arrested_yes" name="crime_arrested" class="custom-control-input" value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="crime_arrested_no">
                                        <input type="radio" id="crime_arrested_no" name="crime_arrested" class="custom-control-input" value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Arrested For A Crime -->

                                <!-- Begin Q: Convicted For A Crime -->
                                <div class="form-group radio-group row">
                                    <div class="col-sm-4">
                                        <label class="form-control-label">Have you ever been convicted for a crime?</label>
                                    </div>
                                    <label class="custom-control custom-radio" for="crime_convicted_yes">
                                        <input  type="radio" id="crime_convicted_yes" name="crime_convicted" class="custom-control-input" value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="crime_convicted_no">
                                        <input type="radio" id="crime_convicted_no" name="crime_convicted" class="custom-control-input" value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Convicted For A Crime -->

                                <div  class="form-group hidden-field row crime_convicted_div_yes answer_yes">
                                    <label class="col-form-label col-sm-2" for="crime_explain">Please explain:</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" id="crime_explain" name="crime_explain" placeholder="Please provide an explanation">
                                    </div>
                                </div>

                                <!-- Begin Q: Jail Or Prison Record -->
                                <div class="form-group radio-group row">
                                    <div class="col-sm-4">
                                        <label class="form-control-label">Do you have a jail and/or prison record?</label>
                                    </div>
                                    <label class="custom-control custom-radio" for="jail_prison_record_yes">
                                        <input  type="radio" id="jail_prison_record_yes" name="jail_prison_record" class="custom-control-input" value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="jail_prison_record_no">
                                        <input type="radio" id="jail_prison_record_no" name="jail_prison_record" class="custom-control-input" value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Jail Or Prison Record -->

                                <div  class="form-group hidden-field row jail_prison_record_div_yes answer_yes">
                                    <label class="col-form-label col-sm-2" for="jail_prison_explain">Please explain:</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" id="jail_prison_explain" name="jail_prison_explain" placeholder="When were you in jail/prison and for what offense?">
                                    </div>
                                </div>

                                <!-- Begin Q: Parole Or Probation -->
                                <div class="form-group radio-group row">
                                    <div class="col-sm-4">
                                        <label class="form-control-label">Are you currently on parole or probation?</label>
                                    </div>
                                    <label class="custom-control custom-radio" for="parole_probation_yes">
                                        <input  type="radio" id="parole_probation_yes" name="parole_probation" class="custom-control-input" value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="parole_probation_no">
                                        <input type="radio" id="parole_probation_no" name="parole_probation" class="custom-control-input" value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Parole Or Probation -->

                                <div  class="form-group hidden-field row parole_probation_div_yes answer_yes">
                                    <label class="col-form-label col-sm-2" for="parole_probation_explain">Please explain:</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" id="parole_probation_explain" name="parole_probation_explain" placeholder="For what offense?">
                                    </div>
                                </div>

                                <!-- Begin Q: Other Family Members Attending Class-->
                                <div class="form-group radio-group row">
                                    <div class="col-sm-4">
                                        <label class="form-control-label">Are there any other members of your family taking a parenting class with this agency?</label>
                                    </div>
                                    <label class="custom-control custom-radio" for="family_members_taking_class_yes">
                                        <input  type="radio" id="family_members_taking_class_yes" name="family_members_taking_class" class="custom-control-input" value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="family_members_taking_class_no">
                                        <input type="radio" id="family_members_taking_class_no" name="family_members_taking_class" class="custom-control-input" value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Other Family Members Attending Class -->

                                <div  class="form-group hidden-field row family_members_taking_class_div_yes answer_yes">
                                    <label class="col-form-label col-sm-2" for="family_members">Family Members:</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" id="family_members" name="family_members" placeholder="Please list their name(s)">
                                    </div>
                                </div>
                            </div>
                        </div>   <!-- 4th collapsible end -->
                    </div>
                    </div>
                </form>
            </div>  <!-- panel group end -->
            <br>
            <?php include('form_duplicate_check.php')?>

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
<?php include('footer.php'); ?>