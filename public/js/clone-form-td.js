/*
Author: Tristan Denyer (based on Charlie Griefer's original clone code, and some great help from Dan - see his comments in blog post)
Plugin repo: https://github.com/tristandenyer/Clone-section-of-form-using-jQuery
Demo at http://tristandenyer.com/using-jquery-to-duplicate-a-section-of-a-form-maintaining-accessibility/
Ver: 0.9.5.0
Last updated: Oct 23, 2015

The MIT License (MIT)

Copyright (c) 2011 Tristan Denyer

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

// FOR ADDING FAMILY MEMBERS
$(function () {
    $('#btnAddMember').click(function () {
        var num     = $('.clonedFamily').length, // Checks to see how many "duplicatable" input fields we currently have
            newNum  = new Number(num + 1),      // The numeric ID of the new input field being added, increasing by 1 each time
            newElem = $('#familyEntry_' + num).clone().attr('id', 'familyEntry_' + newNum).hide().fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value

        /*  This is where we manipulate the name/id values of the input inside the new, cloned element
            Below are examples of what forms elements you can clone, but not the only ones.
            There are 2 basic structures below: one for an H2, and one for form elements.
            To make more, you can copy the one for form elements and simply update the classes for its label and input.
            Keep in mind that the .val() method is what clears the element when it gets cloned. Radio and checkboxes need .val([]) instead of .val('').
        */
        // Section header
        newElem.find('.heading-reference').attr('id', 'ID' + newNum + '_reference').attr('name', 'ID' + newNum + '_reference').html('Household Member ' + newNum);

        // First name - text
        newElem.find('.label_fn').attr('for', 'family_first_name_' + newNum);
        newElem.find('.input_fn').attr('id', 'family_first_name_' + newNum).attr('name', 'family_first_name_' + newNum).val('');

        // Last name - text
        newElem.find('.label_ln').attr('for', 'family_last_name_' + newNum );
        newElem.find('.input_ln').attr('id', 'family_last_name_' + newNum).attr('name', 'family_last_name_' + newNum).val('');

        // Middle initial - text
        newElem.find('.label_mi').attr('for', 'family_mi_' + newNum);
        newElem.find('.input_mi').attr('id', 'family_mi_' + newNum).attr('name', 'family_mi_' + newNum).val('');

        // DOB - text
        newElem.find('.label_dob').attr('for', 'family_dob_' + newNum);
        newElem.find('.input_dob').attr('id', 'family_dob_' + newNum).attr('name', 'family_dob_' + newNum).val('');

        // Race - select
        newElem.find('.label_race').attr('for', 'family_race_' + newNum);
        newElem.find('.select_race').attr('id', 'family_race_' + newNum).attr('name', 'family_race_' + newNum).val('');

        // Sex - select
        newElem.find('.label_sex').attr('for', 'family_sex_' + newNum);
        newElem.find('.select_sex').attr('id', 'family_sex_' + newNum).attr('name', 'family_sex_' + newNum).val('');

        // relationship - select
        newElem.find('.label_relationship').attr('for', 'family_relationship_' + newNum);
        newElem.find('.select_relationship').attr('id', 'family_relationship_' + newNum).attr('name', 'family_relationship_' + newNum).val('');

        // Needs - text
        newElem.find('.label_needs').attr('for', 'family_needs_' + newNum);
        newElem.find('.input_needs').attr('id', 'family_needs_' + newNum).attr('name', 'family_needs_' + newNum).val('');


        // Insert the new element after the last "duplicatable" input field
        $('#familyEntry_' + num).after(newElem);
        $('#ID' + newNum + '_title').focus();

        // Enable the "remove" button. This only shows once you have a duplicated section.
        $('#btnDelMember').attr('disabled', false);

        // Right now you can only add 4 sections, for a total of 5. Change '5' below to the max number of sections you want to allow.
        if (newNum == 5)
            $('#btnAddMember').attr('disabled', true).prop('value', "You've reached the limit"); // value here updates the text in the 'add' button when the limit is reached
    });

    $('#btnDelMember').click(function () {
        // Confirmation dialog box. Works on all desktop browsers and iPhone.
        if (confirm("Are you sure you wish to remove this family member? This cannot be undone."))
        {
            var num = $('.clonedFamily').length;
            // how many "duplicatable" input fields we currently have
            $('#familyEntry_' + num).slideUp('slow', function () {$(this).remove();
                // if only one element remains, disable the "remove" button
                if (num -1 === 1)
                    $('#btnDelMember').attr('disabled', true);
                // enable the "add" button
                $('#btnAddMember').attr('disabled', false).prop('value', "add section");});
        }
        return false; // Removes the last section you added
    });
    // Enable the "add" button
    $('#btnAddMember').attr('disabled', false);
    // Disable the "remove" button
    $('#btnDelMember').attr('disabled', true);
});





// FOR ADDING ADDITIONAL PARTIES
$(function () {
    $('#btnAddParty').click(function () {
        var num     = $('.clonedParty').length, // Checks to see how many "duplicatable" input fields we currently have
            newNum  = new Number(num + 1),      // The numeric ID of the new input field being added, increasing by 1 each time
            newElem = $('#partyEntry_' + num).clone().attr('id', 'partyEntry_' + newNum).hide().fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value

        /*  This is where we manipulate the name/id values of the input inside the new, cloned element
            Below are examples of what forms elements you can clone, but not the only ones.
            There are 2 basic structures below: one for an H2, and one for form elements.
            To make more, you can copy the one for form elements and simply update the classes for its label and input.
            Keep in mind that the .val() method is what clears the element when it gets cloned. Radio and checkboxes need .val([]) instead of .val('').
        */
        // Section header
        newElem.find('.heading-reference').attr('id', 'ID' + newNum + '_reference').attr('name', 'ID' + newNum + '_reference').html('Additional Party ' + newNum);

        // Party type - select
        newElem.find('.label_type').attr('for', 'party_type_' + newNum);
        newElem.find('.select_type').attr('id', 'party_type_' + newNum).attr('name', 'party_type_' + newNum).val('');

        // First name - text
        newElem.find('.label_fn').attr('for', 'party_firstname_' + newNum);
        newElem.find('.input_fn').attr('id', 'party_firstname_' + newNum).attr('name', 'party_firstname_' + newNum).val('');

        // Last name - text
        newElem.find('.label_ln').attr('for', 'party_lastname_' + newNum );
        newElem.find('.input_ln').attr('id', 'party_lastname_' + newNum).attr('name', 'party_lastname_' + newNum).val('');

        // Phone - text
        newElem.find('.label_phone').attr('for', 'party_phone_' + newNum);
        newElem.find('.input_phone').attr('id', 'party_phone_' + newNum).attr('name', 'party_phone_' + newNum).val('');


        // Email - text
        newElem.find('.label_email').attr('for', 'party_email_' + newNum);
        newElem.find('.input_email').attr('id', 'party_email_' + newNum).attr('name', 'party_email_' + newNum).val('');


        // Insert the new element after the last "duplicatable" input field
        $('#partyEntry_' + num).after(newElem);
        $('#ID' + newNum + '_title').focus();
        initMask();

        // Enable the "remove" button. This only shows once you have a duplicated section.
        $('#btnDelParty').attr('disabled', false);

        // Right now you can only add 4 sections, for a total of 5. Change '5' below to the max number of sections you want to allow.
        if (newNum == 5)
            $('#btnAddParty').attr('disabled', true).prop('value', "You've reached the limit"); // value here updates the text in the 'add' button when the limit is reached
    });

    $('#btnDelParty').click(function () {
        // Confirmation dialog box. Works on all desktop browsers and iPhone.
        if (confirm("Are you sure you wish to remove this party? This cannot be undone."))
        {
            var num = $('.clonedParty').length;
            // how many "duplicatable" input fields we currently have
            $('#partyEntry_' + num).slideUp('slow', function () {$(this).remove();
                // if only one element remains, disable the "remove" button
                if (num -1 === 1)
                    $('#btnDelParty').attr('disabled', true);
                // enable the "add" button
                $('#btnAddParty').attr('disabled', false).prop('value', "add section");});
        }
        return false; // Removes the last section you added
    });
    // Enable the "add" button
    $('#btnAddParty').attr('disabled', false);
    // Disable the "remove" button
    $('#btnDelParty').attr('disabled', true);

});