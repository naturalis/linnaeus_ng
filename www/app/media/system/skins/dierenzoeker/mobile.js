function handleResultLinkClick(event) {
	return true; // default, use normal navigation, this is being overridden in desktop.js
}
$(document).bind("pagechange", function(ev, info) {
    var id = $(info.toPage).attr("id");    
    if ((id=="main" || id.indexOf("facetgrouppage")!=-1)) {
        $("#bottom-bar").css("display", "");
    } else {        
        $("#bottom-bar").css("display", "none");
    }
})
