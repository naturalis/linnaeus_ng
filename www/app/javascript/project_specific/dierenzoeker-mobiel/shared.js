function appendToSearchTerms(name, value, submit, mobile) {
	var form = $('#facetForm');
	var newElement = document.createElement("input");
	newElement.type = 'hidden';
	newElement.name = name;
	newElement.value = value;
	form.append(newElement);
		
	if (submit === true) {
		$("#requestRows").val('');
		if (isMobile() || mobile) {
			$.mobile.showPageLoadingMsg();
		}		
		form.submit();
	}
}

function submitFacet(name, value) {	
	createCookie("newFacet", true, 1);
	appendToSearchTerms('data[facet][' + name + ']', value);
	appendToSearchTerms('currentFacet', name, true);
}

function removeFacet(e) {
	var element = $(e);
	//if (confirm('Zoekoptie "'+element.attr("facetlabel")+'" verwijderen?')) {
		appendToSearchTerms('data[facet][' + element.attr("facetname") + ']', element.attr("facetlink"), true);
	//}
}

function resetFacetChoices(initialQuery) {
	if (confirm('Weet je zeker dat je alle zoekopties wilt verwijderen?')) {
		var input = $('#facetForm > input[name="data[facet][searchTerms]"]').first();
		if (input) {
			input.attr("value", initialQuery);
			if ($.mobile)
				$.mobile.showPageLoadingMsg();
			$('#facetForm').submit();
		}
	}
}

function requestMoreRows(totalRows, maxRowsPerCall) {
	// show loading message
	if ($.mobile) {
		$.mobile.showPageLoadingMsg();
	}
	// get listview object
	var resultsListView = $('#resultsListView');
	// how many rows does it have
	var currentRows = resultsListView.children('li').length;
	// make the ajax call
	$.ajax({
		url: requestmore_url,
		dataType: 'html',
		data: { query: $("#searchTerms").val(), start: currentRows, rows: maxRowsPerCall },
		success: function(data) {			
			// insert after current row
			$(data).insertAfter('.result' + (currentRows - 1));
			// refresh listview
			
				$('.resultlist').each(function(){
				    try { 
				        $(this).listview("refresh");
				    } catch(e) {
				        
				    } 
			     });
			 
			// how many rows do we have now
			currentRows = resultsListView.children('li').length;
			// more rows left
			if (currentRows < totalRows) {
				// update get more button
				$("#moreRowsButton").find("span.ui-btn-text").html("Volgende " + Math.min(maxRowsPerCall, totalRows - currentRows) + " tonen");
			} else {
				// hide the button
				$("#moreRowsButton").hide();
			}
			// hide loading message
			if ($.mobile) {
				$.mobile.hidePageLoadingMsg();
			}
	  },
	  	error: function(jqXHR, textStatus, errorThrown) {
	  		alert("error processing rows request");
	  }
		// handle failure
	});
	
	return false;
}

function toggleFilter(link) {
	var $img = $(link).find("img").first();
	
	if ($img.attr("rel") == "closed") {
		$img.attr("src", images_url+"/img/TabButton_neutraal_open.png")
		$img.attr("rel", "open");
	}  
	else if($img.attr("rel") == "open" ) {
		$img.attr("src", images_url+"/img/TabButton_neutraal_closed.png")
		$img.attr("rel", "closed");
	}
	else {
		//console.log($img);
	}
	
	//$(link).find("span.ui-icon").first().toggleClass("ui-icon-arrow-d").toggleClass("ui-icon-arrow-u");
	$('#filter').slideToggle('slow');
}

function initTabButton($button, states) {
	
	// Preload image states:
	$.each(states, function(index,value) {
		$("<img />")[0].src = value;
	});

	// $button needs to be a clickable element with an img inside it.
	var $img 	= $button.find("img").first();
	
	if ($img.attr("rel") == undefined) {
		$img.attr("rel", "closed")
	}
	
	$button.mousedown(function() {
		if($img.attr("rel")=="open") {
			$img.attr("src", states.selected_open)	
		}
		else if($img.attr("rel") == "closed") {
			$img.attr("src", states.selected_closed)
		}		
	});
	$button.click(function() {
		// Slide...
		$('#filter').slideToggle('slow', function() {
			// ... And when you're done sliding:			
			if($img.attr("rel")=="open") {
				$img.attr("rel", "closed");	
				$img.attr("src", states.neutral_closed)
			}
			else if($img.attr("rel") == "closed") {
				$img.attr("rel", "open");
				$img.attr("src", states.neutral_open)
			}		
		});
	});
}

function initButton($button, states) {
	// $button needs to be a clickable element with an img inside it.
	var $img = $button.find("img").first(); 
	
	// Preload image states:
	$.each(states, function(index,value) {
		$("<img />")[0].src = value;
	});
	
	$button.mousedown(function() {		
		$img.attr("src", states.selected);
	});
	$button.click(function() {		
		$img.attr("src", states.neutral);
	});	
}

function closeInfoPanel() {
	$("#info-panel-container").hide();
}
function showInfoPanel() {	
	$("#info-panel-container").show();
}

function hideAddressBar() {
setTimeout(function(){
    // Hide the address bar!
    window.scrollTo(0, 1);
  }, 0);
}

function addPaddingToFotos() {
	var $firstSmallImg = $(".fotos > img, .fotos > a > img").first();
	$firstSmallImg.css("padding-right", "20px");
	$(".fotos > img, .fotos > a > img").css("padding-bottom", "20px");
}

	
var shakeCalled = false;
$('div[id^="facetgrouppage"]').live('pageshow', function(event, ui) {
	$('#facetForm').attr("action", "#"+event.target.id);
	if (readCookie("newFacet")!=null) {
		//$('.facet-value-choice-feedback').show().delay(6000).fadeOut(500);	
		if (!shakeCalled) {
			
			shakeCalled = true;
		}	
	}
	eraseCookie("newFacet");
	hideAddressBar();
});

$('#main').live('pageshow', function(event, ui) {	
	$('#facetForm').attr("action", "#main");	
	hideAddressBar();
});

$('.main-page').live( 'pageinit',function(event, ui){
  	initTabButton($("#tab-button"), {
		selected_open:		images_url+"img/TabButton_selected_open.png",
		selected_closed:	images_url+"img/TabButton_selected_closed.png",
		neutral_open:		images_url+"img/TabButton_neutraal_open.png",
		neutral_closed:		images_url+"img/TabButton_neutraal_closed.png"
	});
	initButton($(".large-close-button-link"), {
		selected:	images_url+"img/Sluiten_selected.png",
		neutral:	images_url+"img/Sluiten_neutraal.png"		
	});
	
	$(".info-panel-close-button").click(closeInfoPanel);
	$("#infobutton").click(showInfoPanel);
	hideAddressBar();
});

$('.show-page').live('pageinit', function(event,ui) {
	initButton($("#back-button"), {
		neutral:	images_url+"img/Stap_terug_neutraal.png",
		selected:	images_url+"img/Stap_terug_selected.png"
	});	
	addPaddingToFotos();
	hideAddressBar();
});
$('.facetgrouppage').live('pageinit', function(event, ui){
	var $menubutton = $(this).find(".menu-button").first();
	initButton($menubutton, {
		neutral: 	images_url+'img/Menu_neutraal.png',
		selected:	images_url+'img/Menu_selected.png'
	});
	if (readCookie("newFacet")==null) {
		$('.facet-value-choice-feedback').hide()
	}
	hideAddressBar();	
});

$('#results').live('pageshow', function(event, ui) {
	$('#facets').appendTo("#menu-tab");
	hideAddressBar();
});

// from: www.quirksmode.org/js/cookies.html
function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}

function isMobile() {
	return ( navigator.userAgent.match(/Android/i) ||
	  navigator.userAgent.match(/webOS/i) ||
	  navigator.userAgent.match(/iPhone/i) ||
	  navigator.userAgent.match(/iPod/i) ||
	  navigator.userAgent.match(/BlackBerry/)
 	)
}
