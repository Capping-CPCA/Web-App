/**
 * PEP Capping 2017 Algozzine's Class
 *
 * functions that help the dashboard's button pagination
 *
 * determines how many buttons and the content of each button
 *
 * @author Scott Hansen
 * @copyright 2017 Marist College
 * @version 0.7
 * @since 0.7
 */

window.onload = function () {
    handlePagination();
};

//holds all of the rows
var tableContent;
var rowsParent = document.getElementById('result-pag');

/**
 * takes original page query result on page and uses javascript to
 * hide some of these elements and to create buttons to navigate
 * the available recent classes to see.
 */
function handlePagination() {

    var numResults = rowsParent.childElementCount;

    //disable and enable buttons based on row count
    if (numResults <= 5){
        //do nothing
    }else if(numResults <= 10){
        createPaginationListElement(2);
    } else if(numResults <= 15){
        createPaginationListElement(2);
        createPaginationListElement(3);
    } else { //over 20
        createPaginationListElement(2);
        createPaginationListElement(3);
        createPaginationListElement(4);
    }

    //store all rows in an array
    tableContent = rowsParent.innerHTML;

    showSelectedChildren(0, 4)


}

/**
 * creates a numbered button and then appends it to the page
 *
 * @param num{int} - the number to be displayed on the button
 *
 */
function createPaginationListElement(num){
    var btnParent = document.getElementById('button-nav');

    var btnElement = document.createElement('button');
    btnElement.setAttribute('id', 'btn-pag-' + num.toString());
    btnElement.setAttribute('name', 'btn-pag');
    btnElement.classList.add('btn');
    btnElement.classList.add('btn-outline-secondary');
    btnElement.setAttribute('onclick', "selectButton(" + num.toString() + ")");
    btnElement.innerText = num.toString();

    btnParent.appendChild(btnElement);

}

/**
 *
 * @param selectionNum{int} - what number button was clicked
 *
 */
function selectButton(selectionNum){
    switch (selectionNum){
        case 1:
            showSelectedChildren(0,4);
            changeButton(1);
            break;
        case 2:
            showSelectedChildren(5,9);
            changeButton(2);
            break;
        case 3:
            showSelectedChildren(10,14);
            changeButton(3);
            break;
        case 4:
            showSelectedChildren(15,19);
            changeButton(4);
            break;
        default:
            showSelectedChildren(0,4);
            changeButton(1);
            break;
    }
}

/**
 * takes in the number of button which is to be changed to selected
 * and resets the other button colors, then adds a class to indicate
 * it was selected
 *
 * @param num{int} - the number button to be changed
 *
 */
function changeButton(num) {
    resetButtonColors();
    var clickedButton = document.getElementById("btn-pag-" + num.toString());
    clickedButton.removeAttribute('class');
    clickedButton.classList.add('btn');
    clickedButton.classList.add('btn-dark');
}

/**
 * set all buttons to secondary class
 */
function resetButtonColors() {
    var btnGroup = document.getElementsByName("btn-pag");
    for(var i = 0; i < btnGroup.length; i++){
        btnGroup[i].removeAttribute('class');
        btnGroup[i].classList.add('btn');
        btnGroup[i].classList.add('btn-outline-secondary');
    }
}

/**
 * controls which rows will be generated in attendance
 * dashboard table generation
 *
 * @param min{int} - bottom number of range of class rows to show
 * @param max{int} - top number of range of class row to show
 *
 */
function showSelectedChildren(min, max){
    //remove all elements
    rowsParent.innerHTML = tableContent;

    var numElements = rowsParent.childElementCount;

    //append appropriate children
    for(var i = 0; i <= numElements; i++){
        //remove all children not in that range
        if((i < min) || (i > max)){
            var row = document.getElementById(i.toString());
            if(row !== null){
                row.outerHTML = "";
            }
            delete row;
        }

    }

}