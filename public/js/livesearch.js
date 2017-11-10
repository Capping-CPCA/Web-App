/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Live search js function that allows users to search while typing and view results on their current page
 *
 * Ussage: When there exists a page where a user has the ability to seach/query a database, this class will
 * take in user specified input and query the database with each keystroke. The results are sent back to the
 * origin page (where the user is), and displayed in a parent/child based view.
 *
 * @author Vallie Joseph
 * @copyright 2017 Marist College
 * @since 0.1.2
 * @version 0.1.2
 *
 * @param input - form input given by the user
 * @param results - the main section in which results will be displayed, must of parent/child structure
 * i.e Unordered Lists, Ordered Lists, or Tables
 * @param urlResult - where the ajax url will point to- see AJAX url documentation
 * @param formMethod - method in which ajax will submit the form - see AJAX  type documentation
 * @param typeData - type that ajax will return your data in, options are text, html, and json
 * @param resultFilter - this is how you will extract the specific desired elements from your results, for example,
 * If the user types 'John Doe' into the form input field, AJAX will return the information it recieves from a succesful submission.
 * If the submission is succesful, the returned information may be html (based on developer specifications in typeData)
 * If we return html, we may only be interested in the list elements of the results- this is the place to specify that
 * @setInputListener = constructor
 */

//init vars
var input;
var results;
var indiResults;
var urlResult;
var formMethod;
var typeData;
var resultFilter;

//set values to vars, this is done wherever the livesearch is called
function setInputListener(inputField , resultField, resultHolder, uUrl, method, typeD){

	input = inputField;
	results = resultField;
	indiResults = resultHolder;
	urlResult =  uUrl + '/';
	typeData = typeD;
	return this;
}

//set the method type for ajax submission
function setMethod(method){
	var regex= /(GET|PUT)/g;
	var methods = method.match(regex);
	if(methods != null){
		return this;
	}
	console.log("The method was not set correctly");
	return false;
}

//setting the filter / find for returning only desired result html
function setResultFilter(filter){
	resultFilter = filter;
	return this;
}

//actual jquery live search fn, takes in the form where live search is being performed
(function( $form ){
	$.fn.liveSearch = function() {
		//giving the form it's own var for simplicity sake, later functions redefine the 'this' method
		var uForm = $(this);

		//taking cleaning user supplied input - no extra spaces or , will be queried
		function filterResults(raw){
			return raw.replace(/\s+/g,' ').trim().replace(',','');
		}
		
		//takes user input and sanitizes it live
		function filterInput(raw){
			return raw.replace(/\s\s/g,' ');
		}
		
		//set the form input element text to cleaned text, nice and tidy
		uForm.find(":text").val(filterInput(uForm.find(":text").val()));
		var userInput=  uForm.find(":text").val();

		//ajax - asynchronously requesting resutls from our result page
		$.ajax({
			type: formMethod,
			url: urlResult+userInput.trim(),
			dataType: typeData,
			data: uForm.serialize(),
			success:function(data){
				//clear the search result display each time we look up new results - otherwise duplicates show
				results.empty();
				var userResults = $(data).find(resultFilter);
				
				//check to see if we're looking for card elements
				if(userResults.attr('class') ==  "card-title"){
					userResults = $("."+userResults.attr('class')+":contains("+userInput+")");
					
				}
				
				//jquery for each short hand, populating result list with well...results
				$(userResults).each(function(){
					//creates new children for dev specified parent
					$(results).append($(indiResults).addClass('search-result').html($(this).text()));

				});
				//whenever a result is clicked, complete the search to direct the user to the results page
				//this should work with any child of the desired element (Ex: rows in a table, list elemetns in a list)
                $(results).children().each(function() {
                    $(this).click(function(){
                        //first make sure the user input is updated
                        input.val(filterResults($(this).text()));
                        //submit that form!
                        uForm.submit();
                    });

                    $(this).keypress(function(e) {
                        if (e.key === 'Enter' || e.key === 'Space') {
                            $(this).click();
                        }
                    });
                });
			}
		});
		return this;
	};
})( jQuery );
