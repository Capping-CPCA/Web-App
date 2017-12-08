<?php
/**
 * PEP Capping 2017 Algozzine's Class
 * 
 * Find duplicates after completing intake, self-referral, or agency-referral packets.
 * Ussage: When an employee fills out an intake/self-referral/agency-referral form they should not 
 * be creating a new participant with each form. This function grabs the user supplied input from whichever
 * form they are filling out, and populates it into a hidden form. That hidden form is then submitted through an
 * ajax request which routes to a page with a sql query that looks for any possible matches in the db.
 * @author Vallie Joseph
 * @copyright 2017 Marist College
 * @version 1.1
 * @since 1.1
 * The hidden form and modal to be incldued on any page the
 * perform a participant-match-search
 * 
*/
?>
<form class="checkingName" method='GET' action="/form-match">
	<input type="hidden" value="" id ="firstname" name="firstname">
	<input type="hidden" value="" id ="middleinit"  name="middleinit">
	<input type="hidden" value="" id ="lastname"  name="lastname">
	<input type="hidden" value="" id ="primphone"  name="primphone">
	<input type="hidden" value="" id ="race"  name="race">
	<input type="hidden" value="" id ="sex"  name="sex">
	<input type="hidden" value="" id ="address"  name="address">
	<input type="hidden" value="" id ="zip"  name="zip">
	<input type="hidden" value="" id ="dob"  name="dob">
	<input type="hidden" value="" id ="state"  name="state">
	<input type="hidden" value="" id ="city"  name="city">
	<input type="hidden" value="" id ="apt"  name="apt">
	<input id="btnRegister" type="submit" name="namecheck" class="cpca btn" onclick="formChanged = false;" style="margin-bottom: 20px;">
</form>

<div class="modal fade modal-larger" id="matchModal" tabindex="-1" role="dialog" aria-labelledby="matchModal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width: 50rem;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Are You Sure This is a New Participant?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="container">
                <div class="modal-body row ">
                    <div class="col">
                        <div class=" sticky-top">
                            <i>Information Currently Entered:</i>
                            <div class="followNameHolder">
                            </div>
                        </div>
                    </div>
                    <div class="col namesCol">
                        <div class="sticky-top" style="background-color:white;">
                            <i>Click to select a participant.</i>
                        </div>
                        <div class="nameCol">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel" data-dismiss="modal">Yes, this is a new
                    participant
                </button>
                <button type="button" class="btn cpca" onclick="$('.close').click();">Go Back</button>
            </div>
        </div>
    </div>
</div>