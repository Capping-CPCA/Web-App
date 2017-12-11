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
 * @version 1.2.2
 * @since 0.3.2
 */

authorizedPage();
global $db, $params, $route, $view;

// Checks if the page has a people ID associated for updating/editing forms.
if (isset($params[0]) && isset($params[1]) && isset($params[2])) {
    $peopleid = $params[1];
    $referral_formID = $params[2];

    // SELECT FROM VIEWS TO POPULATE FIELDS
    // First Card (Participant Information)
    $pers_firstname_edit = $db->query("SELECT AgencyReferralInfo.firstName FROM AgencyReferralInfo WHERE peopleID = $1;", [$peopleid]);
    $pers_firstname_result = pg_fetch_result($pers_firstname_edit, 0);

    $pers_lastname_edit = $db->query("SELECT AgencyReferralInfo.lastName FROM AgencyReferralInfo WHERE peopleID = $1;", [$peopleid]);
    $pers_lastname_result = pg_fetch_result($pers_lastname_edit, 0);

    $pers_middlein_edit = $db->query("SELECT AgencyReferralInfo.middleInit FROM AgencyReferralInfo WHERE peopleID = $1;", [$peopleid]);
    $pers_middlein_result = pg_fetch_result($pers_middlein_edit, 0);

    $pers_dob_edit = $db->query("SELECT AgencyReferralInfo.PDoB FROM AgencyReferralInfo WHERE peopleID = $1;", [$peopleid]);
    $pers_dob_result = pg_fetch_result($pers_dob_edit, 0);

    $pers_race_edit = $db->query("SELECT AgencyReferralInfo.PRace FROM AgencyReferralInfo WHERE peopleID = $1;", [$peopleid]);
    $pers_race_result = pg_fetch_result($pers_race_edit, 0);

    $pers_sex_edit = $db->query("SELECT AgencyReferralInfo.PSex FROM AgencyReferralInfo WHERE peopleID = $1;", [$peopleid]);
    $pers_sex_result = pg_fetch_result($pers_sex_edit, 0);

    $pers_street_num_edit = $db->query("SELECT Addresses.addressNumber FROM Addresses WHERE addressID = $1;", [$referral_formID]);
    $pers_street_num_result = pg_fetch_result($pers_street_num_edit, 0);

    $pers_street_name_edit = $db->query("SELECT Addresses.street FROM Addresses WHERE addressID = $1;", [$referral_formID]);
    $pers_street_name_result = pg_fetch_result($pers_street_name_edit, 0);

    $pers_zip_edit = $db->query("SELECT Addresses.zipCode FROM Addresses WHERE addressID = $1;", [$referral_formID]);
    $pers_zip_result = pg_fetch_result($pers_zip_edit, 0);

    $pers_state_edit = $db->query("SELECT ZipCodes.state FROM ZipCodes WHERE zipCode = $1;", [$pers_zip_result]);
    $pers_state_result = pg_fetch_result($pers_state_edit, 0);

    $pers_city_edit = $db->query("SELECT ZipCodes.city FROM ZipCodes WHERE zipCode = $1;", [$pers_zip_result]);
    $pers_city_result = pg_fetch_result($pers_city_edit, 0);

    $pers_apt_info_edit = $db->query("SELECT Addresses.aptinfo FROM Addresses WHERE addressID = $1;", [$referral_formID]);
    $pers_apt_info_result = pg_fetch_result($pers_apt_info_edit, 0);

    $pers_primphone_edit = $db->query("SELECT FormPhoneNumbers.phoneNumber FROM FormPhoneNumbers WHERE formID = $1 AND phoneType = 'Primary';", [$referral_formID]);
    $pers_primphone_result = pg_fetch_result($pers_primphone_edit, 0);


    $pers_secphone_edit = $db->query("SELECT FormPhoneNumbers.phoneNumber FROM FormPhoneNumbers WHERE formID = $1 AND phoneType = 'Secondary';", [$referral_formID]);
    $pers_secphone_result = pg_fetch_result($pers_secphone_edit, 0);

    $pers_reason_edit = $db->query("SELECT AgencyReferralInfo.reason FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $pers_reason_result = pg_fetch_result($pers_reason_edit, 0);

    // Second Card (Referring Party Information)
    $ref_party_edit = $db->query("SELECT AgencyReferralInfo.reason FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $ref_party_result = pg_fetch_result($ref_party_edit, 0);

    $ref_date_edit = $db->query("SELECT Forms.employeeSignedDate FROM Forms WHERE formID = $1;", [$referral_formID]);
    $ref_date_result = pg_fetch_result($ref_date_edit, 0);

    $pers_reason_edit = $db->query("SELECT AgencyReferralInfo.reason FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $pers_reason_result = pg_fetch_result($pers_reason_edit, 0);

    // Fourth Card (Additional Information)
    $chkSpecialEd_edit = $db->query("SELECT AgencyReferralInfo.hasSpecialNeeds FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $chkSpecialEd_result = pg_fetch_result($chkSpecialEd_edit, 0);

    $chkCPS_edit = $db->query("SELECT AgencyReferralInfo.hasInvolvementCPS FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $chkCPS_result = pg_fetch_result($chkCPS_edit, 0);

    $chkSubAbuse_edit = $db->query("SELECT AgencyReferralInfo.hasSubstanceAbuseHistory FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $chkSubAbuse_result = pg_fetch_result($chkSubAbuse_edit, 0);

    $chkMental_edit = $db->query("SELECT AgencyReferralInfo.hasMentalHealth FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $chkMental_result = pg_fetch_result($chkMental_edit, 0);

    $chkPreg_edit = $db->query("SELECT AgencyReferralInfo.isPregnant FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $chkPreg_result = pg_fetch_result($chkPreg_edit, 0);

    $chkIQ_edit = $db->query("SELECT AgencyReferralInfo.hasIQDoc FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $chkIQ_result = pg_fetch_result($chkIQ_edit, 0);

    $chkViolence_edit = $db->query("SELECT AgencyReferralInfo.hasDomesticViolenceHistory FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $chkViolence_result = pg_fetch_result($chkViolence_edit, 0);

    $chkReside_edit = $db->query("SELECT AgencyReferralInfo.childrenLiveWithIndividual FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $chkReside_result = pg_fetch_result($chkReside_edit, 0);

    $chkSigned_edit = $db->query("SELECT AgencyReferralInfo.hasAgencyConsentForm FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $chkSigned_result = pg_fetch_result($chkSigned_edit, 0);

    $additional_info_edit = $db->query("SELECT AgencyReferralInfo.additionalInfo FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $additional_info_result = pg_fetch_result($additional_info_edit, 0);

    $main_agency_edit = $db->query("SELECT peopleid, agency, phone, email, firstname, lastname, ismaincontact FROM contactagencymembers
                                               INNER JOIN People ON people.peopleid = contactagencymembers.contactagencyID
                                               INNER JOIN contactagencyassociatedwithreferred ON contactagencyassociatedwithreferred.contactagencyid = contactagencymembers.contactagencyid
                                               WHERE agencyreferralid = $1
                                               AND isMainContact = TRUE;", [$referral_formID]);
    $main_agency_result = pg_fetch_assoc($main_agency_edit);

    $ref_party_result = $main_agency_result['agency'];
    $ref_firstname_result = $main_agency_result['firstname'];
    $ref_lastname_result = $main_agency_result['lastname'];
    $ref_phone_result = $main_agency_result['phone'];
    $ref_email_result = $main_agency_result['email'];


    // Fifth Card (Office Information)
    $office_contact_date_edit = $db->query("SELECT AgencyReferralInfo.dateFirstContact FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $office_contact_date_result = pg_fetch_result($office_contact_date_edit, 0);

    $office_means_edit = $db->query("SELECT AgencyReferralInfo.meansOfContact FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $office_means_result = pg_fetch_result($office_means_edit, 0);

    $office_initial_date_edit = $db->query("SELECT AgencyReferralInfo.dateOfInitialMeet FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $office_initial_date_result = explode(" ", pg_fetch_result($office_initial_date_edit, 0));
    $office_initial_date_result = $office_initial_date_result[0];

    $office_location_edit = $db->query("SELECT AgencyReferralInfo.ARLocation FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $office_location_result = pg_fetch_result($office_location_edit, 0);

    $comments_edit = $db->query("SELECT AgencyReferralInfo.comments FROM AgencyReferralInfo WHERE formID = $1;", [$referral_formID]);
    $comments_result = pg_fetch_result($comments_edit, 0);
    // END OF VIEW QUERIES


}

if ($_SERVER[ 'REQUEST_METHOD' ] == 'POST') {

    /*************
     * VARIABLES *
     *************/

    $form_type = "agency referral";

    // First card (Participant Information)
   $pers_firstname = !empty($_POST['pers_firstname']) ? (trim($_POST['pers_firstname'])) : NULL;
    $pers_lastname = !empty($_POST['pers_lastname']) ? (trim($_POST['pers_lastname'])) : NULL;
    $pers_middlein = !empty($_POST['pers_middlein']) ? trim($_POST['pers_middlein']) : NULL;
    $pers_dob = !empty($_POST['pers_dob']) ? trim($_POST['pers_dob']) : NULL;
    $pers_sex = !empty($_POST['pers_sex']) ? trim($_POST['pers_sex']) : NULL;
    $pers_race = !empty($_POST['pers_race']) ? trim($_POST['pers_race']) : NULL;
    $pers_address = !empty($_POST['pers_address']) ? trim($_POST['pers_address']) : NULL;
    $address_info = explode(" ", $pers_address);
    $pers_address_num = NULL;
    $pers_address_street = NULL;

    // Address separation logic
    for ($i = 0; $i < sizeOf($address_info); $i++) {
        if ($i === 0) {
            if ($address_info[$i] !== "") {
                if (is_numeric($address_info[$i])) {
                    $pers_address_num = $address_info[$i];
                } else {
                    $pers_address_street .= " " . $address_info[$i];
                }
            }
        } else {
            $pers_address_street .= " " . $address_info[$i];
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

    $family_first_name_6 = !empty($_POST['family_first_name_6']) ? trim($_POST['family_first_name_6']) : NULL;
    $family_last_name_6 = !empty($_POST['family_last_name_6']) ? trim($_POST['family_last_name_6']) : NULL;
    $family_mi_6 = !empty($_POST['family_mi_6']) ? trim($_POST['family_mi_6']) : NULL;
    $family_dob_6 = !empty($_POST['family_dob_6']) ? trim($_POST['family_dob_6']) : NULL;
    $family_sex_6 = !empty($_POST['family_sex_6']) ? trim($_POST['family_sex_6']) : NULL;
    $family_race_6 = !empty($_POST['family_race_6']) ? trim($_POST['family_race_6']) : NULL;
    $family_relationship_6 = !empty($_POST['family_relationship_6']) ? trim($_POST['family_relationship_6']) : NULL;

    $family_first_name_7 = !empty($_POST['family_first_name_7']) ? trim($_POST['family_first_name_7']) : NULL;
    $family_last_name_7 = !empty($_POST['family_last_name_7']) ? trim($_POST['family_last_name_7']) : NULL;
    $family_mi_7 = !empty($_POST['family_mi_7']) ? trim($_POST['family_mi_7']) : NULL;
    $family_dob_7 = !empty($_POST['family_dob_7']) ? trim($_POST['family_dob_7']) : NULL;
    $family_sex_7 = !empty($_POST['family_sex_7']) ? trim($_POST['family_sex_7']) : NULL;
    $family_race_7 = !empty($_POST['family_race_7']) ? trim($_POST['family_race_7']) : NULL;
    $family_relationship_7 = !empty($_POST['family_relationship_7']) ? trim($_POST['family_relationship_7']) : NULL;
    $family_needs_7 = !empty($_POST['family_needs_7']) ? trim($_POST['family_needs_7']) : NULL;

    $family_first_name_8 = !empty($_POST['family_first_name_8']) ? trim($_POST['family_first_name_8']) : NULL;
    $family_last_name_8 = !empty($_POST['family_last_name_8']) ? trim($_POST['family_last_name_8']) : NULL;
    $family_mi_8 = !empty($_POST['family_mi_8']) ? trim($_POST['family_mi_8']) : NULL;
    $family_dob_8 = !empty($_POST['family_dob_8']) ? trim($_POST['family_dob_8']) : NULL;
    $family_sex_8 = !empty($_POST['family_sex_8']) ? trim($_POST['family_sex_8']) : NULL;
    $family_race_8 = !empty($_POST['family_race_8']) ? trim($_POST['family_race_8']) : NULL;
    $family_relationship_8 = !empty($_POST['family_relationship_8']) ? trim($_POST['family_relationship_8']) : NULL;

    $family_first_name_9 = !empty($_POST['family_first_name_9']) ? trim($_POST['family_first_name_9']) : NULL;
    $family_last_name_9 = !empty($_POST['family_last_name_9']) ? trim($_POST['family_last_name_9']) : NULL;
    $family_mi_9 = !empty($_POST['family_mi_9']) ? trim($_POST['family_mi_9']) : NULL;
    $family_dob_9 = !empty($_POST['family_dob_9']) ? trim($_POST['family_dob_9']) : NULL;
    $family_sex_9 = !empty($_POST['family_sex_9']) ? trim($_POST['family_sex_9']) : NULL;
    $family_race_9 = !empty($_POST['family_race_9']) ? trim($_POST['family_race_9']) : NULL;
    $family_relationship_9 = !empty($_POST['family_relationship_9']) ? trim($_POST['family_relationship_9']) : NULL;

    $family_first_name_10 = !empty($_POST['family_first_name_10']) ? trim($_POST['family_first_name_10']) : NULL;
    $family_last_name_10 = !empty($_POST['family_last_name_10']) ? trim($_POST['family_last_name_10']) : NULL;
    $family_mi_10 = !empty($_POST['family_mi_10']) ? trim($_POST['family_mi_10']) : NULL;
    $family_dob_10 = !empty($_POST['family_dob_10']) ? trim($_POST['family_dob_10']) : NULL;
    $family_sex_10 = !empty($_POST['family_sex_10']) ? trim($_POST['family_sex_10']) : NULL;
    $family_race_10 = !empty($_POST['family_race_10']) ? trim($_POST['family_race_10']) : NULL;
    $family_relationship_10 = !empty($_POST['family_relationship_10']) ? trim($_POST['family_relationship_10']) : NULL;

    // Fourth Card (Additional Information)
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



    if(isset($params[0]) && isset($params[1]) && isset($params[2])){

        /******************
         * UPDATE QUERIES *
         ******************/

        $updatePeopleResult = $db->query("UPDATE
                                    People
                                    SET
                                    firstName = $1,
                                    lastName = $2,
                                    middleInit = $3
                                    WHERE 
                                    peopleID = $4;", [$pers_firstname, $pers_lastname, $pers_middlein, $params[1]]);
        $updateParticipantResult = $db->query("UPDATE 
                                    Participants
                                    SET 
                                    dateOfBirth = $1,
                                    race = $2,
                                    sex = $3
                                    WHERE 
                                    participantID = $4;", [$pers_dob, $pers_race, $pers_sex, $params[1]]);

        $newZip = $db->query("INSERT INTO ZipCodes VALUES($1, $2, $3);", [$pers_zip, $pers_city, $pers_state]);

        $updateAddressResult = $db->query("UPDATE
                                    Addresses
                                    SET
                                    addressNumber = $1,
                                    aptInfo = $2,
                                    street = $3,
                                    zipCode = $4
                                    WHERE
                                    addressID = $5;", [$pers_address_num, $pers_apartment_info, $pers_address_street, $pers_zip, $params[2]]);


        $updatePrimaryPhoneResult = $db->query("UPDATE 
                                    FormPhoneNumbers
                                    SET 
                                    phoneNumber = $1
                                    WHERE
                                    formID = $2 AND
                                    phoneType = $3;", [$pers_primphone, $params[2], 'Primary']);

        $updatePrimaryPhoneInsert = $db->query("INSERT INTO 
                                                   FormPhoneNumbers (formID, phoneNumber, phoneType)
                                                   SELECT $1, $2, $3
                                                   WHERE NOT EXISTS (SELECT 1 FROM FormPhoneNumbers WHERE formID = $1 AND phoneType = $3);", [$params[2], $pers_primphone, 'Primary']);

        $updateSecPhoneResult = $db->query("UPDATE 
                                    FormPhoneNumbers
                                    SET 
                                    phoneNumber = $1
                                    WHERE
                                    formID = $2 AND
                                    phoneType = $3;", [$pers_secphone, $params[2], 'Secondary']);

        $updateSecPhoneInsert = $db->query("INSERT INTO 
                                                   FormPhoneNumbers (formID, phoneNumber, phoneType)
                                                   SELECT $1, $2, $3
                                                   WHERE NOT EXISTS (SELECT 1 FROM FormPhoneNumbers WHERE formID = $1 AND phoneType = $3);", [$params[2], $pers_secphone, 'Secondary']);

        $updateReferralResult = $db->query("UPDATE
                                    AgencyReferral
                                    SET
                                    agencyReferralID = $1,
                                    reason = $2,
                                    hasAgencyConsentForm = $3,
                                    additionalInfo = $4,
                                    hasSpecialNeeds = $5,
                                    hasSubstanceAbuseHistory = $6,
                                    hasInvolvementCPS = $7,
                                    isPregnant = $8,
                                    hasIQDoc = $9,
                                    hasMentalHealth = $10,
                                    hasDomesticViolenceHistory = $11,
                                    childrenLiveWithIndividual = $12,
                                    dateFirstContact = $13,
                                    meansOfContact = $14,
                                    dateOfInitialMeet = $15,
                                    location = $16,
                                    comments = $17
                                    WHERE
                                    agencyReferralID = $18;", [$params[2],
                                                                $pers_reason,
                                                                $chkSigned,
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
                                                                $params[2]]);

        $editRefDate = $db->query("UPDATE 
                                    Forms
                                    SET
                                    employeeSignedDate = $1
                                    WHERE 
                                    formID = $2;", [$ref_date, $params[2]]);

        // Household queries to check how many current household members there are and if we need to add more
        $household_edit = $db->query("SELECT COUNT (familymembersid)
                                                     FROM family
                                                     WHERE family.formID = $1;", [$referral_formID]);
        $household_count = pg_fetch_result($household_edit, 0);

        $household_all_edit = $db->query("SELECT * FROM familymembers
                                                         INNER JOIN People ON people.peopleid = familymembers.familymemberid
                                                         INNER JOIN family ON family.familymembersid = familymembers.familymemberid
                                                         WHERE family.formID = $1;", [$referral_formID]);

        // For loop based on how many household members were inputted into a particular form.
        for ($i = 1; $i <= 10; $i++) {

            // Create variable names for names of fields (JavaScript ids)
            $fam_first_name = "family_first_name_" . $i;
            $fam_last_name = "family_last_name_" . $i;
            $fam_mi = "family_mi_" . $i;
            $fam_relationship = "family_relationship_" . $i;
            $fam_dob = "family_dob_" . $i;
            $fam_race = "family_race_" . $i;
            $fam_sex = "family_sex_" . $i;

            if ($i <= $household_count) {
                $row = pg_fetch_assoc($household_all_edit, $i - 1);
                $fam_id = $row['familymemberid'];
                $updateFamNameResult = $db->query("UPDATE
                                    People
                                    SET
                                    firstName = $1,
                                    lastName = $2,
                                    middleInit = $3
                                    WHERE 
                                    peopleID = $4;", [$$fam_first_name, $$fam_last_name, $$fam_mi, $fam_id]);

                $updateFamResult = $db->query("UPDATE
                                                FamilyMembers
                                                SET
                                                dateOfBirth = $1,
                                                race = $2,
                                                sex = $3,
                                                relationship = $4
                                                WHERE
                                                familyMemberID = $5;", [$$fam_dob, $$fam_race, $$fam_sex, $$fam_relationship, $fam_id]);

            } else {

                if ($$fam_first_name !== NULL && $$fam_last_name !== NULL) {

                    if ($$fam_relationship === "Son" || $$fam_relationship === "Daughter")
                        $isChild = 1;
                    else
                        $isChild = 0;

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
                                            fID := $11::INT)", [$$fam_first_name, $$fam_last_name, $$fam_mi, $$fam_relationship, $$fam_dob, $$fam_race, $$fam_sex, $isChild, NULL, NULL, $params[2]]);
                }
            }
        }

        // Counts how many parties are associated with a particular form.
        $party_edit = $db->query("SELECT COUNT (contactagencyid) 
                                             FROM contactagencyassociatedwithreferred
                                             WHERE agencyreferralid = $1
                                             AND isMainContact != TRUE;", [$referral_formID]);
        $party_count = pg_fetch_result($party_edit, 0);

        $parties_all_edit = $db->query("SELECT peopleid, agency, phone, email, firstname, lastname, ismaincontact FROM contactagencymembers
                                                   INNER JOIN People ON people.peopleid = contactagencymembers.contactagencyID
                                                   INNER JOIN contactagencyassociatedwithreferred ON contactagencyassociatedwithreferred.contactagencyid = contactagencymembers.contactagencyid
                                                   WHERE agencyreferralid = $1
                                                   AND isMainContact != TRUE;", [$referral_formID]);

        $main_agency_edit = $db->query("SELECT peopleid, agency, phone, email, firstname, lastname, ismaincontact FROM contactagencymembers
                                               INNER JOIN People ON people.peopleid = contactagencymembers.contactagencyID
                                               INNER JOIN contactagencyassociatedwithreferred ON contactagencyassociatedwithreferred.contactagencyid = contactagencymembers.contactagencyid
                                               WHERE agencyreferralid = $1
                                               AND isMainContact = TRUE;", [$referral_formID]);
        $main_agency_result = pg_fetch_assoc($main_agency_edit);

        $ref_id = $main_agency_result['peopleid'];
        $ref_firstname_result = $main_agency_result['firstname'];
        $ref_lastname_result = $main_agency_result['lastname'];


        // Insert new main agency if one doesn't exist
        if ($ref_firstname !== NULL && $ref_lastname !== NULL && $ref_firstname_result == NULL && $ref_lastname_result == NULL) {
            $pIDMainAgency = $db->query("SELECT PeopleInsert(
                                               fName := $1::TEXT,
                                               lName := $2::TEXT,
                                               mInit := $3::VARCHAR
                                               );", [$ref_firstname, $ref_lastname, NULL]);
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
                                );", [$pIDMainAgency, $ref_party, $ref_phone, $ref_email, TRUE, $params[2]]);
        } else {
            $updateMainNameResult = $db->query("UPDATE
                                    People
                                    SET
                                    firstName = $1,
                                    lastName = $2,
                                    middleInit = $3
                                    WHERE 
                                    peopleID = $4;", [$ref_firstname, $ref_lastname, NULL, $ref_id]);

            $updateMainInfoResult = $db->query("UPDATE 
                                    ContactAgencyMembers
                                    SET
                                    agency = $1,
                                    phone = $2,
                                    email = $3
                                    WHERE 
                                    contactAgencyID = $4;", [$ref_party, $ref_phone, $ref_email, $ref_id]);
        }

        // For loop based on how many parties were inputted into a particular form.
        for ($i = 1; $i <= 5; $i++) {

            // Create variable names for names of fields (JavaScript ids)
            $prt_type = "party_type_" . $i;
            $prt_first_name = "party_firstname_" . $i;
            $prt_last_name = "party_lastname_" . $i;
            $prt_phone = "party_phone_" . $i;
            $prt_email = "party_email_" . $i;

            if ($i <= $party_count) {
                $row = pg_fetch_assoc($parties_all_edit, $i - 1);
                $prt_id = $row['peopleid'];

                $updateFamNameResult = $db->query("UPDATE
                                    People
                                    SET
                                    firstName = $1,
                                    lastName = $2,
                                    middleInit = $3
                                    WHERE 
                                    peopleID = $4;", [$$prt_first_name, $$prt_last_name, NULL, $prt_id]);

                $updatePartyInfoResult = $db->query("UPDATE 
                                    ContactAgencyMembers
                                    SET
                                    agency = $1,
                                    phone = $2,
                                    email = $3
                                    WHERE 
                                    contactAgencyID = $4;", [$$prt_type, $$prt_phone, $$prt_email, $prt_id]);

            } else {
                if ($$prt_first_name !== NULL && $$prt_last_name !== NULL) {
                    $pidParty = $db->query("SELECT PeopleInsert(
                                           fName := $1::TEXT,
                                           lName := $2::TEXT,
                                           mInit := $3::VARCHAR
                                           );", [$$prt_first_name, $$prt_last_name, NULL]);
                    $pidParty = pg_fetch_result($pidParty, 0);

                    $formatted_phone = phoneStrToNum($$prt_phone);

                    $partyResult = $db->query("SELECT agencyMemberInsert(
                                agencyMemberID := $1::INT,
                                agen := $2::referraltype,
                                phn := $3::TEXT,
                                em := $4::text,
                                isMain := $5::boolean,
                                arID := $6::int
                                );", [$pidParty, $$prt_type, $formatted_phone, $$prt_email, 0, $params[2]]);
                }
            }
        }

        header('Location: /ps-view-participant/'.$params[1]);
        die();

    } else {
        /*                 ---------------------
                           | STORED PROCEDURES |
                           ---------------------                  */


        /*                     - Main Procedures -
         Participant PeopleInsert and main addAgencyReferral stored procedures
         Primary and Secondary phone inserts into phone table              */

        $pIDResult = checkForDuplicates($db,$pers_firstname, $pers_lastname,  $pers_middlein );


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
                      eID := $29::INTEGER)', array(
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

        $updateRefDate = $db->query("UPDATE 
                                    Forms
                                    SET
                                    employeeSignedDate = $1
                                    WHERE 
                                    formID = $2;", [$ref_date, $formID]);

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
        if ($pers_primphone !== NULL) {
            $phoneResults = $db->query("INSERT INTO FormPhoneNumbers(
                                                formID,
                                                phoneNumber,
                                                phoneType) VALUES ($1, $2, $3);",
                                                [$formID, $pers_primphone, "Primary"]);
        }

        // Insert secondary phone number
        if ($pers_secphone !== NULL) {
            $secPhoneResults = $db->query("INSERT INTO FormPhoneNumbers(
                                                  formID,
                                                  phoneNumber,
                                                  phoneType) VALUES ($1, $2, $3);",
                                                  [$formID, $pers_secphone, "Secondary"]);
        }
        /*              - End Main Procedures -             */


        /*              - Agency Stored Procedures -
        Main agency contact member is run through PeopleInsert and then
        agencyMemberInsert with type as main. Other Agencies are run
        through a for loop the same way and added in only if the main
        agency is set.
                                                                        */

        // Insert Main agency contact
        if ($ref_firstname !== NULL && $ref_lastname !== NULL) {
            $pIDMainAgency = $db->query("SELECT PeopleInsert(
                                               fName := $1::TEXT,
                                               lName := $2::TEXT,
                                               mInit := $3::VARCHAR
                                               );", [$ref_firstname, $ref_lastname, NULL]);
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
                                );", [$pIDMainAgency, $ref_party, $ref_phone, $ref_email, TRUE, $formID]);

            // ADDITIONAL REFERRING AGENCIES

            for ($i = 1; $i <= 5; $i++) {
                $prt_type = "party_type_" . $i;
                $prt_first_name = "party_firstname_" . $i;
                $prt_last_name = "party_lastname_" . $i;
                $prt_phone = "party_phone_" . $i;
                $prt_email = "party_email_" . $i;


                if ($$prt_first_name !== NULL && $$prt_last_name !== NULL) {
                    $pidParty = $db->query("SELECT PeopleInsert(
                                           fName := $1::TEXT,
                                           lName := $2::TEXT,
                                           mInit := $3::VARCHAR
                                           );", [$$prt_first_name, $$prt_last_name, NULL]);
                    $pidParty = pg_fetch_result($pidParty, 0);

                    $formatted_phone = phoneStrToNum($$prt_phone);

                    $partyResult = $db->query("SELECT agencyMemberInsert(
                                agencyMemberID := $1::INT,
                                agen := $2::referraltype,
                                phn := $3::TEXT,
                                em := $4::text,
                                isMain := $5::boolean,
                                arID := $6::int
                                );", [$pidParty, $$prt_type, $formatted_phone, $$prt_email, 0, $formID]);
                }
            }
        }
        /*            - End Agency Stored Procedures -              */


        /*          - Household Members Stored Procedure -
        Uses a for loop to iterate through every instance of family_
        variables, checks to see whether it should run through based
        off of if the first or last name is set
                                                                        */
        for ($i = 1; $i <= 10; $i++) {
            // Create variable names
            $fam_first_name = "family_first_name_" . $i;
            $fam_last_name = "family_last_name_" . $i;
            $fam_mi = "family_mi_" . $i;
            $fam_relationship = "family_relationship_" . $i;
            $fam_dob = "family_dob_" . $i;
            $fam_race = "family_race_" . $i;
            $fam_sex = "family_sex_" . $i;

            // Check to see if the household member is a child
            if ($$fam_relationship === "Son" || $$fam_relationship === "Daughter")
                $isChild = 1;
            else
                $isChild = 0;

            // Run InsertPeople for current household member
            if ($$fam_first_name !== NULL && $$fam_last_name !== NULL) {
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
                    echo '<form id="participant_info" action="/referral-form/'.$params[0].'/'.$params[1].'/'.$params[2].'" method="post" novalidate>';
                else
                    echo '<form id="participant_info" action="/referral-form" method="post" novalidate>';
                ?>
                    <div id="accordion" role="tablist" aria-multiselectable="true">
                        <br>
                        <!-- first collapsable -->
                        <div class="card">
                            <div class="card-header" role="tab">
                                <h5 class="card-title" style="font-weight: normal;">
                                    <a data-toggle="collapse" class="form-header" data-parent="#accordion" onfocusin="section1()" id="pers_title" href="#collapse1">Participant Information</a>
                                </h5>
                            </div>

                            <div id="collapse1" class="collapse show" role="tabpanel">
                                <div class="card-body">
                                    <!-- Participant info form -->

                                    <h5>Personal Information</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="pers_firstname">Participant Name:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" id="pers_firstname" name="pers_firstname"
                                                   value="<?= isset($pers_firstname_result) ? $pers_firstname_result : "" ?>" placeholder="First name" required>
                                            <div class="invalid-feedback">Enter first name</div>
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="pers_lastname">Last Name:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" id="pers_lastname" name="pers_lastname"
                                                   value="<?= isset($pers_lastname_result) ? $pers_lastname_result : "" ?>" placeholder="Last name" required>
                                            <div class="invalid-feedback">Enter last name</div>
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="pers_middlein">MInitial:</label>
                                        <div class="col-sm-1 col">
                                            <input type="text" class="form-control" id="pers_middlein" name="pers_middlein"
                                                   value="<?= isset($pers_middlein_result) ? $pers_middlein_result : "" ?>" placeholder="Initial" maxlength="1">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="pers_dob">Date of Birth:</label>
                                        <div class="col-sm-2 col">
                                            <input type="date" class="form-control" name="pers_dob" id="pers_dob"
                                                   value="<?= isset($pers_dob_result) ? $pers_dob_result : "" ?>" placeholder="Enter DOB">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Race:</label>
                                        <div class="col-sm-2 col">
                                            <select class="form-control" name="pers_race" id="pers_race">
                                                <option value="" selected="selected">Choose one</option>
                                                <?php
                                                $res = $db->query("SELECT unnest(enum_range(NULL::race)) AS type", []);
                                                while ($enumtype = pg_fetch_assoc($res)) {
                                                    $t = $enumtype ['type'];
                                                    ?>
                                                    <option value="<?= $t ?>" <?php echo (isset($pers_race_result) && $pers_race_result == $t) ? "selected" : "" ?>><?= $t ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Sex:</label>
                                        <div class="col-sm-2 col">
                                            <select class="form-control" name="pers_sex" id="pers_sex">
                                                <option value="" selected="selected">Choose one</option>
                                                <?php
                                                $res = $db->query("SELECT unnest(enum_range(NULL::sex)) AS type", []);
                                                while ($enumtype = pg_fetch_assoc($res)) {
                                                    $t = $enumtype ['type'];
                                                    ?>
                                                    <option value="<?= $t ?>" <?php echo (isset($pers_sex_result) && $pers_sex_result == $t) ? "selected" : "" ?>><?= $t ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <h5>Contact Information</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 col-2" for="pers_address">Street Address:</label>
                                        <div class="col-sm-3 col">
                                            <input type="text" class="form-control" name="pers_address" id="pers_address"
                                                   value="<?php if(isset($pers_street_num_result) && isset($pers_street_name_result))
                                                                    echo $pers_street_num_result . " " . $pers_street_name_result;
                                                                else if (!isset($pers_street_num_result) && isset($pers_street_name_result))
                                                                    echo $pers_street_name_result; ?>" placeholder="Street address">
                                        </div>
                                        <label class="col-form-label col-sm-1 col-2" for="pers_apt_info">Apartment:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" name="pers_apt_info" id="pers_apt_info"
                                                   value="<?= isset($pers_apt_info_result) ? $pers_apt_info_result : "" ?>" placeholder="Apartment Information">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 col-2" for="pers_state">State:</label>
                                        <div class="col-sm-3 col">
                                            <select class="form-control" name="pers_state" id="pers_state" >
                                                <option value="" selected="selected">Choose a state</option>
                                                <?php
                                                $res = $db->query("SELECT unnest(enum_range(NULL::states)) AS type", []);
                                                while ($enumtype = pg_fetch_assoc($res)) {
                                                    $t = $enumtype ['type'];
                                                    ?>
                                                    <option value="<?= $t ?>" <?php echo (isset($pers_state_result) && $pers_state_result == $t) ? "selected" : "" ?>><?= $t ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <label class="col-form-label col-sm-1 col-2" for="pers_city">City:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" name="pers_city" id="pers_city"
                                                   value="<?= isset($pers_city_result) ? $pers_city_result : "" ?>" placeholder="City">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2 col-2" for="pers_zip">ZIP:</label>
                                        <div class="col-sm-1 col">
                                            <input type="text" class="form-control mask-zip" name="pers_zip" id="pers_zip"
                                                   value="<?= isset($pers_zip_result) ? $pers_zip_result : "" ?>" placeholder="Zip">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="pers_primphone">Primary Phone:</label>
                                        <div class="col-sm-2 col">
                                            <input type="tel" class="form-control mask-phone feedback-icon" name="pers_primphone" id="pers_primphone"
                                                   value="<?= isset($pers_primphone_result) ? $pers_primphone_result : "" ?>" placeholder="(999) 999-9999">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="pers_secphone">Secondary Phone:</label>
                                        <div class="col-sm-2 col">
                                            <input type="tel" class="form-control mask-phone" name="pers_secphone" id="pers_secphone"
                                                   value="<?= isset($pers_secphone_result) ? $pers_secphone_result : "" ?>" placeholder="(999) 999-9999">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="comment">Reason for Referral:</label>
                                        <div class="col-sm-3 col">
                                            <textarea style="resize: none;" class="form-control" rows=4 name="pers_reason" id="pers_reason" placeholder="Reason for referral"><?= isset($pers_reason_result) ? $pers_reason_result : "" ?></textarea>
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
                                        <div class="col-sm-2 col">
                                            <select class="form-control" name="ref_party" id="ref_party" placeholder="Enter party">
                                                <option value="" selected="selected">Choose a party</option>
                                                <?php
                                                $res = $db->query("SELECT unnest(enum_range(NULL::referraltype)) AS type", []);
                                                while ($enumtype = pg_fetch_assoc($res)) {
                                                    $t = $enumtype ['type'];
                                                    ?>
                                                    <option value="<?= $t ?>" <?php echo (isset($ref_party_result) && $ref_party_result == $t) ? "selected" : "" ?>><?= $t ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <label class="col-form-label col-sm-2" for="ref_date">Date of Referral:</label>
                                        <div class="col-sm-2 col">
                                            <input type="date" class="form-control" name="ref_date" id="ref_date" value="<?= isset($ref_date_result) ? $ref_date_result : "" ?>">
                                        </div>
                                    </div>
                                    <br>
                                    <h5>Referring Party Contact Information</h5>
                                    <br>
                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="ref_firstname">Referring Party Name:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" name="ref_firstname" id="ref_firstname"
                                                   value="<?= isset($ref_firstname_result) ? $ref_firstname_result : "" ?>" placeholder="First Name">
                                        </div>

                                        <label class="col-form-label col-sm-0 sr-only" for="ref_lastname">Last Name:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control" name="ref_lastname" id="ref_lastname"
                                                   value="<?= isset($ref_lastname_result) ? $ref_lastname_result : "" ?>" placeholder="Last Name">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="ref_phone">Phone Number:</label>
                                        <div class="col-sm-2 col">
                                            <input type="text" class="form-control mask-phone" name="ref_phone" id="ref_phone"
                                                   value="<?= isset($ref_phone_result) ? $ref_phone_result : "" ?>" placeholder="(999) 999-9999">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-sm-2" for="ref_email">Email:</label>
                                        <div class="col-sm-2 col">
                                            <input type="email" class="form-control" name="ref_email" id="ref_email"
                                                   value="<?= isset($ref_email_result) ? $ref_email_result : "" ?>" placeholder="cpca@cpca.com">
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
                                    <a data-toggle="collapse" class="form-header" onfocusin="section3()" id="household_Member_Info" data-parent="#accordion" href="#collapse3">Participant Household Information</a>
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
                                            <div class="col-sm-2 col">
                                                <input type="text" class="form-control input_fn" id="family_first_name_1" name="family_first_name_1" placeholder="First name">
                                            </div>

                                            <label class="col-sm-0 col-form-label sr-only label_ln">Last Name:</label>
                                            <div class="col-sm-2 col">
                                                <input type="text" class="form-control input_ln" id="family_last_name_1" name="family_last_name_1" placeholder="Last name">
                                            </div>

                                            <label class="col-sm-0 col-form-label sr-only label_mi">Middle Initial:</label>
                                            <div class="col-sm-1 col">
                                                <input type="text" class="form-control input_mi" id="family_mi_1" name="family_mi_1" maxlength="1" placeholder="Initial">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label label_dob" for="family_dob_1">Date of Birth:</label>
                                            <div class="col-sm-2 col">
                                                <input type="date" class="form-control input_dob" name="family_dob_1" id="family_dob_1" maxlength="255">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label label_sex">Sex:</label>
                                            <div class="col-sm-2 col">
                                                <select class="form-control select_sex" name="family_sex_1" id="family_sex_1">
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
                                            <label class="col-sm-2 col-form-label label_race">Race:</label>
                                            <div class="col-sm-2 col">
                                                <select class="form-control select_race" name="family_race_1" id="family_race_1">
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
                                            <label class="col-sm-2 col-form-label label_relationship col-3">Relationship:</label>
                                            <div class="col-sm-2 col">
                                                <select class="form-control select_relationship" name="family_relationship_1" id="family_relationship_1">
                                                    <option value="" selected="selected">Choose one</option>
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

                                    <div class="form-group agencybutton row controls">
                                        <label class="col-sm-2 col-form-label">Add Member:</label>
                                        <div class="col-sm-1">
                                            <button class="btn btn-default" type="button" id="btnAddMember"><span class="fa fa-plus"></span></button> <!-- every other dropdown on this form uses down arrows -->
                                        </div>

                                    </div>

                                    <div class="form-group agencybutton rembtn row controls">
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
                                    <a data-toggle="collapse" data-parent="#accordion" onfocusin="section4()" id="additional_participant_info" class="form-header" href="#collapse4">Additional Information</a>
                                </h5>
                            </div>

                            <div id="collapse4" class="collapse">
                                <div class="card-body">
                                    <h5>Additional Participant Information</h5>
                                    <span>Please check all that apply to the participant:</span>
                                    <br><br>
                                    <div style="padding-left:100px;">

                                        <!-- Begin Row 1 -->
                                        <div class="form-check">
                                            <label class="custom-control custom-checkbox mr-0 pl-3 col-sm-5">
                                                <input class="custom-control-input" id="chkSpecialEd" name="chkSpecialEd" type="checkbox"
                                                    <?php echo (isset($chkSpecialEd_result) && $chkSpecialEd_result == 't') ? "checked" : "" ?> >
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description" style="margin-left:5px">
                                                    Special Education/IEP/Resource Services
                                                </span>
                                            </label>

                                            <label class="custom-control custom-checkbox mr-0 pl-3 col-sm-6">
                                                <input class="custom-control-input" id="chkCPS" name="chkCPS" type="checkbox"
                                                    <?php echo (isset($chkCPS_result) && $chkCPS_result == 't') ? "checked" : "" ?> >
                                                <span class='custom-control-indicator'></span>
                                                <span class="custom-control-description" style="margin-left:5px">
                                                    Involved with CPS/Foster Care/Preventive Services
                                                </span>
                                            </label>
                                        </div>
                                        <!-- End Row 1 -->

                                        <!-- Begin Row 2 -->
                                        <div class="form-check">
                                            <label class="custom-control custom-checkbox mr-0 pl-3 col-sm-5">
                                                <input class="custom-control-input" id="chkSubAbuse" name="chkSubAbuse" type="checkbox"
                                                    <?php echo (isset($chkSubAbuse_result) && $chkSubAbuse_result == 't') ? "checked" : "" ?> >
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description" style="margin-left:5px">
                                                    Substance Use/Abuse History
                                                </span>
                                            </label>
                                            <label class="custom-control custom-checkbox mr-0 pl-3 col-sm-6">
                                                <input class="custom-control-input" id="chkMental" name="chkMental" type="checkbox"
                                                    <?php echo (isset($chkMental_result) && $chkMental_result == 't') ? "checked" : "" ?> >
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description" style="margin-left:5px">
                                                    Mental Health/Dual Diagnosis
                                                </span>
                                            </label>
                                        </div>
                                        <!-- End Row 2 -->

                                        <!-- Begin Row 3 -->
                                        <div class="form-check">
                                            <label class="custom-control custom-checkbox mr-0 pl-3 col-sm-5">
                                                <input class="custom-control-input" id="chkPreg" name="chkPreg" type="checkbox"
                                                    <?php echo (isset($chkPreg_result) && $chkPreg_result == 't') ? "checked" : "" ?> >
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description" style="margin-left:5px">
                                                    Pregnant
                                                </span>
                                            </label>
                                            <label class="custom-control custom-checkbox mr-0 pl-3 col-sm-6">
                                                <input class="custom-control-input" id="chkIQ" name="chkIQ" type="checkbox"
                                                    <?php echo (isset($chkIQ_result) && $chkIQ_result == 't') ? "checked" : "" ?> >
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description" style="margin-left:5px">
                                                    IQ Documentation
                                                </span>
                                            </label>
                                        </div>
                                        <!-- End Row 3 -->

                                        <!-- Begin Row 4 -->
                                        <div class="form-check">
                                            <label class="custom-control custom-checkbox mr-0 pl-3 col-sm-5">
                                                <input class="custom-control-input" id="chkViolence" name="chkViolence" type="checkbox"
                                                    <?php echo (isset($chkViolence_result) && $chkViolence_result == 't') ? "checked" : "" ?> >
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description" style="margin-left:5px">
                                                    Domestic Violence History
                                                </span>
                                            </label>
                                            <label class="custom-control custom-checkbox mr-0 pl-3 col-sm-6">
                                                <input class="custom-control-input" id="chkReside" name="chkReside" type="checkbox"
                                                    <?php echo (isset($chkReside_result) && $chkReside_result == 't') ? "checked" : "" ?> >
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description" style="margin-left:5px">
                                                    Child/Children do not reside with Referred Individual
                                                </span>
                                            </label>
                                        </div>
                                        <!-- End Row 4 -->

                                        <!-- Begin Row 5 -->
                                        <div class="form-check">
                                            <label class="custom-control custom-checkbox mr-0 pl-3 col-sm-5">
                                                <input class="custom-control-input" id="chkSigned" name="chkSigned" type="checkbox"
                                                    <?php echo (isset($chkSigned_result) && $chkSigned_result == 't') ? "checked" : "" ?> >
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description" style="margin-left:5px">
                                                    Signed consent form for release of information
                                                </span>
                                            </label>
                                        </div>
                                        <!-- End Row 5 -->
                                    </div>

                                    <br>

                                    <div class="form-group">
										<div class="row">
											<label class="col-form-label col-sm-2 col-3" for="comments">Additional Information:</label>
											<div class="col-sm-3 col">
												<textarea style="resize: none;" class="form-control" rows=5 name="additional_info" id="additional_info" placeholder="Enter any additional information"><?php if(isset($additional_info_result)) echo $additional_info_result ?></textarea>
											</div>
										</div>
                                    </div>

                                    <div id="partyEntry_1" class="clonedParty">
                                        <h5 class="heading-reference">Additional Parties Involved</h5>
                                        <br>
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-2 label_type" for="ag_name">Party Type:</label>
                                            <div class="col-sm-2 col">
                                                <select class="form-control select_type" name="party_type_1" id="party_type_1" placeholder="Enter party">
                                                    <option value="" selected="selected">Choose a party</option>
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
                                            <div class="col-sm-2 col">
                                                <input type="text" class="form-control input_fn" name="party_firstname_1" id="party_firstname_1" placeholder="First Name">
                                            </div>

                                            <label class="col-form-label col-sm-0 sr-only label_ln" for="party_lastname_1">Last Name:</label>
                                            <div class="col-sm-2 col">
                                                <input type="text" class="form-control input_ln" name="party_lastname_1" id="party_lastname_1" placeholder="Last Name">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-2 label_phone" for="party_phone_1">Contact Phone:</label>
                                            <div class="col-sm-2 col">
                                                <input type="text" class="form-control mask-phone input_phone" name="party_phone_1" id="party_phone_1" placeholder="(999) 999-9999">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-2 label_email" for="party_email_1">Contact Email:</label>
                                            <div class="col-sm-2 col">
                                                <input type="email" class="form-control input_email" name="party_email_1" id="party_email_1" placeholder="cpca@cpca.com">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group agencybutton row controls">
                                        <label class="col-sm-2 col-form-label">Add Another Party:</label>
                                        <div class="col-sm-1 col">
                                            <button class="btn btn-default" type="button" id="btnAddParty"><span class="fa fa-plus"></span></button>
                                        </div>
                                    </div>
                                    <div class="form-group agencybutton rembtn row controls">
                                        <label class="col-sm-2 col-form-label">Remove Party:</label>
                                        <div class="col-sm-1 col">
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
                                <a data-toggle="collapse" data-parent="#accordion" onfocusin="section5()" id="office_info" class="form-header" href="#collapse5">Office Information</a>
                            </h5>
                        </div>

                        <div id="collapse5" class="collapse">
                            <div class="card-body">
                                <h5>For Office Use Only</h5>
                                <br>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2 col-3" for="office_contact_date">Date of First Contact:</label>
                                    <div class="col-sm-2 col">
                                        <input type="date" class="form-control" name="office_contact_date" id="office_contact_date"
                                               value="<?= isset($office_contact_date_result) ? $office_contact_date_result : "" ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2 col-3" for="office_means">Means of Contact:</label>
                                    <div class="col-sm-2 col">
                                        <input type="text" class="form-control" name="office_means" id="office_means"
                                               value="<?= isset($office_means_result) ? $office_means_result : "" ?>" placeholder="Email, Phone, etc...">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2 col-3" for="office_initial_date">Initial Meeting Info:</label>
                                    <div class="col-sm-2 col">
                                        <input type="date" class="form-control" name="office_initial_date" id="office_initial_date"
                                               value="<?= isset($office_initial_date_result) ? $office_initial_date_result : "" ?>">
                                    </div>

                                    <label class="col-form-label col-sm-0 sr-only" for="office_location">Location:</label>
                                    <div class="col-sm-2 col">
                                        <input type="text" class="form-control" name="office_location" id="office_location"
                                               value="<?= isset($office_location_result) ? $office_location_result : "" ?>" placeholder="Location">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-2 col-3" for="comments">Comments:</label>
                                    <div class="col-sm-3 col">
                                        <textarea style="resize: none;" class="form-control" rows=5 name="comments" id="comments" placeholder="Enter any comments here"><?= isset($comments_result) ? $comments_result : "" ?></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>  <!-- panel group end -->

            <?php
            if (isset($params[0]) && isset($params[1]) && isset($params[2])) {
                // Third Card (Participant Household Information)
                // Counts how many household members are associated with a particular form.
                $household_edit = $db->query("SELECT COUNT (familymembersid)
                                                     FROM family
                                                     WHERE family.formID = $1;", [$referral_formID]);
                $household_count = pg_fetch_result($household_edit, 0);

                $household_all_edit = $db->query("SELECT * FROM familymembers
                                                         INNER JOIN People ON people.peopleid = familymembers.familymemberid
                                                         INNER JOIN family ON family.familymembersid = familymembers.familymemberid
                                                         WHERE family.formID = $1;", [$referral_formID]);

                // For loop based on how many household members were inputted into a particular form.
                for ($i = 1; $i <= $household_count; $i++) {

                // Create variable names for names of fields (JavaScript ids)
                $fam_first_name = "family_first_name_" . $i;
                $fam_last_name = "family_last_name_" . $i;
                $fam_mi = "family_mi_" . $i;
                $fam_relationship = "family_relationship_" . $i;
                $fam_dob = "family_dob_" . $i;
                $fam_race = "family_race_" . $i;
                $fam_sex = "family_sex_" . $i;

                // Get each row based on how many rows are returned.
                $row = pg_fetch_assoc($household_all_edit, $i - 1);
                // Create double variables for results.
                $$fam_first_name = $row['firstname'];
                $$fam_last_name = $row['lastname'];
                $$fam_mi = $row['middleinit'];
                $$fam_relationship = $row['relationship'];
                $$fam_dob = $row['dateofbirth'];
                $$fam_race = $row['race'];
                $$fam_sex = $row['sex'];
                ?>
                <script type="text/javascript">
                    // Insert results into fields.
                    $('#family_first_name_<?= $i ?>').val('<?= $$fam_first_name ?>');
                    $('#family_last_name_<?= $i ?>').val('<?= $$fam_last_name ?>');
                    $('#family_mi_<?= $i ?>').val('<?= $$fam_mi ?>');
                    $('#family_relationship_<?= $i ?>').val('<?= $$fam_relationship ?>');
                    $('#family_dob_<?= $i ?>').val('<?= $$fam_dob ?>');
                    $('#family_race_<?= $i ?>').val('<?= $$fam_race ?>');
                    $('#family_sex_<?= $i ?>').val('<?= $$fam_sex ?>');
                </script>

            <?php

                if ($i < $household_count)
                    // Open up the appropriate amount of fields (add party is called).
                    echo '<script>addHousehold();</script>';
            }

            // Fourth Card (Additional Information)
            // Counts how many parties are associated with a particular form.
            $party_edit = $db->query("SELECT COUNT (contactagencyid) 
                                             FROM contactagencyassociatedwithreferred
                                             WHERE agencyreferralid = $1
                                             AND isMainContact != TRUE;", [$referral_formID]);
            $party_count = pg_fetch_result($party_edit, 0);

            $parties_all_edit = $db->query("SELECT peopleid, agency, phone, email, firstname, lastname, ismaincontact FROM contactagencymembers
                                                   INNER JOIN People ON people.peopleid = contactagencymembers.contactagencyID
                                                   INNER JOIN contactagencyassociatedwithreferred ON contactagencyassociatedwithreferred.contactagencyid = contactagencymembers.contactagencyid
                                                   WHERE agencyreferralid = $1
                                                   AND isMainContact != TRUE;", [$referral_formID]);

            // For loop based on how many parties were inputted into a particular form.
            for ($i = 1; $i <= $party_count; $i++) {

            // Create variable names for names of fields (JavaScript ids)
            $prt_type = "party_type_" . $i;
            $prt_first_name = "party_firstname_" . $i;
            $prt_last_name = "party_lastname_" . $i;
            $prt_phone = "party_phone_" . $i;
            $prt_email = "party_email_" . $i;

            // Get each row based on how many rows are returned.
            $row = pg_fetch_assoc($parties_all_edit, $i - 1);
            // Create double variables for results.
            $$prt_type = $row['agency'];
            $$prt_first_name = $row['firstname'];
            $$prt_last_name = $row['lastname'];
            $$prt_phone = $row['phone'];
            $$prt_email = $row['email'];
            ?>
                    <script type="text/javascript">
                        // Insert results into fields.
                        $('#party_type_<?= $i ?>').val('<?= $$prt_type ?>');
                        $('#party_firstname_<?= $i ?>').val('<?= $$prt_first_name ?>');
                        $('#party_lastname_<?= $i ?>').val('<?= $$prt_last_name ?>');
                        $('#party_phone_<?= $i ?>').val('<?= $$prt_phone ?>');
                        $('#party_email_<?= $i ?>').val('<?= $$prt_email ?>');
                    </script>

                    <?php
                    if ($i < $party_count)
                        // Open up the appropriate amount of fields (add party is called).
                        echo '<script>addParty();</script>';
                }


            }

            if(isset($params[0]) && $params[0] == "edit") {
                echo '<button id="btnRegister" onclick="submitAll()" class="cpca btn">Update</button>';
            } else if (isset($params[0]) && $params[0] == "view"){
                echo '<a href="/ps-view-participant/'.$params[1].'"><button id="btnView" class="cpca btn">Back To Participant</button></a>';
            } else {
                include('form_duplicate_check.php');
            }


            if(isset($params[0]) && $params[0] == "view") {
                echo '<script type="text/javascript">',
                     'disableReferralFields();',
                     '</script>';
            } else if(isset($params[0]) && $params[0] == "edit") {
                echo '<script type="text/javascript">',
                'referralEditUpdates();',
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
<?php include('footer.php'); ?>