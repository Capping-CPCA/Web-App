/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Displaying additional details to differentiate participants
 *
 * Ussage: After the user submits their search query for a participant, there may be multiple
 * participants with similiar names. To resolve this, we serach for additional details for a 
 * specific participant and display it optionally if the user expands the participant view
 *
 * @author Vallie Joseph
 * @copyright 2017 Marist College
 * @version 0.7.1
 * @since 0.1
 */
 $(document).ready(function(){
    var userSearch = "";
    var pName = $(".participant_name");
    var pStatus = $(".participant_status");
    var pNotes = $(".participant_notes");
    var pOther = $(".participant_other");

    $(".sublist").hide();

    $( ".dynamic-search" ).keypress(function() {
        userSearch=$(".dynamic-search").val();
        $(".list-group-item:not(:contains("+userSearch+"))").hide();
        $(".list-group-item:contains("+userSearch+")").show();
    });
	
    $(".advanced-info").click(function(e){
		var btnclass= $(this).find(".fa").attr("class");
		if( btnclass == "fa fa-caret-right"){
			$(this).find("i").removeClass("fa-caret-right");
			$(this).find("i").addClass("fa-caret-down");
		}else if(btnclass== "fa fa-caret-down"){
			$(this).find("i").removeClass("fa-caret-down");
			$(this).find("i").addClass("fa-caret-right");
		}
		//if()
        var relatedDetailedInfo =$(this).parent().children(".sublist");
        relatedDetailedInfo.toggle("fast");
    });
	
});