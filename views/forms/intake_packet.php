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
 * @version 1.2.2
 * @since 0.3.2
 */

authorizedPage();
global $db, $params, $route, $view;

// Checks if the page has a people ID associated for updating/editing forms.
if (isset($params[0]) && isset($params[1]) && isset($params[2])) {
    $peopleid = $params[1];
    $intake_formID = $params[2];

    // SELECT FROM VIEWS TO POPULATE FIELDS
    // First Card (Participant Information)
    $intake_packet_edit = $db->query("SELECT * FROM intakepacketinfo WHERE peopleid = $1", [$peopleid]);
    if (pg_num_rows($intake_packet_edit) > 0) {
        $intake_packet_info = pg_fetch_assoc($intake_packet_edit);

        # personal information
        $intake_firstname_result            = $intake_packet_info['firstname'];
        $intake_lastname_result             = $intake_packet_info['lastname'];
        $intake_middlein_result             = $intake_packet_info['middleinit'];
        $intake_dob_result                  = $intake_packet_info['pdob'];
        $intake_religion_result             = $intake_packet_info['religion'];
        $intake_ethnicity_result            = $intake_packet_info['prace'];
        $intake_sex_result                  = $intake_packet_info['psex'];
        $intake_occupation_result           = $intake_packet_info['occupation'];
        $intake_last_year_school_result     = $intake_packet_info['lastyearofschoolcompleted'];
        $intake_languages_spoken_result     = $intake_packet_info['language'];
        $intake_handicap_medication_result  = $intake_packet_info['handicapsormedication'];

        # contact information
        $intake_address_id = $intake_packet_info['addressid'];
        $intake_address_result = $db->query("SELECT * FROM addresses WHERE addressid = $1", [$intake_address_id]);
        if (pg_num_rows($intake_address_result) > 0) {
            $intake_addr_info = pg_fetch_assoc($intake_address_result);
            $intake_street_num_result = $intake_addr_info['addressnumber'];
            $intake_street_name_result = $intake_addr_info['street'];
            $intake_street = (empty($intake_street_num_result) ? '' : $intake_street_num_result . ' ') .
                (empty($intake_street_name_result) ? '' : $intake_street_name_result);
            $intake_zip_result = $intake_addr_info['zipcode'];
            $intake_intake_apt_info_result = $intake_addr_info['aptinfo'];

            # zip
            $intake_zip_res = $db->query("SELECT * FROM zipcodes WHERE zipcode = $1", [$intake_zip_result]);
            $intake_zip_info = pg_fetch_assoc($intake_zip_res);
            $intake_state_result = $intake_zip_info['state'];
            $intake_city_result = $intake_zip_info['city'];
        }

        # additional participant information
        $drug_alcohol_abuse_result              = $intake_packet_info['hassubstanceabusehistory'];
        $drug_alcohol_abuse_explain_result      = $intake_packet_info['substanceabusedescription'];
        $live_with_children_separated_result    = $intake_packet_info['timeseparatedfromchildren'];
        $separated_length_result                = $intake_packet_info['timeseparatedfrompartner'];
        $relationship_result                    = $intake_packet_info['relationshiptootherparent'];
        $parenting_result                       = $intake_packet_info['hasparentingpartnershiphistory'];
        $child_protective_result                = $intake_packet_info['hasinvolvementcps'];
        $previous_child_protective_result       = $intake_packet_info['previouslyinvolvedwithcps'];
        $mandated_result                        = $intake_packet_info['ismandatedtotakeclass'];
        $mandated_by_result                     = $intake_packet_info['mandatedbywhom'];
        $reason_for_taking_class_result         = $intake_packet_info['reasonforattendence'];
        $other_classes_result                   = $intake_packet_info['attendedotherparentingclasses'];
        $other_classes_where_when_result        = $intake_packet_info['previousclassinfo'];
        $victim_of_abuse_result                 = $intake_packet_info['wasvictim'];
        $form_of_abuse_result                   = $intake_packet_info['formofchildhoodabuse'];
        $abuse_therapy_result                   = $intake_packet_info['hashadtherapy'];
        $childhood_abuse_relating_result        = $intake_packet_info['feelstillhasissuesfromchildabuse'];
        $class_participation_result             = $intake_packet_info['safeparticipate'];
        $parenting_opinion_result               = $intake_packet_info['preventativebehaviors'];
        $class_takeaway_result                  = $intake_packet_info['mostimportantliketolearn'];

        # participant history questions
        $domestic_violence_result               = $intake_packet_info['hasdomesticviolencehistory'];
        $domestic_violence_discussed_result     = $intake_packet_info['hasdiscusseddomesticviolence'];
        $history_violence_family_result         = $intake_packet_info['hashistoryofviolenceinoriginfamily'];
        $history_violence_nuclear_result        = $intake_packet_info['hashistoryofviolenceinnuclearfamily'];
        $protection_order_result                = $intake_packet_info['ordersofprotectioninvolved'];
        $protection_order_explain_result        = $intake_packet_info['reasonforordersofprotection'];
        $crime_arrested_result                  = $intake_packet_info['hasbeenarrested'];
        $crime_convicted_result                 = $intake_packet_info['hasbeenconvicted'];
        $crime_explain_result                   = $intake_packet_info['reasonforarrestorconviction'];
        $jail_prison_record_result              = $intake_packet_info['hasjailorprisonrecord'];
        $jail_prison_explain_result             = $intake_packet_info['offenseforjailorprison'];
        $parole_probation_result                = $intake_packet_info['currentlyonparole'];
        $parole_probation_explain_result        = $intake_packet_info['onparoleforwhatoffense'];
        $family_members_taking_class_result     = $intake_packet_info['otherfamilytakingclass'];
        $family_members_result                  = $intake_packet_info['familymemberstakingclass'];
    }

    $intake_phone_day_edit = $db->query("SELECT FormPhoneNumbers.phoneNumber FROM FormPhoneNumbers WHERE formID = $1 AND phoneType = 'Day';", [$intake_formID]);
    $intake_phone_day_result = pg_fetch_result($intake_phone_day_edit, 0);

    $intake_phone_night_edit = $db->query("SELECT FormPhoneNumbers.phoneNumber FROM FormPhoneNumbers WHERE formID = $1 AND phoneType = 'Evening';", [$intake_formID]);
    $intake_phone_night_result = pg_fetch_result($intake_phone_night_edit, 0);

    $contact_edit = $db->query("SELECT * FROM emergencycontactdetail
                                        INNER JOIN emergencycontacts ON emergencycontacts.emergencycontactid = emergencycontactdetail.emergencycontactid
                                        INNER JOIN people ON people.peopleid = emergencycontactdetail.emergencycontactid
                                        WHERE intakeinformationid = $1;", [$intake_formID]);
    $contact_result = pg_fetch_assoc($contact_edit);
    $contact_firstname_result = $contact_result['firstname'];
    $contact_lastname_result = $contact_result['lastname'];
    $contact_relationship_result = $contact_result['relationship'];
    $contact_phone_result = $contact_result['primaryphone'];

    // END OF VIEW QUERIES

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /*************
     * VARIABLES *
     *************/

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
    $contact_firstname = !empty($_POST['contact_firstname']) ? trim($_POST['contact_firstname']) : NULL;
    $contact_lastname = !empty($_POST['contact_lastname']) ? trim($_POST['contact_lastname']) : NULL;
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
    //6th
    $child_first_name_6 = !empty($_POST['child_first_name_6']) ? trim($_POST['child_first_name_6']) : NULL;
    $child_last_name_6 = !empty($_POST['child_last_name_6']) ? trim($_POST['child_last_name_6']) : NULL;
    $child_mi_6 = !empty($_POST['child_mi_6']) ? trim($_POST['child_mi_6']) : NULL;
    $child_dob_6 = !empty($_POST['child_dob_6']) ? $_POST['child_dob_6'] : NULL;
    $child_sex_6 = !empty($_POST['child_sex_6']) ? $_POST['child_sex_6'] : NULL;
    $child_race_6 = !empty($_POST['child_race_6']) ? $_POST['child_race_6'] : NULL;
    $child_live_6 = !empty($_POST['child_live_6']) ? trim($_POST['child_live_6']) : NULL;
    $child_custody_6 = !empty($_POST['child_custody_6']) ? trim($_POST['child_custody_6']) : NULL;
    // 7th Child Clone
    $child_first_name_7 = !empty($_POST['child_first_name_7']) ? trim($_POST['child_first_name_7']) : NULL;
    $child_last_name_7 = !empty($_POST['child_last_name_7']) ? trim($_POST['child_last_name_7']) : NULL;
    $child_mi_7 = !empty($_POST['child_mi_7']) ? trim($_POST['child_mi_7']) : NULL;
    $child_dob_7 = !empty($_POST['child_dob_7']) ? $_POST['child_dob_7'] : NULL;
    $child_sex_7 = !empty($_POST['child_sex_7']) ? $_POST['child_sex_7'] : NULL;
    $child_race_7 = !empty($_POST['child_race_7']) ? $_POST['child_race_7'] : NULL;
    $child_live_7 = !empty($_POST['child_live_7']) ? trim($_POST['child_live_7']) : NULL;
    $child_custody_7 = !empty($_POST['child_custody_7']) ? trim($_POST['child_custody_7']) : NULL;
    // 8th Child Clone
    $child_first_name_8 = !empty($_POST['child_first_name_8']) ? trim($_POST['child_first_name_8']) : NULL;
    $child_last_name_8 = !empty($_POST['child_last_name_8']) ? trim($_POST['child_last_name_8']) : NULL;
    $child_mi_8 = !empty($_POST['child_mi_8']) ? trim($_POST['child_mi_8']) : NULL;
    $child_dob_8 = !empty($_POST['child_dob_8']) ? $_POST['child_dob_8'] : NULL;
    $child_sex_8 = !empty($_POST['child_sex_8']) ? $_POST['child_sex_8'] : NULL;
    $child_race_8 = !empty($_POST['child_race_8']) ? $_POST['child_race_8'] : NULL;
    $child_live_8 = !empty($_POST['child_live_8']) ? trim($_POST['child_live_8']) : NULL;
    $child_custody_8 = !empty($_POST['child_custody_8']) ? trim($_POST['child_custody_8']) : NULL;
    // 9th Child Clone
    $child_first_name_9 = !empty($_POST['child_first_name_9']) ? trim($_POST['child_first_name_9']) : NULL;
    $child_last_name_9 = !empty($_POST['child_last_name_9']) ? trim($_POST['child_last_name_9']) : NULL;
    $child_mi_9 = !empty($_POST['child_mi_9']) ? trim($_POST['child_mi_9']) : NULL;
    $child_dob_9 = !empty($_POST['child_dob_9']) ? $_POST['child_dob_9'] : NULL;
    $child_sex_9 = !empty($_POST['child_sex_9']) ? $_POST['child_sex_9'] : NULL;
    $child_race_9 = !empty($_POST['child_race_9']) ? $_POST['child_race_9'] : NULL;
    $child_live_9 = !empty($_POST['child_live_9']) ? trim($_POST['child_live_9']) : NULL;
    $child_custody_9 = !empty($_POST['child_custody_9']) ? trim($_POST['child_custody_9']) : NULL;
    // 10th Child Clone
    $child_first_name_10 = !empty($_POST['child_first_name_10']) ? trim($_POST['child_first_name_10']) : NULL;
    $child_last_name_10 = !empty($_POST['child_last_name_10']) ? trim($_POST['child_last_name_10']) : NULL;
    $child_mi_10 = !empty($_POST['child_mi_10']) ? trim($_POST['child_mi_10']) : NULL;
    $child_dob_10 = !empty($_POST['child_dob_10']) ? $_POST['child_dob_10'] : NULL;
    $child_sex_10 = !empty($_POST['child_sex_10']) ? $_POST['child_sex_10'] : NULL;
    $child_race_10 = !empty($_POST['child_race_10']) ? $_POST['child_race_10'] : NULL;
    $child_live_10 = !empty($_POST['child_live_10']) ? trim($_POST['child_live_10']) : NULL;
    $child_custody_10 = !empty($_POST['child_custody_10']) ? trim($_POST['child_custody_10']) : NULL;

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
    $live_with_children_separated = !empty($_POST['live_with_children_separated']) && !$live_with_children ? trim($_POST['live_with_children_separated']) : '';
    if (!empty($_POST['parent_separated'])) {
        $parent_separated = $_POST['parent_separated'] === "Yes" ? 1 : 0;
    } else {
        $parent_separated = NULL;
    }
    $separated_length = !empty($_POST['separated_length']) && $parent_separated ? trim($_POST['separated_length']) : (!$parent_separated ? '' : NULL);
    $relationship = !empty($_POST['relationship']) && $parent_separated ? trim($_POST['relationship']) : (!$parent_separated ? '' : NULL);
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

    if (isset($params[0]) && isset($params[1]) && isset($params[2])) {

        /*************
         *  UPDATES  *
         *************/

        $updatePeopleResult = $db->query("UPDATE
                                    People
                                    SET
                                    firstName = $1,
                                    lastName = $2,
                                    middleInit = $3
                                    WHERE 
                                    peopleID = $4;", [$intake_firstname, $intake_lastname, $intake_middlein, $params[1]]);

        $updateParticipantResult = $db->query("UPDATE 
                                    Participants
                                    SET 
                                    dateOfBirth = $1,
                                    race = $2,
                                    sex = $3
                                    WHERE 
                                    participantID = $4;", [$intake_dob, $intake_ethnicity, $intake_sex, $params[1]]);

        $newZip = $db->query("INSERT INTO ZipCodes VALUES($1, $2, $3);", [$intake_zip, $intake_city, $intake_state]);

        $updateAddressResult = $db->query("UPDATE
                                    Addresses
                                    SET
                                    addressNumber = $1,
                                    aptInfo = $2,
                                    street = $3,
                                    zipCode = $4
                                    WHERE
                                    addressID = $5;", [$intake_street_num, $intake_intake_apt_info, $intake_street_name, $intake_zip, $intake_address_id]);


        $updateDayPhoneResult = $db->query("UPDATE 
                                    FormPhoneNumbers
                                    SET 
                                    phoneNumber = $1
                                    WHERE
                                    formID = $2 AND
                                    phoneType = $3;", [$intake_phone_day, $params[2], 'Day']);

        $updateDayPhoneInsert = $db->query("INSERT INTO 
                                                  FormPhoneNumbers (formID, phoneNumber, phoneType)
                                                  SELECT $1, $2, $3
                                                  WHERE NOT EXISTS (SELECT 1 FROM FormPhoneNumbers WHERE formID = $1 AND phoneType = $3);", [$params[2], $intake_phone_day, 'Day']);

        $updateEveningPhoneResult = $db->query("UPDATE 
                                    FormPhoneNumbers
                                    SET 
                                    phoneNumber = $1
                                    WHERE
                                    formID = $2 AND
                                    phoneType = $3;", [$intake_phone_night, $params[2], 'Evening']);

        $updateEveningPhoneInsert = $db->query("INSERT INTO 
                                                  FormPhoneNumbers (formID, phoneNumber, phoneType)
                                                  SELECT $1, $2, $3
                                                  WHERE NOT EXISTS (SELECT 1 FROM FormPhoneNumbers WHERE formID = $1 AND phoneType = $3);", [$params[2], $intake_phone_night, 'Evening']);

        $updateEmergencyPhoneResult = $db->query("UPDATE 
                                    FormPhoneNumbers
                                    SET 
                                    phoneNumber = $1
                                    WHERE
                                    formID = $2 AND
                                    phoneType = $3;", [$contact_phone, $params[2], 'Primary']);

        $emergencyContactInfo = $db->query("SELECT * FROM emergencycontactdetail
                                        INNER JOIN emergencycontacts ON emergencycontacts.emergencycontactid = emergencycontactdetail.emergencycontactid
                                        INNER JOIN people ON people.peopleid = emergencycontactdetail.emergencycontactid
                                        WHERE intakeinformationid = $1;", [$params[2]]);

        $emergencyResults = pg_fetch_assoc($emergencyContactInfo);
        $emergencyContactPid = $emergencyResults['emergencycontactid'];
        $contact_firstname_result = $emergencyResults['firstname'];
        $contact_lastname_result = $emergencyResults['lastname'];

        if ($contact_firstname !== NULL && $contact_lastname !== NULL && $contact_firstname_result == NULL && $contact_lastname_result == NULL) {
            $newContact = $db->query("SELECT createEmergencyContact(
                                            emerContactFName := $1::TEXT,
                                            emerContactLName := $2::TEXT,
                                            emerContactMiddleInit := $3::VARCHAR(1),
                                            intInfoID := $4::INT,
                                            rel := $5::relationship,
                                            phon := $6::text
                                            );", [$contact_firstname, $contact_lastname, NULL, $params[2], $contact_relationship, $contact_phone]);
        } else {
            $updateEmergencyContactName = $db->query("UPDATE
                                    People
                                    SET
                                    firstName = $1,
                                    lastName = $2,
                                    middleInit = $3
                                    WHERE 
                                    peopleID = $4;", [$contact_firstname, $contact_lastname, NULL, $emergencyContactPid]);

            $updateEmergencyContactInfo = $db->query("UPDATE 
                                    EmergencyContacts
                                    SET 
                                    relationship = $1,
                                    primaryPhone = $2
                                    WHERE
                                    emergencyContactID = $3;", [$contact_relationship, $contact_phone, $emergencyContactPid]);
        }

        $updatePrimaryPhoneInsert = $db->query("INSERT INTO 
                                                  FormPhoneNumbers (formID, phoneNumber, phoneType)
                                                  SELECT $1, $2, $3
                                                  WHERE NOT EXISTS (SELECT 1 FROM FormPhoneNumbers WHERE formID = $1 AND phoneType = $3);", [$params[2], $contact_phone, 'Primary']);

        $updateIntakeResult = $db->query("UPDATE 
                                    IntakeInformation
                                    SET
                                    occupation = $1,
                                    religion = $2,
                                    handicapsOrMedication = $3,
                                    lastYearOfSchoolCompleted = $4,
                                    hasSubstanceAbuseHistory = $5,
                                    substanceAbuseDescription = $6,
                                    timeSeparatedFromChildren = $7,
                                    timeSeparatedFromPartner = $8,
                                    relationshipToOtherParent = $9,
                                    hasParentingPartnershipHistory = $10,
                                    hasInvolvementCPS = $11,
                                    previouslyInvolvedWithCPS = $12,
                                    isMandatedToTakeClass = $13,
                                    mandatedByWhom = $14,
                                    reasonForAttendence = $15,
                                    safeParticipate = $16,
                                    preventativeBehaviors = $17,
                                    attendedOtherParentingClasses = $18,
                                    previousClassInfo = $19,
                                    wasVictim = $20,
                                    formOfChildhoodAbuse = $21,
                                    hasHadTherapy = $22,
                                    feelStillHasIssuesFromChildAbuse = $23,
                                    mostImportantLikeToLearn = $24,
                                    hasDomesticViolenceHistory = $25,
                                    hasDiscussedDomesticViolence = $26,
                                    hasHistoryOfViolenceInOriginFamily = $27,
                                    hasHistoryOfViolenceInNuclearFamily = $28,
                                    ordersOfProtectionInvolved = $29,
                                    reasonForOrdersOfProtection = $30,
                                    hasBeenArrested = $31,
                                    hasBeenConvicted = $32,
                                    reasonForArrestOrConviction = $33,
                                    hasJailOrPrisonRecord = $34,
                                    offenseForJailOrPrison = $35,
                                    currentlyOnParole = $36,
                                    onParoleForWhatOffense = $37,
                                    language = $38,
                                    otherFamilyTakingClass = $39,
                                    familyMembersTakingClass = $40,
                                    ptpFormSignedDate = $41,
                                    ptpEnrollmentSignedDate = $42,
                                    ptpConstentReleaseFormSignedDate = $43
                                    WHERE
                                    intakeInformationID = $44;", [$intake_occupation, $intake_religion, $intake_handicap_medication, $intake_last_year_school, $drug_alcohol_abuse, $drug_alcohol_abuse_explain, $live_with_children_separated,
            $separated_length, $relationship, $parenting, $child_protective, $previous_child_protective, $mandated, $mandated_by, $reason_for_attendance, $class_participation, $parenting_opinion,
            $other_classes, $other_classes_where_when, $victim_of_abuse, $form_of_abuse, $abuse_therapy, $childhood_abuse_relating, $class_takeaway, $domestic_violence, $domestic_violence_discussed,
            $history_violence_family, $history_violence_nuclear, $protection_order, $protection_order_explain, $crime_arrested, $crime_convicted, $crime_explain,
            $jail_prison_record, $jail_prison_explain, $parole_probation, $parole_probation_explain, $intake_languages_spoken, $family_members_taking_class, $family_members, $datestamp, $datestamp, $datestamp, $params[2]]);



        $formID = $params[2];

        // Counts how many children are associated with a particular form.
        $children_edit = $db->query("SELECT COUNT(familyMemberID)
                                                        FROM FamilyInfo, Children
                                                        WHERE FamilyInfo.formID = $1 
                                                        AND Children.childrenID = FamilyInfo.FamilyMemberID;", [$formID]);
        $children__count = pg_fetch_result($children_edit, 0);

        $children_all_edit = $db->query("SELECT * FROM Children 
                                                            INNER JOIN People ON people.peopleid = children.childrenID 
                                                            INNER JOIN familymembers ON familymembers.familymemberid = children.childrenid 
                                                            INNER JOIN family ON family.familymembersid = children.childrenid
                                                            WHERE family.formID = $1;", [$formID]);

        // For loop based on how many children were inputted into a particular form.
        for ($i = 1; $i <= 10; $i++) {

            $chd_first_name = "child_first_name_" . $i;
            $chd_last_name = "child_last_name_" . $i;
            $chd_mi = "child_mi_" . $i;
            $chd_dob = "child_dob_" . $i;
            $chd_race = "child_race_" . $i;
            $chd_sex = "child_sex_" . $i;
            $chd_live = "child_live_" . $i;
            $chd_custody = "child_custody_" . $i;

            if($i <= $children__count) {
                $row = pg_fetch_assoc($children_all_edit, $i-1);
                $chd_id = $row['childrenid'];
                $updateChildNameResult = $db->query("UPDATE
                                    People
                                    SET
                                    firstName = $1,
                                    lastName = $2,
                                    middleInit = $3
                                    WHERE 
                                    peopleID = $4;", [$$chd_first_name, $$chd_last_name, $$chd_mi, $chd_id]);

                $updateFamilyResult = $db->query("UPDATE
                                                FamilyMembers
                                                SET
                                                dateOfBirth = $1,
                                                race = $2,
                                                sex = $3
                                                WHERE
                                                familyMemberID = $4;", [$$chd_dob, $$chd_race, $$chd_sex, $chd_id]);

                $updateChildrenResult = $db->query("UPDATE
                                                Children
                                                SET
                                                custody = $1,
                                                location = $2
                                                WHERE
                                                childrenID = $3;", [$$chd_custody, $$chd_live, $chd_id]);

            } else {
                if ($$chd_first_name !== NULL && $$chd_last_name !== NULL) {

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
        header('Location: /ps-view-participant/'.$params[1]);
        die();

    } else {

        /*********************
         * STORED PROCEDURES *
         *********************/

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

        if ($contact_firstname !== NULL && $contact_lastname !== NULL) {
            $contact = $db->query("SELECT createEmergencyContact(
                                            emerContactFName := $1::TEXT,
                                            emerContactLName := $2::TEXT,
                                            emerContactMiddleInit := $3::VARCHAR(1),
                                            intInfoID := $4::INT,
                                            rel := $5::relationship,
                                            phon := $6::text
                                            );", [$contact_firstname, $contact_lastname, NULL, $formID, $contact_relationship, $contact_phone]);
        }

        // Child stored procedures (handles entering multiple children for an intake packet).
        for ($i = 1; $i <= 10; $i++) {
            // Create variable names
            $chd_first_name = "child_first_name_" . $i;
            $chd_last_name = "child_last_name_" . $i;
            $chd_mi = "child_mi_" . $i;
            $chd_dob = "child_dob_" . $i;
            $chd_race = "child_race_" . $i;
            $chd_sex = "child_sex_" . $i;
            $chd_live = "child_live_" . $i;
            $chd_custody = "child_custody_" . $i;

            // Run InsertPeople for current child
            if ($$chd_first_name !== NULL && $$chd_last_name !== NULL) {

                if ($$chd_first_name !== NULL && $$chd_last_name !== NULL) {
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
                if (isset($params[0]) && isset($params[1]) && isset($params[2]))
                    echo '<form id="intake_packet" action="/intake-packet/'.$params[0].'/'.$params[1].'/'.$params[2].'" method="post" novalidate>';
                else
                    echo '<form id="intake_packet" action="/intake-packet" method="post" novalidate>';
                ?>

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
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="intake_firstname">Participant Name:</label>

                                        <div class="col-md-5 col-xl-2 col">
                                            <input type="text" class="form-control" id="intake_firstname" name="intake_firstname"
                                                   value="<?= (isset($intake_firstname_result)) ? $intake_firstname_result : ""?>" placeholder="First name" required>
                                            <div class="invalid-feedback">Enter first name</div>
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="intake_lastname">Last Name:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <input type="text" class="form-control" id="intake_lastname" name="intake_lastname"
                                                   value="<?= (isset($intake_lastname_result)) ? $intake_lastname_result : ""?>" placeholder="Last name" required>
                                            <div class="invalid-feedback">Enter last name</div>
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="intake_middlein">MInitial:</label>
                                        <div class="col-md-2 col-xl-1 col">
                                            <input type="text" class="form-control" id="intake_middlein" name="intake_middlein"
                                                   value="<?= (isset($intake_middlein_result)) ? $intake_middlein_result : "" ?>" placeholder="Initial" maxlength="1">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="intake_dob">Date of Birth:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <input type="date" class="form-control" id="intake_dob" name="intake_dob"
                                                   value="<?= (isset($intake_dob_result)) ? $intake_dob_result : "" ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="intake_religion">Religion:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <input type="text" class="form-control" id="intake_religion" name="intake_religion"
                                                   value="<?= (isset($intake_religion_result)) ? $intake_religion_result : "" ?>" placeholder="Religion">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="intake_ethnicity">Race:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <select class="form-control select_sex" name="intake_ethnicity" id="intake_ethnicity">
                                                <option value="" selected="selected">Choose one</option>
                                                <?php
                                                $res = $db->query("SELECT unnest(enum_range(NULL::race)) AS type", []);
                                                while ($enumtype = pg_fetch_assoc($res)) {
                                                    $t = $enumtype ['type'];
                                                    ?>
                                                    <option value="<?= $t ?>" <?= (isset($intake_ethnicity_result) && $intake_ethnicity_result == $t) ? "selected" : "" ?>><?= $t ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="intake_sex">Sex:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <select class="form-control select_sex" name="intake_sex" id="intake_sex">
                                                <option value="" selected="selected">Choose one</option>
                                                <?php
                                                $res = $db->query("SELECT unnest(enum_range(NULL::sex)) AS type", []);
                                                while ($enumtype = pg_fetch_assoc($res)) {
                                                    $t = $enumtype ['type'];
                                                    ?>
                                                    <option value="<?= $t ?>" <?= (isset($intake_sex_result) && $intake_sex_result == $t) ? "selected" : "" ?>><?= $t ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="intake_occupation">Occupation:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <input type="text" class="form-control" id="intake_occupation" name="intake_occupation"
                                                   value="<?= (isset($intake_occupation_result)) ? $intake_occupation_result : "" ?>" placeholder="Enter an occupation">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="intake_last_year_school">Last Year of School:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <input type="text" class="form-control" id="intake_last_year_school" name="intake_last_year_school"
                                                   value="<?= (isset($intake_last_year_school_result)) ? $intake_last_year_school_result : "" ?>" placeholder="example: 1988">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="intake_languages_spoken">Languages Spoken:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <input type="text" class="form-control" id="intake_languages_spoken" name="intake_languages_spoken"
                                                   value="<?= (isset($intake_languages_spoken_result)) ? $intake_languages_spoken_result : "" ?>" placeholder="English, Spanish, etc...">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="handicap_medication">Handicap/Medication:</label>
                                        <div class="col-md-12 col-xl-4  col">
                                            <textarea style="resize: none;" class="form-control" rows=4 id="handicap_medication" name="handicap_medication" placeholder="Any handicapping conditions or medications"><?= (isset($intake_handicap_medication_result)) ? $intake_handicap_medication_result : "" ?></textarea>
                                        </div>
                                    </div>

                                    <h5>Contact Information</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="intake_address">Street Address:</label>
                                        <div class="col-md-5 col-xl-3 col">
                                            <input type="text" class="form-control" id="intake_address" name="intake_address"
                                                   value="<?= (isset($intake_street)) ? $intake_street : ""?>" placeholder="Street address">
                                        </div>
                                        <label class="col-form-label col-md-12 col-xl-1 d-md-block" for="intake_apt_info">Apartment:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <input type="text" class="form-control" id="intake_apt_info" name="intake_apt_info"
                                                   value="<?= (isset($intake_intake_apt_info_result)) ? $intake_intake_apt_info_result : ""?>" placeholder="Apartment Information">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="intake_state">State:</label>
                                        <div class="col-md-5 col-xl-3 col">
                                            <select class="form-control" id="intake_state" name="intake_state" >
                                                <option value="" selected="selected">Choose a state</option>
                                                <?php
                                                $res = $db->query("SELECT unnest(enum_range(NULL::states)) AS type", []);
                                                while ($enumtype = pg_fetch_assoc($res)) {
                                                    $t = $enumtype ['type'];
                                                    ?>
                                                    <option value="<?= $t ?>" <?= (isset($intake_state_result) && $intake_state_result == $t) ? "selected" : "" ?>><?= $t ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <label class="col-form-label col-md-12 col-xl-1 col-2 d-md-block" for="intake_city">City:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <input type="text" class="form-control" id="intake_city" name="intake_city"
                                                   value="<?= (isset($intake_city_result)) ? $intake_city_result : "" ?>" placeholder="City" data-error="Enter city.">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="intake_zip">ZIP:</label>
                                        <div class="col-md-5 col-xl-1 col">
                                            <input type="text" class="form-control mask-zip" id="intake_zip" name="intake_zip"
                                                   value="<?= (isset($intake_zip_result)) ? $intake_zip_result : "" ?>" placeholder="Zip">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="intake_phone_day">Daytime Phone:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <input type="tel" class="form-control mask-phone feedback-icon" id="intake_phone_day" name="intake_phone_day"
                                                   value="<?= (isset($intake_phone_day_result)) ? $intake_phone_day_result : "" ?>" placeholder="(999) 999-9999">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="intake_phone_night">Evening Phone:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <input type="tel" class="form-control mask-phone feedback-icon" id="intake_phone_night" name="intake_phone_night"
                                                   value="<?= (isset($intake_phone_night_result)) ? $intake_phone_night_result : "" ?>" placeholder="(999) 999-9999">
                                        </div>
                                    </div>
                                    <br>

                                    <h5>Emergency Contact</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="contact_firstname">Contact Name:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <input type="text" class="form-control" id="contact_firstname" name="contact_firstname"
                                                   value="<?= (isset($contact_firstname_result)) ? $contact_firstname_result : ""?>" placeholder="First name">
                                            <div class="invalid-feedback">Enter first name</div>
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="contact_lastname">Last Name:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <input type="text" class="form-control" id="contact_lastname" name="contact_lastname"
                                                   value="<?= (isset($contact_lastname_result)) ? $contact_lastname_result : ""?>" placeholder="Last name">
                                            <div class="invalid-feedback">Enter last name</div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="contact_relationship">Relationship:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <select class="form-control" name="contact_relationship" id="contact_relationship">
                                                <option value="" selected="selected">Choose one</option>
                                                <?php
                                                $res = $db->query("SELECT unnest(enum_range(NULL::relationship)) AS type", []);
                                                while ($enumtype = pg_fetch_assoc($res)) {
                                                    $t = $enumtype ['type'];
                                                    ?>
                                                    <option value="<?= $t ?>" <?= (isset($contact_relationship_result) && $contact_relationship_result == $t)  ? "selected" : "" ?>><?= $t ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 col-xl-2 d-md-block" for="contact_phone">Phone Number:</label>
                                        <div class="col-md-5 col-xl-2 col">
                                            <input type="tel" class="form-control mask-phone feedback-icon" id="contact_phone" name="contact_phone"
                                                   value="<?= (isset($contact_phone_result)) ? $contact_phone_result : ""  ?>" placeholder="(999) 999-9999">
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
                                            <label class="col-form-label col-md-12 col-xl-2 d-md-block label_fn">Child Name:</label>
                                            <div class="col-md-5 col-xl-2 col">
                                                <input type="text" class="form-control input_fn" name="child_first_name_1" maxlength="255" placeholder="First name" id="child_first_name_1">
                                            </div>
                                            <label class="col-sm-0 col-form-label sr-only label_ln">Last Name:</label>
                                            <div class="col-md-5 col-xl-2 col">
                                                <input type="text" class="form-control input_ln" name="child_last_name_1" maxlength="255" placeholder="Last name" id="child_last_name_1">
                                            </div>
                                            <label class="col-sm-0 col-form-label sr-only label_mi">Middle Initial:</label>
                                            <div class="col-md-2 col-xl-1 col">
                                                <input type="text" class="form-control input_mi" name="child_mi_1" maxlength="1" placeholder="Initial" id="child_mi_1">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-md-12 col-xl-2 d-md-block label_dob" for="child_dob_1">Date of Birth:</label>
                                            <div class="col-md-5 col-xl-2 col">
                                                <input type="date" class="form-control input_dob" name="child_dob_1" id="child_dob_1">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-md-12 col-xl-2 d-md-block label_sex">Sex:</label>
                                            <div class="col-md-5 col-xl-2 col">
                                                <select class="form-control select_sex" name="child_sex_1" id="child_sex_1">
                                                    <option value="" selected="selected">Choose one</option>
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
                                            <label class="col-form-label col-md-12 col-xl-2 d-md-block label_race">Race:</label>
                                            <div class="col-md-5 col-xl-2 col">
                                                <select class="form-control select_race" name="child_race_1" id="child_race_1">
                                                    <option value="" selected="selected">Choose one</option>
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
                                            <label class="col-form-label col-md-12 col-xl-2 d-md-block label_live">Residence:</label>
                                            <div class="col-md-10 col-xl-4 col">
                                                <input type="text" class="form-control input_live" name="child_live_1" placeholder="Where does this child live?" id="child_live_1">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-md-12 col-xl-2 d-md-block label_custody">Custody:</label>
                                            <div class="col-md-10 col-xl-4 col">
                                                <input type="text" class="form-control input_custody" name="child_custody_1" placeholder="Who has custody of this child?" id="child_custody_1">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group row childbutton controls">
                                        <label class="col-md-2 col-xl-2 col-form-label">Add Child:</label>
                                        <div class="col-sm-1 col">
                                            <button class="btn btn-default" type="button" id="btnAddChild"><span class="fa fa-plus"></span></button>
                                        </div>
                                    </div>

                                    <div class="form-group row childbutton delbtn controls">
                                        <label class="col-md-2 col-xl-2 col-form-label">Remove Child:</label>
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
                                        <div class="col-md-12 col-xl-4">
                                            <label class="form-control-label">Do you now, or have you ever had a problem with drug/alcohol abuse?</label>
                                        </div>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="custom-control custom-radio" for="drug_alcohol_abuse_yes">
                                            <input type="radio" id="drug_alcohol_abuse_yes" name="drug_alcohol_abuse" class="custom-control-input"
                                                <?= (isset($drug_alcohol_abuse_result) && $drug_alcohol_abuse_result == "t") ? "checked" : "" ?> value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="drug_alcohol_abuse_no">
                                            <input type="radio" id="drug_alcohol_abuse_no" name="drug_alcohol_abuse" class="custom-control-input"
                                                <?= (isset($drug_alcohol_abuse_result) && $drug_alcohol_abuse_result == "f") ? "checked" : "" ?> value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Drug/Alcohol Abuse -->

                                    <div class="form-group hidden-field row drug_alcohol_abuse_div_yes answer_yes">
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="col-form-label col-md-11 col-xl-2" for="drug_alcohol_abuse_explain">Please explain:</label>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <div class="col-xl-5 col-md-9">
                                            <input type="text" class="form-control" id="drug_alcohol_abuse_explain" name="drug_alcohol_abuse_explain"
                                                   value="<?= (isset($drug_alcohol_abuse_explain_result)) ? $drug_alcohol_abuse_explain_result : ""?>" placeholder="Please describe your past with drug/alcohol abuse">
                                        </div>
                                    </div>

                                    <!-- Begin Q: Live With Children -->
                                    <div class="form-group radio-group row">
                                        <div class="col-xl-4 col-md-12">
                                            <label class="form-control-label">Do you currently live with your child(ren)?</label>
                                        </div>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="custom-control custom-radio" for="live_with_children_yes">
                                            <input  type="radio" id="live_with_children_yes" name="live_with_children" class="custom-control-input"
                                                    <?= (isset($params[1]) && isset($live_with_children_separated_result) && $live_with_children_separated_result == '') ? "checked" : ""?> value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="live_with_children_no">
                                            <input type="radio" id="live_with_children_no" name="live_with_children" class="custom-control-input"
                                                <?= (isset($params[1]) && isset($live_with_children_separated_result) && $live_with_children_separated_result != '') ? "checked" : ""?> value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Live With Children -->

                                    <div class="form-group hidden-field row live_with_children_div_no answer_no">
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="col-form-label col-xl-2 col-md-11" for="live_with_children_separated">Length of Separation:</label>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <div class="col-xl-5 col-md-9">
                                            <input type="text" class="form-control" id="live_with_children_separated" name="live_with_children_separated"
                                                   value="<?= (isset($live_with_children_separated_result) && $live_with_children_separated_result != '') ? $live_with_children_separated_result : ""?>" placeholder="For how long have you been separated from your child(ren)?">
                                        </div>
                                    </div>

                                    <!-- Begin Q: Separated With Children -->
                                    <div class="form-group radio-group row">
                                        <div class="col-xl-4 col-md-12">
                                            <label class="form-control-label">Are you separated with your child(ren)'s other biological parent?</label>
                                        </div>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="custom-control custom-radio" for="parent_separated_yes">
                                            <input  type="radio" id="parent_separated_yes" name="parent_separated" class="custom-control-input"
                                                <?= (isset($separated_length_result) && !empty($separated_length_result) && isset($params[1])) ? "checked" : "" ?> value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="parent_separated_no">
                                            <input type="radio" id="parent_separated_no" name="parent_separated" class="custom-control-input"
                                                   <?= (isset($separated_length_result) && empty($separated_length_result) && isset($params[1])) ? "checked" : "" ?> value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Separated With Children-->

                                    <div class="form-group hidden-field row parent_separated_div_yes answer_yes">
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="col-form-label col-xl-2 col-md-11" for="separated_length">Please explain:</label>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <div class="col-xl-5 col-md-9">
                                            <input type="text" class="form-control" id="separated_length" name="separated_length"
                                                   value="<?= (isset($separated_length_result)) ? $separated_length_result : ""?>" placeholder="For how long have you been separated?">
                                        </div>
                                    </div>

                                    <div  class="form-group hidden-field row parent_separated_div_yes answer_yes">
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="col-form-label col-xl-2 col-md-11" for="relationship">Relationship status:</label>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <div class="col-xl-5 col-md-9">
                                            <input type="text" class="form-control" id="relationship" name="relationship"
                                                   value="<?= (isset($relationship_result)) ? $relationship_result : ""?>" placeholder="What is your relationship like?">
                                        </div>
                                    </div>

                                    <!-- Begin Q: Parent Together -->
                                    <div class="form-group radio-group row">
                                        <div class="col-xl-4 col-md-12">
                                            <label class="form-control-label">Have you and your child(ren)'s parent been able to parent together?</label>
                                        </div>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="custom-control custom-radio" for="parenting_yes">
                                            <input  type="radio" id="parenting_yes" name="parenting" class="custom-control-input"
                                                <?= (isset($parenting_result) && $parenting_result == "t") ? "checked" : "" ?> value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="parenting_no">
                                            <input type="radio" id="parenting_no" name="parenting" class="custom-control-input"
                                                <?= (isset($parenting_result) && $parenting_result == "f") ? "checked" : "" ?> value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Parent Together -->

                                    <!-- Begin Q: Currently involved CPS -->
                                    <div class="form-group radio-group row">
                                        <div class="col-xl-4 col-md-12">
                                            <label class="form-control-label">Are you involved with Child Protective Services?</label>
                                        </div>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="custom-control custom-radio" for="child_protective_yes">
                                            <input  type="radio" id="child_protective_yes" name="child_protective" class="custom-control-input"
                                                <?= (isset($child_protective_result) && $child_protective_result == "t") ? "checked" : "" ?> value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="child_protective_no">
                                            <input type="radio" id="child_protective_no" name="child_protective" class="custom-control-input"
                                                <?= (isset($child_protective_result) && $child_protective_result == "f") ? "checked" : "" ?> value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Currently involved CPS

                                    <!-- Begin Q: Previously involved CPS -->
                                    <div class="form-group radio-group row">
                                        <div class="col-xl-4 col-md-12">
                                            <label class="form-control-label">Have you previously been involved with Child Protective Services?</label>
                                        </div>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="custom-control custom-radio" for="previous_child_protective_yes">
                                            <input  type="radio" id="previous_child_protective_yes" name="previous_child_protective" class="custom-control-input"
                                                <?= (isset($previous_child_protective_result) && $previous_child_protective_result == 1) ? "checked" : "" ?> value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="previous_child_protective_no">
                                            <input type="radio" id="previous_child_protective_no" name="previous_child_protective" class="custom-control-input"
                                                <?= (isset($previous_child_protective_result) && $previous_child_protective_result == 0) ? "checked" : "" ?> value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Previously Involved CPS -->

                                    <!-- Begin Q: Mandated To Take Class -->
                                    <div class="form-group radio-group row">
                                        <div class="col-xl-4 col-md-12">
                                            <label class="form-control-label">Have you been mandated to take this class?</label>
                                        </div>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="custom-control custom-radio" for="mandated_yes">
                                            <input  type="radio" id="mandated_yes" name="mandated" class="custom-control-input"
                                                <?= (isset($mandated_result) && $mandated_result == "t") ? "checked" : "" ?> value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="mandated_no">
                                            <input type="radio" id="mandated_no" name="mandated" class="custom-control-input"
                                                <?= (isset($mandated_result) && $mandated_result == "f") ? "checked" : "" ?> value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Mandated To Take Class -->

                                    <div  class="form-group hidden-field row mandated_div_yes answer_yes">
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="col-form-label col-xl-2 col-md-11" for="mandated_by">Mandated by:</label>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <div class="col-xl-5 col-md-9">
                                            <input type="text" class="form-control" id="mandated_by" name="mandated_by"
                                                   value="<?= (isset($mandated_by_result)) ? $mandated_by_result : "" ?>" placeholder="Who mandated you?">
                                        </div>
                                    </div>

                                    <div  class="form-group hidden-field row mandated_div_yes answer_yes">
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="col-form-label col-xl-2 col-md-11" for="reason_mandated">Mandate Reason:</label>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <div class="col-xl-5 col-md-9">
                                            <input type="text" class="form-control" id="reason_mandated" name="reason_mandated"
                                                   value="<?= (isset($mandated_result) && $mandated_result == "t" && isset($reason_for_taking_class_result))
                                                            ? $reason_for_taking_class_result
                                                            : "" ?>" placeholder="Reason you were mandated (be specific)">
                                        </div>
                                    </div>

                                    <div class="form-group hidden-field row mandated_div_no answer_no">
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="col-form-label col-xl-2 col-md-11" for="reason_for_taking_class">Reason For Taking Class:</label>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <div class="col-xl-5 col-md-9">
                                            <input type="text" class="form-control" id="reason_for_taking_class"
                                                   value="<?= (isset($mandated_result) && $mandated_result == "f" && isset($reason_for_taking_class_result))
                                                            ? $reason_for_taking_class_result
                                                            : "" ?>" name="reason_for_taking_class" placeholder="Please explain...">
                                        </div>
                                    </div>

                                    <!-- Begin Q: Other Parenting Classes -->
                                    <div class="form-group radio-group row">
                                        <div class="col-xl-4 col-md-12">
                                            <label class="form-control-label">Have you attended any other parenting classes?</label>
                                        </div>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="custom-control custom-radio" for="other_classes_yes">
                                            <input  type="radio" id="other_classes_yes" name="other_classes" class="custom-control-input"
                                                <?= (isset($other_classes_result) && $other_classes_result == "t") ? "checked" : "" ?> value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="other_classes_no">
                                            <input type="radio" id="other_classes_no" name="other_classes" class="custom-control-input"
                                                <?= (isset($other_classes_result) && $other_classes_result == "f") ? "checked" : "" ?> value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Other Parenting Classes -->

                                    <div  class="form-group hidden-field row other_classes_div_yes answer_yes">
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="col-form-label col-xl-2 col-md-11" for="other_classes_where_when">Please explain:</label>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <div class="col-xl-5 col-md-9">
                                            <input type="text" class="form-control" id="other_classes_where_when" name="other_classes_where_when"
                                                   value="<?= (isset($other_classes_where_when_result)) ? $other_classes_where_when_result : "" ?>" placeholder="Where did you take classes and how long ago?">
                                        </div>
                                    </div>

                                    <!-- Begin Q: The Victim Of Abuse Or Neglect -->
                                    <div class="form-group radio-group row">
                                        <div class="col-xl-4 col-md-12">
                                            <label class="form-control-label">Were you the victim of abuse or neglect in your own childhood?</label>
                                        </div>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="custom-control custom-radio" for="victim_of_abuse_yes">
                                            <input  type="radio" id="victim_of_abuse_yes" name="victim_of_abuse" class="custom-control-input"
                                                <?= (isset($victim_of_abuse_result) && $victim_of_abuse_result == "t") ? "checked" : "" ?> value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="victim_of_abuse_no">
                                            <input type="radio" id="victim_of_abuse_no" name="victim_of_abuse" class="custom-control-input"
                                                <?= (isset($victim_of_abuse_result) && $victim_of_abuse_result == "f") ? "checked" : "" ?> value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: The Victim Of Abuse Or Neglect -->

                                    <div  class="form-group hidden-field row victim_of_abuse_div_yes answer_yes">
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="col-form-label col-xl-2 col-md-11" for="form_of_abuse">Please explain:</label>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <div class="col-xl-5 col-md-9">
                                            <input type="text" class="form-control" id="form_of_abuse" name="form_of_abuse"
                                                   value="<?= (isset($form_of_abuse_result)) ? $form_of_abuse_result : ""?>" placeholder="What form of abuse did you take?">
                                        </div>
                                    </div>

                                    <!-- Begin Q: Abuse In Therapy -->
                                    <div class="form-group radio-group row">
                                        <div class="col-xl-4 col-md-12">
                                            <label class="form-control-label">Did you ever deal with your abuse in therapy?</label>
                                        </div>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="custom-control custom-radio" for="abuse_therapy_yes">
                                            <input  type="radio" id="abuse_therapy_yes" name="abuse_therapy" class="custom-control-input"
                                                <?= (isset($abuse_therapy_result) && $abuse_therapy_result == "t") ? "checked" : "" ?> value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="abuse_therapy_no">
                                            <input type="radio" id="abuse_therapy_no" name="abuse_therapy" class="custom-control-input"
                                                <?= (isset($abuse_therapy_result) && $abuse_therapy_result == "f") ? "checked" : "" ?> value="No">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">No</span>
                                        </label>
                                    </div>
                                    <!-- End Q: Abuse In Therapy -->

                                    <!-- Begin Q: Issues Relating To Your Childhood Abuse -->
                                    <div class="form-group radio-group row">
                                        <div class="col-xl-4 col-md-12">
                                            <label class="form-control-label">Do you feel you still have some issues relating to childhood abuse?</label>
                                        </div>
                                        <div class="col-md-1 d-xl-none"></div>
                                        <label class="custom-control custom-radio" for="childhood_abuse_relating_yes">
                                            <input  type="radio" id="childhood_abuse_relating_yes" name="childhood_abuse_relating" class="custom-control-input"
                                                <?= (isset($childhood_abuse_relating_result) && $childhood_abuse_relating_result == true) ? "checked" : "" ?> value="Yes">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Yes</span>
                                        </label>
                                        <label class="custom-control custom-radio" for="childhood_abuse_relating_no">
                                            <input type="radio" id="childhood_abuse_relating_no" name="childhood_abuse_relating" class="custom-control-input"
                                                <?= (isset($childhood_abuse_relating_result) && $childhood_abuse_relating_result == false) ? "checked" : "" ?> value="No">
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
                                        <label class="col-form-label col-xl-12 col-md-12 col-12" for="class_participation" style="text-align: left">What do you need from this class to feel safe and fully participate?</label>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-xl-8 col-md-10 col-8">
                                            <input type="text" class="form-control" id="class_participation" name="class_participation"
                                                   value="<?= (isset($class_participation_result)) ? $class_participation_result : "" ?>" placeholder="Please explain...">
                                        </div>
                                    </div>
                                    <!-- End Class participation -->

                                    <!-- Begin Parenting -->
                                    <div class="row">
                                        <label class="col-form-label col-xl-12 col-md-12 col-12" for="parenting_opinion" style="text-align: left;">What behaviors would keep you from voicing your opinion on your parenting style?</label>
                                    </div>
                                    <div class="form-group row">

                                        <div class="col-xl-8 col-md-10 col-8">
                                            <input type="text" class="form-control" id="parenting_opinion" name="parenting_opinion"
                                                   value="<?= (isset($parenting_opinion_result)) ? $parenting_opinion_result : "" ?>" placeholder="Please explain...">
                                        </div>
                                    </div>
                                    <!-- End Parenting -->

                                    <!-- Begin Class Takeaway -->
                                    <div class="row">
                                        <label class="col-form-label col-xl-12 col-md-12 col-12" for="class_takeaway" style="text-align: left;">What is the most important thing you would like to learn from this class?</label>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-xl-8 col-md-10 col-8">
                                            <input type="text" class="form-control" id="class_takeaway" name="class_takeaway"
                                                   value="<?= (isset($class_takeaway_result)) ? $class_takeaway_result : "" ?>" placeholder="Please explain...">
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
                                    <div class="col-xl-4 col-md-12">
                                        <label class="form-control-label">Have you ever had any involvement with domestic violence?</label>
                                    </div>
                                    <div class="col-md-1 d-xl-none"></div>
                                    <label class="custom-control custom-radio" for="domestic_violence_yes">
                                        <input  type="radio" id="domestic_violence_yes" name="domestic_violence" class="custom-control-input"
                                            <?= (isset($domestic_violence_result) && $domestic_violence_result == "t") ? "checked" : "" ?> value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="domestic_violence_no">
                                        <input type="radio" id="domestic_violence_no" name="domestic_violence" class="custom-control-input"
                                            <?= (isset($domestic_violence_result) && $domestic_violence_result == "f") ? "checked" : "" ?> value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Domestic Violence -->

                                <div  style="margin-right: 20%" class="form-group hidden-field radio-group row domestic_violence_div_yes answer_yes">
                                    <div class="col-md-1 d-xl-none"></div>
                                    <div class="col-xl-4 col-md-11">
                                        <label class="form-control-label">Have you discussed it with someone?</label>
                                    </div>
                                    <div class="col-md-2 d-xl-none"></div>
                                    <label class="custom-control custom-radio" for="domestic_violence_discussed_yes">
                                        <input  type="radio" id="domestic_violence_discussed_yes" name="domestic_violence_discussed" class="custom-control-input"
                                                <?= (isset($domestic_violence_discussed_result) && $domestic_violence_discussed_result == true) ? "checked" : "" ?> value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="domestic_violence_discussed_no">
                                        <input type="radio" id="domestic_violence_discussed_no" name="domestic_violence_discussed" class="custom-control-input"
                                            <?= (isset($domestic_violence_discussed_result) && $domestic_violence_discussed_result == false) ? "checked" : "" ?> value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>

                                <!-- Begin Q: History Of Family Violence -->
                                <div class="form-group radio-group row">
                                    <div class="col-xl-4 col-md-12">
                                        <label class="form-control-label">Is there any history of violence in your family of origin?</label>
                                    </div>
                                    <div class="col-md-1 d-xl-none"></div>
                                    <label class="custom-control custom-radio" for="history_violence_family_yes">
                                        <input  type="radio" id="history_violence_family_yes" name="history_violence_family" class="custom-control-input"
                                            <?= (isset($history_violence_family_result) && $history_violence_family_result == "t") ? "checked" : "" ?> value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="history_violence_family_no">
                                        <input type="radio" id="history_violence_family_no" name="history_violence_family" class="custom-control-input"
                                            <?= (isset($history_violence_family_result) && $history_violence_family_result == "f") ? "checked" : "" ?> value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: History Of Family Violence -->

                                <!-- Begin Q: Nuclear Family Violence -->
                                <div class="form-group radio-group row">
                                    <div class="col-xl-4 col-md-12">
                                        <label class="form-control-label">Is there any history of violence in your nuclear family?</label>
                                    </div>
                                    <div class="col-md-1 d-xl-none"></div>
                                    <label class="custom-control custom-radio" for="history_violence_nuclear_yes">
                                        <input  type="radio" id="history_violence_nuclear_yes" name="history_violence_nuclear" class="custom-control-input"
                                            <?= (isset($history_violence_nuclear_result) && $history_violence_nuclear_result == "t") ? "checked" : "" ?> value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="history_violence_nuclear_no">
                                        <input type="radio" id="history_violence_nuclear_no" name="history_violence_nuclear" class="custom-control-input"
                                            <?= (isset($history_violence_nuclear_result) && $history_violence_nuclear_result == "f") ? "checked" : "" ?> value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Nuclear Family Violence -->

                                <!-- Begin Q: Protection Orders -->
                                <div class="form-group radio-group row">
                                    <div class="col-xl-4 col-md-12">
                                        <label class="form-control-label">Are there any orders of protection involved?</label>
                                    </div>
                                    <div class="col-md-1 d-xl-none"></div>
                                    <label class="custom-control custom-radio" for="protection_order_yes">
                                        <input  type="radio" id="protection_order_yes" name="protection_order" class="custom-control-input"
                                            <?= (isset($protection_order_result) && $protection_order_result == "t") ? "checked" : "" ?> value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="protection_order_no">
                                        <input type="radio" id="protection_order_no" name="protection_order" class="custom-control-input"
                                            <?= (isset($protection_order_result) && $protection_order_result == "f") ? "checked" : "" ?> value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Protection Orders-->

                                <div  class="form-group hidden-field row protection_order_div_yes answer_yes">
                                    <div class="col-md-1 d-xl-none"></div>
                                    <label class="col-form-label col-xl-2 col-md-11" for="protection_order_explain">Please explain:</label>
                                    <div class="col-md-1 d-xl-none"></div>
                                    <div class="col-xl-5 col-md-9">
                                        <input type="text" class="form-control" id="protection_order_explain" name="protection_order_explain"
                                               value="<?= (isset($protection_order_explain_result)) ? $protection_order_explain_result : "" ?>" placeholder="Why and who are they against?">
                                    </div>
                                </div>

                                <!-- Begin Q: Arrested For A Crime -->
                                <div class="form-group radio-group row">
                                    <div class="col-xl-4 col-md-12">
                                        <label class="form-control-label">Have you ever been arrested for a crime?</label>
                                    </div>
                                    <div class="col-md-1 d-xl-none"></div>
                                    <label class="custom-control custom-radio" for="crime_arrested_yes">
                                        <input  type="radio" id="crime_arrested_yes" name="crime_arrested" class="custom-control-input"
                                            <?= (isset($crime_arrested_result) && $crime_arrested_result == "t") ? "checked" : "" ?> value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="crime_arrested_no">
                                        <input type="radio" id="crime_arrested_no" name="crime_arrested" class="custom-control-input"
                                            <?= (isset($crime_arrested_result) && $crime_arrested_result == "f") ? "checked" : "" ?> value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Arrested For A Crime -->

                                <!-- Begin Q: Convicted For A Crime -->
                                <div class="form-group radio-group row">
                                    <div class="col-xl-4 col-md-12">
                                        <label class="form-control-label">Have you ever been convicted for a crime?</label>
                                    </div>
                                    <div class="col-md-1 d-xl-none"></div>
                                    <label class="custom-control custom-radio" for="crime_convicted_yes">
                                        <input  type="radio" id="crime_convicted_yes" name="crime_convicted" class="custom-control-input"
                                            <?= (isset($crime_convicted_result) && $crime_convicted_result == "t") ? "checked" : "" ?> value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="crime_convicted_no">
                                        <input type="radio" id="crime_convicted_no" name="crime_convicted" class="custom-control-input"
                                            <?= (isset($crime_convicted_result) && $crime_convicted_result == "f") ? "checked" : "" ?> value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Convicted For A Crime -->

                                <div  class="form-group hidden-field row crime_convicted_div_yes answer_yes">
                                    <div class="col-md-1 d-xl-none"></div>
                                    <label class="col-form-label col-xl-2 col-md-11" for="crime_explain">Please explain:</label>
                                    <div class="col-md-1 d-xl-none"></div>
                                    <div class="col-xl-5 col-md-9">
                                        <input type="text" class="form-control" id="crime_explain" name="crime_explain"
                                               value="<?= (isset($crime_explain_result)) ? $crime_explain_result : "" ?>" placeholder="Please provide an explanation">
                                    </div>
                                </div>

                                <!-- Begin Q: Jail Or Prison Record -->
                                <div class="form-group radio-group row">
                                    <div class="col-xl-4 col-md-12">
                                        <label class="form-control-label">Do you have a jail and/or prison record?</label>
                                    </div>
                                    <div class="col-md-1 d-xl-none"></div>
                                    <label class="custom-control custom-radio" for="jail_prison_record_yes">
                                        <input  type="radio" id="jail_prison_record_yes" name="jail_prison_record" class="custom-control-input"
                                            <?= (isset($jail_prison_record_result) && $jail_prison_record_result == "t") ? "checked" : "" ?> value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="jail_prison_record_no">
                                        <input type="radio" id="jail_prison_record_no" name="jail_prison_record" class="custom-control-input"
                                            <?= (isset($jail_prison_record_result) && $jail_prison_record_result == "f") ? "checked" : "" ?> value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Jail Or Prison Record -->

                                <div  class="form-group hidden-field row jail_prison_record_div_yes answer_yes">
                                    <div class="col-md-1 d-xl-none"></div>
                                    <label class="col-form-label col-xl-2 col-md-11" for="jail_prison_explain">Please explain:</label>
                                    <div class="col-md-1 d-xl-none"></div>
                                    <div class="col-xl-5 col-md-9">
                                        <input type="text" class="form-control" id="jail_prison_explain" name="jail_prison_explain"
                                               value="<?= (isset($jail_prison_explain_result)) ? $jail_prison_explain_result : "" ?>" placeholder="When were you in jail/prison and for what offense?">
                                    </div>
                                </div>

                                <!-- Begin Q: Parole Or Probation -->
                                <div class="form-group radio-group row">
                                    <div class="col-xl-4 col-md-12">
                                        <label class="form-control-label">Are you currently on parole or probation?</label>
                                    </div>
                                    <div class="col-md-1 d-xl-none"></div>
                                    <label class="custom-control custom-radio" for="parole_probation_yes">
                                        <input  type="radio" id="parole_probation_yes" name="parole_probation" class="custom-control-input"
                                            <?= (isset($parole_probation_result) && $parole_probation_result == "t") ? "checked" : "" ?> value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="parole_probation_no">
                                        <input type="radio" id="parole_probation_no" name="parole_probation" class="custom-control-input"
                                            <?= (isset($parole_probation_result) && $parole_probation_result == "f") ? "checked" : "" ?> value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Parole Or Probation -->

                                <div  class="form-group hidden-field row parole_probation_div_yes answer_yes">
                                    <div class="col-md-1 d-xl-none"></div>
                                    <label class="col-form-label col-xl-2 col-md-11" for="parole_probation_explain">Please explain:</label>
                                    <div class="col-md-1 d-xl-none"></div>
                                    <div class="col-xl-5 col-md-9">
                                        <input type="text" class="form-control" id="parole_probation_explain" name="parole_probation_explain"
                                               value="<?= (isset($parole_probation_explain_result)) ? $parole_probation_explain_result : "" ?>" placeholder="For what offense?">
                                    </div>
                                </div>

                                <!-- Begin Q: Other Family Members Attending Class-->
                                <div class="form-group radio-group row">
                                    <div class="col-xl-4 col-md-12">
                                        <label class="form-control-label">Are there any other members of your family taking a parenting class with this agency?</label>
                                    </div>
                                    <div class="col-md-1 d-xl-none"></div>
                                    <label class="custom-control custom-radio" for="family_members_taking_class_yes">
                                        <input  type="radio" id="family_members_taking_class_yes" name="family_members_taking_class" class="custom-control-input"
                                            <?= (isset($family_members_taking_class_result) && $family_members_taking_class_result == "t") ? "checked" : "" ?> value="Yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio" for="family_members_taking_class_no">
                                        <input type="radio" id="family_members_taking_class_no" name="family_members_taking_class" class="custom-control-input"
                                            <?= (isset($family_members_taking_class_result) && $family_members_taking_class_result == "f") ? "checked" : "" ?> value="No">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">No</span>
                                    </label>
                                </div>
                                <!-- End Q: Other Family Members Attending Class -->

                                <div  class="form-group hidden-field row family_members_taking_class_div_yes answer_yes">
                                    <div class="col-md-1 d-xl-none"></div>
                                    <label class="col-form-label col-xl-2 col-md-11" for="family_members">Family Members:</label>
                                    <div class="col-md-1 d-xl-none"></div>
                                    <div class="col-xl-5 col-md-9">
                                        <input type="text" class="form-control" id="family_members" name="family_members"
                                               value="<?= (isset($family_members_result)) ? $family_members_result : "" ?>" placeholder="Please list their name(s)">
                                    </div>
                                </div>
                            </div>
                        </div>   <!-- 4th collapsible end -->
                    </div>
                    </div>
                </form>
            </div>  <!-- panel group end -->

            <?php
                if (isset($params[0]) && isset($params[1]) && isset($params[2])) {


                    // Second Card (Participant Children Information)
                    $formID = $params[2];

                    // Counts how many children are associated with a particular form.
                    $children_edit = $db->query("SELECT COUNT(familyMemberID)
                                                        FROM FamilyInfo, Children
                                                        WHERE FamilyInfo.formID = $1 
                                                        AND Children.childrenID = FamilyInfo.FamilyMemberID;", [$formID]);
                    $children__count = pg_fetch_result($children_edit, 0);

                    $children_all_edit = $db->query("SELECT * FROM Children 
                                                            INNER JOIN People ON people.peopleid = children.childrenID 
                                                            INNER JOIN familymembers ON familymembers.familymemberid = children.childrenid 
                                                            INNER JOIN family ON family.familymembersid = children.childrenid
                                                            WHERE family.formID = $1;", [$formID]);

                    // For loop based on how many children were inputted into a particular form.
                    for ($i = 1; $i <= $children__count; $i++) {

                        // Create variable names for names of fields (JavaScript ids)
                        $chd_first_name = "child_first_name_" . $i;
                        $chd_last_name = "child_last_name_" . $i;
                        $chd_mi = "child_mi_" . $i;
                        $chd_dob = "child_dob_" . $i;
                        $chd_race = "child_race_" . $i;
                        $chd_sex = "child_sex_" . $i;
                        $chd_live = "child_live_" . $i;
                        $chd_custody = "child_custody_" . $i;

                        // Get each row based on how many rows are returned.

                        $row = pg_fetch_assoc($children_all_edit, $i-1);

                        $$chd_first_name = $row['firstname'];
                        $$chd_last_name = $row['lastname'];
                        $$chd_mi = $row['middleinit'];
                        $$chd_dob = $row['dateofbirth'];
                        $$chd_race = $row['race'];
                        $$chd_sex = $row['sex'];
                        $$chd_live = $row['location'];
                        $$chd_custody = $row['custody'];

            ?>
                        <script type="text/javascript">
                            // Insert results into fields.
                            $('#child_first_name_<?= $i ?>').val('<?= $$chd_first_name ?>');
                            $('#child_last_name_<?= $i ?>').val('<?= $$chd_last_name ?>');
                            $('#child_mi_<?= $i ?>').val('<?= $$chd_mi ?>');
                            $('#child_dob_<?= $i ?>').val('<?= $$chd_dob ?>');
                            $('#child_race_<?= $i ?>').val('<?= $$chd_race ?>');
                            $('#child_sex_<?= $i ?>').val('<?= $$chd_sex ?>');
                            $('#child_live_<?= $i ?>').val('<?= $$chd_live ?>');
                            $('#child_custody_<?= $i ?>').val('<?= $$chd_custody ?>');
                        </script>

                        <?php

                            if ($i < $children__count)
                                // Open up the appropriate amount of fields (add child is called).
                                echo '<script>addChild();</script>';
                    }
                }

                if (isset($params[0]) && $params[0] == "edit"){
                    echo '<button id="btnUpdate" onclick="submitAllIntake()" class="cpca btn">Update</button>';
                } else if (isset($params[0]) && $params[0] == "view") {
                    echo '<a href="/ps-view-participant/'.$params[1].'"><button id="btnView" class="cpca btn">Back To Participant</button></a>';
                 } else {
                    include('form_duplicate_check.php');
                }

            if(isset($params[0]) && $params[0] == "view") {
                echo '<script type="text/javascript">',
                'disableIntakeFields();',
                '</script>';
            } else if (isset($params[0]) && $params[0] == "edit"){
                echo '<script type="text/javascript">',
                'intakeEditUpdates();',
                '</script>';
            }
            ?>

        </div>  <!-- /#container -->
    </div>  <!-- /#container-fluid class -->
<script>
	formChanged = false;
	$("select,input,textarea").change(function () {formChanged = true;});
	
	<!--Adds the "Are you sure you want to leave?" pop-up to page-->
	window.onbeforeunload = function() {
		if (formChanged)
			return true;
		else
			return null;
	};
</script>
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
<?php include('footer.php');
?>