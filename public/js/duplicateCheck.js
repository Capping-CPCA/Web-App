/**
* PEP Capping 2017 Algozzine's Class
*
* Duplicate match search that looks for possible duplicates in the db based on user input into 
* intake, self-referral, or agency-referral packets.
*
* Ussage: When an employee fills out an intake/self-referral/agency-referral form they should not 
* be creating a new participant with each form. This function grabs the user supplied input from whichever
* form they are filling out, and populates it into a hidden form. That hidden form is then submitted through an
* ajax request which routes to a page with a sql query that looks for any possible matches in the db.
* If matches are found, the result is displayed to the user via a bootstrap modal. From there, the user
* Will be able to scroll through the possible matches and select the match they believe to be the participant 
* they are filling out information for. If there are no matches or if the user determines they want to create
* a new participant, the modal will close/not appear and submit the form as a new participant.
*
* @author Vallie Joseph
* @copyright 2017 Marist College
* @version 1.1
* @since 1.1
*
*
*/

$(document).ready(function(){

    var pathname = window.location.pathname; // Returns path only
    var firstNameValue;
    var middleinit ;
    var lastname;
    var pers_primphone ;
    var address ;
    var apt;
    var zip;
    var city ;
    var state ;
    var dob ;
    var race;
    var sex;
    var formData;

    function toTitleCase(str){
        return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
    }

    /**
    * Checking the page route to see what page the
    * user is on; this will tell the modal what js function
    * to run depending on the form that is being completed
    * @param pathN string that contains the path route
    */  
    function routePage(pathN){
        if(pathN == "/referral-form"){
            submitAll();
        } else if( pathN == "/self-referral-form"){
            submitAllSelf()
        } else if( pathN == "/intake-packet"){
            submitAllIntake();
        }
    }

    /**
    *  Depending on what form the user is filling out, the following function
    *  will populate the appropriate fields with the appropriate information. I
    *  If another form is added, another else if statement will need to be added here.
    *  
    *  @param pathN - string that holds the page pathname 
    */
    function createVars(pathN){
        if(pathN == "/referral-form"){
            firstNameValue = $("#pers_firstname").val();
            middleinit = $("#pers_middlein").val();
            lastname = $("#pers_lastname").val();
            pers_primphone = $("#pers_primphone").val();
            address = $("#pers_address").val();
            apt = $("#pers_apt_info").val();
            zip = $("#pers_zip").val();
            city = $("#pers_city").val();
            state = $("#pers_state").val();
            dob = $("#pers_dob").val();
            race = $("#pers_race").val();
            sex = $("#pers_sex").val();
            formData = $("#participant_info").serialize();
        } else if( pathN == "/self-referral-form"){
            firstNameValue = $("#self_pers_firstname").val();
            middleinit = $("#self_pers_middlein").val();
            lastname = $("#self_pers_lastname").val();
            pers_primphone = $("#self_pers_phone").val();
            address = $("#self_pers_address").val();
            apt = $("#self_apt_info").val();
            zip = $("#self_pers_zip").val();
            city = $("#self_pers_city").val();
            state = $("#self_pers_state").val();
            dob = $("#self_pers_dob").val();
            race = $("#self_pers_race").val();
            sex = $("#self_pers_sex").val();
            formData = $("#self_participant_info").serialize();
        } else if( pathN == "/intake-packet"){
            firstNameValue = $("#intake_firstname").val();
            middleinit = $("#intake_middlein").val();
            lastname = $("#intake_lastname").val();
            pers_primphone = $("#intake_phone_day").val();
            address = $("#intake_address").val();
            apt = $("#intake_intake_apt_info").val();
            zip = $("#intake_zip").val();
            city = $("#intake_city").val();
            state = $("#intake_state").val();
            dob = $("#intake_dob").val();
            race = $("#intake_ethnicity").val();
            sex = $("#intake_sex").val();
            formData = $("#intake_packet").serialize();
        }
    }

    /**
    * Stops the form submit from behaving normally to process the ajax request
    * this function is also responsible for assigning user inserted values into 
    * the hidden form that is used to query against the db for matches
    * @param e event object that prevents default form handling
    */     
    $(".checkingName").submit(function(){
        
        // Getting all values of all referral packet inputs to find matches against db
        $("form").prepend("<input type='hidden' name='selectedID' id='selectedID'>")
        createVars(pathname);

        // Setting form vars for the match query to match the vars in the referral packet
        $("#firstname").val(firstNameValue);
        $("#middleinit").val(middleinit);
        $("#lastname").val(lastname);
        $("#primphone").val(pers_primphone);
        $("#address").val(address);
        $("#apt").val(apt);
        $("#zip").val(zip);
        $("#city").val(city);
        $("#state").val(state);
        $("#dob").val(dob);
        $("#race").val(race);
        $("#sex").val(sex);
        $("#pageFrom").val(pathname);
        
        // This holds all form data entered by the user - prevents info loss during duplicate selection
        $("#prevFormData").val(formData);
    });
});




