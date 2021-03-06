/**
 * PEP Capping 2017 Algozzine's Class
 *
 * helper functions to add people to the table
 *
 * validation and success/error message function creation
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version 1.1
 * @since 0.7
 */


/**
 * validates the fields in the new person field before they are added to the table
 *
 * @returns {boolean}
 */
function jsValidateTable() {
    var firstName, middleInitial, lastName, race, sex, age, numChildren, zip;

    //grab person's information from fields
    firstName = document.getElementById("new-person-first").value;
    middleInitial = document.getElementById("new-person-middle").value;
    lastName = document.getElementById("new-person-last").value;
    race = document.getElementById("race-select").value;
    sex = document.getElementById("sex-select").value;
    age = document.getElementById("age-input").value;
    numChildren = document.getElementById("num-children-input").value;
    zip = document.getElementById("zip-input").value;

    //javascript validation (will do php validation later, too)
    var valid = true;
    var errorMessage = null;

    //string (if failed) or bool(true if succeeded)
    var validateResult = validateFields(firstName, middleInitial, lastName, race, sex, age, numChildren, zip);
    //failed validation?
    if(validateResult !== true){
        valid = false;
        errorMessage = validateResult;
    }

    //success or failure message
    var insertAlertHere = document.getElementById("alert-box");

    while(insertAlertHere.hasChildNodes()) { //remove all children
        insertAlertHere.removeChild(insertAlertHere.lastChild);
    }
    insertAlertHere.appendChild(createMessage(valid, errorMessage));

    return valid;

}

/**
 * creates an element that is used in feedback to the user
 * on whether or not adding a person was successful and what went right/wrong
 *
 * @param success{boolean}
 * @param errorMessage{string}
 * @returns {Element}
 *
 */
function createMessage(success, errorMessage) {
    var div = document.createElement("div");
    div.setAttribute("role", "alert");

    if(!success){
        div.setAttribute("class", "alert alert-warning");
        div.innerHTML = "<strong>Oops! </strong>" + errorMessage;
    }

    return div;
}

/**
 *
 * @param first{string}
 * @param middle{string}
 * @param last{string}
 * @param race{string}
 * @param sex{string}
 * @param age{int}
 * @param numChildren{int}
 * @param zip{string}
 * @returns {*} - errorMessage for createMessage (if any) - string, or the value true (if successful)
 */
function validateFields(first, middle, last, race, sex, age, numChildren, zip){
    if(!validateName(first)){
        return "First name may only contain letters. Spaces, numbers, and other characters are not allowed.";
    }
    if(!validateMiddle(middle)){
        return "Middle initial may only contain one letter or nothing."
    }
    if(!validateName(last)){
        return "Last name may only contain letters. Spaces, numbers, and other characters are not allowed.";
    }
    if(!validateRace(race)){
        return "Please select a race from the drop-down."
    }
    if(!validateSex(sex)){
        return "Please select a sex from the drop-down."
    }
    if(!validateAge(age)){
        return "Please enter a valid age."
    }
    if(!validateNumChildren(numChildren)){
        return "Please enter a valid number of children"
    }
    if(!validateZip(zip)){
        return "Please enter a valid number zip";
    }
    //success
    return true;
}

/**
 * @param name{string} - first or last
 * @returns {boolean}
 */
function validateName(name) {
    //returns true if matched, validates for a-z and A-Z
    return (/^[A-Za-z']+$/.test(name));
}

/**
 * @param middle{string}
 * @returns {boolean}
 */
function validateMiddle(middle) {
    if(middle === '') return true; //empty
    //returns true if matched, validates for a-z and A-Z max one character
    return (/^[A-Za-z]$/.test(middle));
}

/**
 * @param race{string}
 * @returns {boolean}
 */
function validateRace(race) {
    //returns true if not the default option
    return(race !== "Select Race...");
}

/**
 * @param sex{string}
 * @returns {boolean}
 */
function validateSex(sex) {
    //returns true if not the default option
    return(sex !== "Select Sex...");
}

/**
 * @param age{int}
 * @returns {boolean}
 */
function validateAge(age) {
    var ageNumber = parseInt(age);
    if(isNaN(ageNumber)) return false;
    if(typeof(ageNumber) !== "number") return false;
    return (ageNumber >= 1);
}

/**
 * @param num{int}
 * @returns {boolean}
 */
function validateNumChildren(num) {
    var number = parseInt(num);
    if(isNaN(number)) return false;
    //returns true if a valid number of children
    return (number >= 0);
}

/**
 * @param zip{string}
 * @returns {boolean}
 */
function validateZip(zip) {
    //validate zip code (from stackoverflow)
    return (/(^\d{5}$)/.test(zip));
}