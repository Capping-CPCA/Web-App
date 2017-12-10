<?php
/**
 * PEP Capping 2017 Algozzine's Class
 * 
 * Find duplicates after completing intake, self-referral, or agency-referral packets.
 * Ussage: When an employee fills out an intake/self-referral/agency-referral form they should not 
 * be creating a new participant with each form. This page holds all vars that might be compared for
 * comparision in form_match
 *
 * @author Vallie Joseph
 * @copyright 2017 Marist College
 * @version 1.1
 * @since 1.1
 * The hidden form and modal to be incldued on any page the
 * perform a participant-match-search
 * 
*/
?>
<form class="checkingName" method='POST' action="/form-match" id="dup-form">
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
	<input type="hidden" value="" id ="pageFrom"  name="pageFrom">
    <input type="hidden" value="" id="prevFormData" name ="prevFormData">
	<input id="btnRegister" type="button" name="namecheck" class="cpca btn" onclick="submitForm()" style="margin-bottom: 20px;" value="Submit">
</form>

<script>
    function submitForm() {
        formChanged = false;
        $("#dup-form").submit();
    }
</script>