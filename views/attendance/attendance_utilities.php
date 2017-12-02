<?php

/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Helper functions
 *
 * Validation and misc functions used by one or more files
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version 1.1
 * @since 0.7
 */

/**
 * some curricula and other parts of the have apostrophe's
 * in their name that need to be escaped when using it for a query
 *
 * @param $input{string}
 * @return mixed
 *
 */
function escape_apostrophe($input){
    return str_replace("'","''", $input);
}

/**
 * formatted date for sql subtracted day from now
 *
 * @param $sub_this_time{string} - a specified time in the formats ('x days', or 'y years')
 * @return string
 *
 */
function date_subtraction($sub_this_time){
    //date subtraction for view
    $today = new DateTime('now');
    $sub_date = $today->sub(DateInterval::createFromDateString($sub_this_time));
    return $sub_date->format('Y-m-d');
}

/**
 * age of person born on that date
 *
 * @param $raw_sqlDate{string} - raw sql date right
 * from the DB in mm-dd-yyyy format
 *
 * @return false|int|mixed|string
 *
 */
function calculate_age($raw_sqlDate) {
    if(!empty($raw_sqlDate)){
        //stack overflow next level stuff
        //explode the date to get month, day and year
        $birthDate = explode("-", $raw_sqlDate);
        //reformat to fit the mm-dd-yyyy format
        $birthDateFormatted = array($birthDate[1], $birthDate[2], $birthDate[0]);
        //get age from date or birthdate
        $age = (date("md", date("U", mktime(0, 0, 0, $birthDateFormatted[0], $birthDateFormatted[1], $birthDateFormatted[2]))) > date("md")
            ? ((date("Y") - $birthDateFormatted[2]) - 1)
            : (date("Y") - $birthDateFormatted[2]));

        return $age;
    }

}

/**
 * @param $sqlDate{string} - raw sql timestamp
 * @return string - nicely formatted date and time
 */
function formatSQLDate($sqlDate) {
    //for some reason, if a timestamp is H:i:s.000000, fetching the row drops the microseconds.
    //  this is a workaround
    $sqlDateString = (string) $sqlDate;
    if(strpos($sqlDateString, '.') == false) {
        //period not found, must add
        $sqlDateString .= ".000000";
    }

    $convertDate = DateTime::createFromFormat('Y-m-d H:i:s.u', $sqlDateString);
    $formattedDate = $convertDate->format('l, F jS g:i A');

    return $formattedDate;
}

/**
 *
 * @param $sqlDate{string} - raw sql timestamp
 * @return string - nicely formatted short date and time
 *
 */
function formatSQLDateShort($sqlDate) {
    //for some reason, if a timestamp is H:i:s.000000, fetching the row drops the microseconds.
    //  this is a workaround
    $sqlDateString = (string) $sqlDate;
    if(strpos($sqlDateString, '.') == false) {
        //period not found, must add
        $sqlDateString .= ".000000";
    }

    $convertDate = DateTime::createFromFormat('Y-m-d H:i:s.u', $sqlDateString);
    $formattedDate = $convertDate->format('m/d/Y g:i A');

    return $formattedDate;
}

/**
 * create timestamp from specific date format
 *
 * @param $inputDate{string} - date in 'Y-m-d' and time in 'H:i'
 * @param $inputTime{string}
 * @return string - sql timestamp 'Y-m-d H:i:00.000000'
 *
 */
function makeTimestamp($inputDate, $inputTime){
    $convertDate = DateTime::createFromFormat('Y-m-d h:i A', (string)$inputDate . " " . $inputTime);
    //the one preserves the milliseconds for the function formatSQLDate(timestamp) to work properly
    $timestamp = $convertDate->format('Y-m-d H:i:00.000000');
    return $timestamp;
}

/**
 * serializes array of page information into one large
 * variable to be stored in the session
 *
 * @param $matrix{array}
 * @return string - encoded object
 *
 */
function serializeParticipantMatrix($matrix) {
    return base64_encode(serialize($matrix));
}

/**
 * deserializes large session variable
 *
 * @param $encodedMatrix{string} - encoded object
 * @return array
 *
 */
function deserializeParticipantMatrix($encodedMatrix) {
    return unserialize(base64_decode($encodedMatrix));
}

/**
 * @param $classInformation{array}
 * @return array
 */
//input: class information matrix
//output: updated class information matrix
function handleAttendanceSheetInfo($classInformation){
    //get the post information and match it with people in the roster
    for($i = 0; $i < count($classInformation); $i++){
        //set the important intake fields (present, comment)
        //each row's post name is made up index and field name
        $classInformation[$i]['present'] = isset($_POST[((string)$i . "-check")]);
        $classInformation[$i]['comments'] = isset($_POST[((string)$i . "-comment")]) ? ($_POST[((string)$i . "-comment")]) : null ;
    }

    return $classInformation;
}

/**
 * works on session variable of serializedClassInfo
 *
 * deserializes session info, calls function to update,
 * reserializes and sets session variable
 */
function updateSessionClassInformation(){
    //get serialized class information
    $serializedClassInfo = $_SESSION['serializedInfo'];
    //deserialize info
    $deserializeClassInfo = deserializeParticipantMatrix($serializedClassInfo);
    //update info with handle class attendance function
    $updatedInfo = handleAttendanceSheetInfo($deserializeClassInfo);
    //serialize this new information
    $updatedInfoSerialize = serializeParticipantMatrix($updatedInfo);
    //update the session info with this new value
    $_SESSION['serializedInfo'] = $updatedInfoSerialize;
}

//================================================================
//INPUT VALIDATION
//================================================================
//validation functions (occurs after JS validation so
// if they're not valid, it's malicious or they aren't running JS)

/**
 * @param $name{string} - first or last name
 * @return bool|int
 */
function validateName($name) {
    if(empty($name)){
        return false;
    } else{
        //returns true if matched, validates for a-z and A-Z
        return preg_match("/^[A-Za-z']+$/", $name);
    }
}

/**
 * @param $middle{string} - middle initial
 * @return bool|int
 */
function validateMiddle($middle) {
    if(empty($middle)){
        return true; //not required
    } else{
        //returns true if matched, validates for a-z and A-Z max one character
        return preg_match("/^[A-Za-z]$/", $middle);
    }
}

/**
 * @param $race{string} - race
 * @return bool
 */
function validateRace($race) {
    if(empty($race)){
        return false;
    } else{
        $raceArray = $_SESSION['races'];
        return in_array($race, $raceArray);
    }
}

/**
 * @param $sex{string} - sex
 * @return bool
 */
function validateSex($sex) {
    if(empty($sex)){
        return false;
    } else {
        $sexArray = $_SESSION['sexes'];
        return in_array($sex, $sexArray);
    }
}

/**
 * @param $age{int} - age
 * @return bool
 */
function validateAge($age) {
    if(empty($age)){
        return false;
    } else{
        //returns true if age positive
        return( is_numeric($age) && ($age >= 1));
    }
}

/**
 * @param $num{int} - number of children
 * @return bool
 */
function validateNumChildren($num) {
    if(empty($num) && ($num != 0)){
        return false;
    } else{
        //returns true if a valid number of children
        return(is_numeric($num) && ($num >= 0));
    }
}

/**
 * @param $zip{string} - zip code
 * @return bool|int
 */
function validateZip($zip) {
    if(empty($zip)){
        return false;
    } else {
        //validate zip code (from stackoverflow)
        return preg_match("/(^\d{5}$)/", $zip);
    }
}

/**
 * @param $results{resultSet} - all classes from query
 * @param $input{string} - class to check
 * @return bool
 */
function validateClass($results, $input){
    while($row = pg_fetch_assoc($results)){
        if($input == $row['classid']) return true;
    }
    return false;
}

/**
 * @param $results{resultSet} - all curricula from query
 * @param $input{string} - curriculum to check
 * @return bool
 */
function validateCurriculum($results, $input){
    while($row = pg_fetch_assoc($results)){
        if($input == $row['curriculumid']) return true;
    }
    return false;
}

/**
 * @param $date{string} - 'Y-m-d' (i.e. '2017-11-30')
 * @return bool
 */
function validateDate($date){
    $dateConversion = DateTime::createFromFormat('Y-m-d', $date);
    if ($dateConversion !== false) {
        //valid date
        return true;
    } else {
        //invalid date
        return false;
    }
}

/**
 * @param $time{string} - 'g:i A' (i.e. '5:00 AM')
 * @return bool
 */
function validateTime($time){
    $timeConversion = DateTime::createFromFormat('g:i A', $time);
    if ($timeConversion !== false) {
        //valid time
        return true;
    } else {
        //invalid time
        return false;
    }

}

/**
 * @param $results{resultSet} - all site names from query
 * @param $input{string} - site name to check
 * @return bool
 */
function validateSite($results, $input){
    while($row = pg_fetch_assoc($results)){
        if($input == $row['sitename']) return true;
    }
    return false;
}

/**
 * @param $results{resultSet} - all languages from query
 * @param $input{string} - language to check
 * @return bool
 */
function validateLanguage($results, $input){
    while($row = pg_fetch_assoc($results)){
        if($input == $row['lang']) return true;
    }
    return false;
}

/**
 * @param $results{resultSet} - all facilitators from query
 * @param $input{int} - facilitatorID to check
 * @return bool
 */
function validateFacilitator($results, $input){
    while($row = pg_fetch_assoc($results)){
        if($input == $row['peopleid']) return true;
    }
    return false;
}

/**
 * @param $conn{connection} - db connection
 * @param $string{string} - string to sanitize
 * @return string - SQL safe
 */
function sanitizeString($conn, $string){
    $string = trim($string);
    $string = pg_escape_string($conn, $string);
    return $string;
}