/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Form helping functions for form validation/submission/other logic
 *
 * This JS file is used to store functions needed for the forms to properly submit and display their data.
 *
 * @author Christian Menk and Stephen Bohner
 * @copyright 2017 Marist College
 * @version 1.2.2
 * @since 0.3.2
 */

$(document).ready(function(){
    initMask();
    $('input:required')
        .on('input', function() {
            validation($(this));
        })
        .on('focusout', function() {
            validation($(this));
        });
    $('.mask-zip')
        .on('focusout', function() {
            validateZip($(this));
        })
});

function initMask(){
    $('.mask-zip').mask('00000');
    $('.mask-phone').mask('(000) 000-0000');
}

function disableFields(){
    $("#self_participant_info :input").prop("disabled", true);
    $('#collapse2').collapse('show');
    $('#collapse3').collapse('show');
    $('#collapse4').collapse('show');
}

// Javascript validation for Referral form.
function submitAll(){
    var fname = document.getElementById("pers_firstname");
    var lname = document.getElementById("pers_lastname");
    var pers_zip = document.getElementById("pers_zip");
    var ref_email = document.getElementById("ref_email");
    var reqForm = document.getElementById("participant_info");
    var card = document.getElementById("pers_title");
    var card2 = document.getElementById("pers_referring_party_info");
    // Handles all validation when the user hits the submit button.
    if (reqForm.checkValidity() === false) {
        if (fname.value.length === 0 ) {
            card.focus();
            fname.focus();
        } else if (lname.value.length === 0) {
            card.focus();
            lname.focus();
        } else {
            $('#ref_email').addClass("is-invalid");
            card2.focus();
            ref_email.focus();
        }
    } else if (pers_zip.value.length !== 5 && pers_zip.value.length !== 0) {
        card.focus();
        pers_zip.focus();
    } else {
        reqForm.submit();
    }
}

// Javascript validation for Initial Contact / Self-Referral form.
function submitAllSelf(){
    var self_fname = document.getElementById("self_pers_firstname");
    var self_lname = document.getElementById("self_pers_lastname");
    var self_pers_zip = document.getElementById("self_pers_zip");
    var self_form = document.getElementById("self_participant_info");
    var self_card = document.getElementById("self_pers_title");
    // Handles all validation when the user hits the submit button.
    if (self_form.checkValidity() === false) {
        self_card.focus();
        if(self_lname.value.length === 0)
            self_lname.focus();
        if(self_fname.value.length === 0)
            self_fname.focus();
    } else if (self_pers_zip.value.length !== 5 && self_pers_zip.value.length !== 0) {
        self_card.focus();
        self_pers_zip.focus();
    } else {
        self_form.submit();
    }
}

// Hides or displays the error message accordingly.
function validation(el){
    if (el.val().length !== 0) {
        el.removeClass('is-invalid');
    } else {
        el.addClass('is-invalid');
    }
}

// Functions for navigating through cards.
function section1(){
    $('#collapse1').collapse('show');
    $('#collapse2').collapse('hide');
    $('#collapse3').collapse('hide');
    $('#collapse4').collapse('hide');
    $('#collapse5').collapse('hide');
}

function section2(){
    $('#collapse1').collapse('hide');
    $('#collapse2').collapse('show');
    $('#collapse3').collapse('hide');
    $('#collapse4').collapse('hide');
    $('#collapse5').collapse('hide');
}

function section3(){
    $('#collapse1').collapse('hide');
    $('#collapse2').collapse('hide');
    $('#collapse3').collapse('show');
    $('#collapse4').collapse('hide');
    $('#collapse5').collapse('hide');
}

function section4(){
    $('#collapse1').collapse('hide');
    $('#collapse2').collapse('hide');
    $('#collapse3').collapse('hide');
    $('#collapse4').collapse('show');
    $('#collapse5').collapse('hide');
}

function section5(){
    $('#collapse1').collapse('hide');
    $('#collapse2').collapse('hide');
    $('#collapse3').collapse('hide');
    $('#collapse4').collapse('hide');
    $('#collapse5').collapse('show');
}