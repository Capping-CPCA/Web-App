/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Form helping functions for form validation/submission/other logic
 *
 * This JS file is used to store functions needed for the forms to properly submit and display their data.
 *
 * @author Christian Menk and Stephen Bohner
 * @copyright 2017 Marist College
 * @version 0.3.3
 * @since 0.3.2
 */

$(document).ready(function(){
    initMaskIntake();
    $('input:radio')
        .on('click', function() {
            showFields(this);
        });
    $('input:required')
        .on('input', function() {
            validationIntake($(this));
        })
        .on('focusout', function() {
            validationIntake($(this));
        });
    $('.mask-zip')
        .on('focusout', function() {
            validateZip($(this));
        })
});

function initMaskIntake(){
    $('.mask-zip').mask('00000');
    $('.mask-phone').mask('(000) 000-0000');
}

function submitAllIntake(){
    var intake_firstname = document.getElementById("intake_firstname");
    var intake_lastname = document.getElementById("intake_lastname");
    var intake_zip = document.getElementById("intake_zip");
    var intake_packet = document.getElementById("intake_packet");
    var intake_card = document.getElementById("intake_participantInfo_title");
    // Handles all validation when the user hits the submit button.
    if (intake_packet.checkValidity() === false) {
        intake_card.focus();
        if (intake_lastname.value.length === 0) {
            intake_lastname.focus();
        }
        if (intake_firstname.value.length === 0) {
            intake_firstname.focus();
        }
    } else if (!validZip($('#intake_zip'))) {
        intake_card.focus();
        intake_zip.focus();
    } else {
        intake_packet.submit();
    }
}

// Hides or displays the error message accordingly.
function validationIntake(el){
    if (el.val().length !== 0) {
        el.removeClass('is-invalid');
    } else {
        el.addClass('is-invalid');
    }
}

// Validates zip code.
function validateZip(el){
    if (validZip(el)) {
        el.removeClass('is-invalid');
    } else {
        el.addClass('is-invalid');
    }
}

function validZip(el){
    if (el.val().length === 5 || el.val().length === 0) {
        return true;
    } else {
        return false;
    }
}

// This function displays hidden extra questions within the radio button questionnaire.
// Does so through jQuery searching for the class associated with the radio button
// Had to use classes and not ID's because of the possibility of multiple fields being shown for one radio button
// Classes answer_yes and answer_no determine which radio button will display the hidden field (for situational questions)
function showFields(el){
    var fieldNameYes = el.name + "_div_yes";
    var fieldNameNo = el.name + "_div_no";

    if($('.' + fieldNameYes).hasClass('answer_yes')) {
        if (el.value === "Yes") {
            $('.' + fieldNameYes).removeClass('hidden-field').css({opacity: 0}).animate({opacity:1},600);
        } else {
            $('.' + fieldNameYes).addClass('hidden-field');
        }
    }

    if($('.' + fieldNameNo).hasClass('answer_no')) {
        if (el.value === "No") {
            $('.' + fieldNameNo).removeClass('hidden-field').css({opacity: 0}).animate({opacity:1},600);
        } else {
            $('.' + fieldNameNo).addClass('hidden-field');
        }
    }
}

$(function () {
    $('#btnAddChild').click(function () {
        var num     = $('.clonedChild').length, // Checks to see how many "duplicatable" input fields we currently have
            newNum  = new Number(num + 1),      // The numeric ID of the new input field being added, increasing by 1 each time
            newElem = $('#childEntry_' + num).clone().attr('id', 'childEntry_' + newNum).hide().fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value

        /*  This is where we manipulate the name/id values of the input inside the new, cloned element
            Below are examples of what forms elements you can clone, but not the only ones.
            There are 2 basic structures below: one for an H2, and one for form elements.
            To make more, you can copy the one for form elements and simply update the classes for its label and input.
            Keep in mind that the .val() method is what clears the element when it gets cloned. Radio and checkboxes need .val([]) instead of .val('').
        */
        // Section header
        newElem.find('.heading-reference').attr('id', 'ID' + newNum + '_reference').attr('name', 'ID' + newNum + '_reference').html('Child ' + newNum);

        // First name - text
        newElem.find('.label_fn').attr('for', 'child_first_name_' + newNum);
        newElem.find('.input_fn').attr('id', 'child_first_name_' + newNum).attr('name', 'child_first_name_' + newNum).val('');

        // Last name - text
        newElem.find('.label_ln').attr('for', 'child_last_name_' + newNum );
        newElem.find('.input_ln').attr('id', 'child_last_name_' + newNum).attr('name', 'child_last_name_' + newNum).val('');

        // Middle initial - text
        newElem.find('.label_mi').attr('for', 'child_mi_' + newNum );
        newElem.find('.input_mi').attr('id', 'child_mi_' + newNum).attr('name', 'child_mi_' + newNum).val('');

        // dob - text
        newElem.find('.label_dob').attr('for', 'child_dob_' + newNum);
        newElem.find('.input_dob').attr('id', 'child_dob_' + newNum).attr('name', 'child_dob_' + newNum).val('');

        // Sex
        newElem.find('.label_sex').attr('for', 'child_sex_' + newNum);
        newElem.find('.select_sex').attr('id', 'child_sex_' + newNum).attr('name', 'child_sex_' + newNum).val('');

        // Race
        newElem.find('.label_race').attr('for', 'child_race_' + newNum);
        newElem.find('.select_race').attr('id', 'child_race_' + newNum).attr('name', 'child_race_' + newNum).val('');

        // Live
        newElem.find('.label_live').attr('for', 'child_live_' + newNum);
        newElem.find('.input_live').attr('id', 'child_live_' + newNum).attr('name', 'child_live_' + newNum).val('');

        // Custody
        newElem.find('.label_custody').attr('for', 'child_custody_' + newNum);
        newElem.find('.input_custody').attr('id', 'child_custody_' + newNum).attr('name', 'child_custody_' + newNum).val('');

        // Insert the new element after the last "duplicatable" input field
        $('#childEntry_' + num).after(newElem);
        $('#ID' + newNum + '_title').focus();
        initMask();

        // Enable the "remove" button. This only shows once you have a duplicated section.
        $('#btnDelChild').attr('disabled', false);

        // Right now you can only add 4 sections, for a total of 5. Change '5' below to the max number of sections you want to allow.
        if (newNum == 5)
            $('#btnAddChild').attr('disabled', true).prop('value', "You've reached the limit"); // value here updates the text in the 'add' button when the limit is reached
    });

    $('#btnDelChild').click(function () {
        // Confirmation dialog box. Works on all desktop browsers and iPhone.
        $("#childModal").modal();
        $('#childConfirm').click(function () {
            var num = $('.clonedChild').length;
            // how many "duplicatable" input fields we currently have
            $('#childEntry_' + num).slideUp('slow', function () {$(this).remove();
                // if only one element remains, disable the "remove" button
                if (num -1 === 1)
                    $('#btnDelChild').attr('disabled', true);
                // enable the "add" button
                $('#btnAddChild').attr('disabled', false).prop('value', "add section");});
        });
        return false; // Removes the last section you added
    });
    // Enable the "add" button
    $('#btnAddChild').attr('disabled', false);
    // Disable the "remove" button
    $('#btnDelChild').attr('disabled', true);
});