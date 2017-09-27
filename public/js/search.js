$(document).ready(function(){
    var userSearch = "";
    var pName = $(".participant_name");
    var pStatus = $(".participant_status");
    var pNotes = $(".participant_notes");
    var pOther = $(".participant_other");

    $(".sublist").hide();

    $( ".dynamic-search" ).keypress(function() {
        userSearch=$(".dynamic-search").val();
        console.log(userSearch);
        $(".list-group-item:not(:contains("+userSearch+"))").hide();
        $(".list-group-item:contains("+userSearch+")").show();
    });
    $(".advanced-info").click(function(e){
        // var relatedDetailedInfo = $(this).parent().parent().children(".sublist");
        // relatedDetailedInfo.toggle("fast");
        var relatedDetailedInfo =$(this).parent().children(".sublist");
        relatedDetailedInfo.toggle("fast");
    });
});