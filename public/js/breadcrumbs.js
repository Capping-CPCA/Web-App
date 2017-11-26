$(document).ready(function(){
	var title = $("#page-title").text();
	console.log(title);
	var getNavs = $("a.nav-link.text-secondary").attr('class').split(/\s+/);
	var breadcrumbObject = {};
	for (var i =0; i < $("a.nav-link.text-secondary").length; i++){
		var classArray = $($("a.nav-link.text-secondary")[i]).attr('class').split(/\s+/);
		var nameOfLink =$($("a.nav-link.text-secondary")[i]).text();
		//console.log(classArray);
		if(classArray.indexOf("collapsed") >=0 ){
			var linksInner = $($("a.nav-link.text-secondary")[i]).next(".collapse");
			var linksNumberIner = linksInner.children().children()
			for(var j = 0; j < linksInner.length; j++){
				var innerListArray = [];
				var smallerLinks = $(linksInner.children()[j]).children();
				console.log(smallerLinks);
				for( var k = 0; k <smallerLinks.length; k++ ){
					innerListArray.push($(smallerLinks[k]).text().trim());
				}
			breadcrumbObject[nameOfLink.trim()] =innerListArray;
			}
		}else{
			breadcrumbObject[nameOfLink.trim()] = $($("a.nav-link.text-secondary")[i]).text().trim();
		}
	}
	//console.log(breadcrumbObject);
	console.log(Object.keys(breadcrumbObject));
});