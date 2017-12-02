/**
 * PEP Capping 2017 Algozzine's Class
 *
 * javascript utility functions
 *
 * functions used to reduce duplicated code
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version 1.0
 * @since 1.0
 */

/**
 * generates and appends time input for classes on the new class page
 * and the edit class information page.
 */
function populateTimes() {
    var selectTime = document.getElementById('time-input');

    //populate am first, then pm
    for(var z = 0; z <= 1; z++) {
        var amORpm = (z === 0) ? "AM" : "PM";

        //AM
        for(var i = 0; i < 12; i++){
            var hour;
            if(i === 0){
                hour = 12;
            } else{
                hour = i;
            }
            //append times to page
            selectTime.appendChild(createTime(hour, "00", amORpm));
            selectTime.appendChild(createTime(hour, "30", amORpm));
        }
    }
}