var nbcData;
var nbcStart = 0;
var nbcPerPage = 16;	// default, reset in identify.php
var nbcPerLine = 2;		// default, reset in identify.php
var initData;
var matrixId=null;
var projectId=null;
var startDier=null;

function nbcPrettyPhotoInit() {

 	$("a[rel^='prettyPhoto']").prettyPhoto({
		allow_resize:true,
		animation_speed:50,
 		opacity: 0.70, 
		show_title: false,
 		overlay_gallery: false,
 		social_tools: false
 	});

}

function nbcGetResults(p) {

	setCursor('wait');

	if (p==undefined) var p={};

	p.noGroups=1;
	p.noActiveChars=1;

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_results_nbc',
			params : p,
			time : getTimestamp(),
			key : matrixId,
			p : projectId
		}),
		success : function (data) {
			nbcData = $.parseJSON(data);
			nbcDoResults();
			setCursor();
			updateChoicesMade();
		   	$('.facetgrouppage-close-btn').click();
		  	openDierLink();
		}
	});
	
}

function nbcDoResults(p) {

	if (nbcData.results) {
		verbergDier();
		nbcResetNavigation();
		nbcPrintResults();
	}

}

function nbcPrintResults() {

	var s = '<ul>';

	for(var i=0;i<nbcData.results.length;i++) {
		var data = nbcData.results[i];
		if (i>=nbcStart && i<nbcStart+nbcPerPage) {
			s = s + '<li class="result0"><a href="/linnaeus_ng/app/views/matrixkey/identify.php?dier='+data.l+'" onclick="toonDier({id:'+data.i+',type:\''+data.y+'\'});return false;" style=""><table><tr><td><img alt="" src="'+data.b+'"></td><td style="width:100%">'+data.l+'</td></tr></table></a></li>';
		}
	}

	s = s + '</ul>';
	
	$('#result-list-container').html(s);
	$('#result-count-container').html(nbcData.count.results);

}

function navigeren(dir) {

	if (dir=='eerste')
		nbcStart = 0;
	else
	if (dir=='laatste')
		nbcStart = Math.floor(nbcData.count.results/nbcPerPage)*nbcPerPage;
	else
	if (dir=='vorige')
		nbcStart = nbcStart-nbcPerPage;
	else
		nbcStart = nbcStart+nbcPerPage;

	if (nbcStart>nbcData.count.results)
		nbcStart = nbcStart-nbcPerPage;
	if (nbcStart<0)
		nbcStart = 0;

	nbcPrintResults();
	nbcUpdateNavigation();

}

function nbcResetNavigation() {

	nbcStart=0;
	nbcUpdateNavigation();

}

function nbcUpdateNavigation() {
	
	if (nbcStart==0)
		$('#prev-button-container-top,#prev-button-container-bottom').css('visibility','hidden');
	else
		$('#prev-button-container-top,#prev-button-container-bottom').css('visibility','visible');

	if (nbcStart+nbcPerPage>nbcData.count.results)
		$('#next-button-container-top,#next-button-container-bottom').css('visibility','hidden');
	else
		$('#next-button-container-top,#next-button-container-bottom').css('visibility','visible');
}

function nbcSetState(p) {
	
	setCursor('wait');

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : (p && p.clearState) ? 'clear_state' : 'set_state' ,
			state : p.state,
			id : null,
			time : getTimestamp(),
			key : matrixId,
			p : projectId
		}),
		success : function (data) {
			if (p.norefresh!==true)
				nbcGetResults({closeDialog:true,refreshGroups:true});
			setCursor();
		}
	});
	
}

function nbcSetStateValue(state) {
	
	nbcSetState({state:state});
   		
}

function nbcClearStateValue(state) {

	nbcSetState({state:state,clearState:true});
		
}

function getInitialValues() {

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		async : false,
		data : ({
			action : 'get_initial_values' ,
			time : getTimestamp(),
			key : matrixId,
			p : projectId
		}),
		success : function (data) {
			initData = $.parseJSON(data);
			//console.dir(initData);
		}
	});
}

function updateChoicesMade() {

	$('#gemaakte-keuzes').html('');

	var d = Array();

	for (var i in nbcData.menu.storedStates) {

		var state = nbcData.menu.storedStates[i];
		var characterInfo = initData.characterNames[state.characteristic_id].label.split('|');

		d.push('<li><div class="ui-block-a"><a href="#" class="chosen-facet" onclick="nbcClearStateValue(\''+(state.val)+'\');return false;">'+
        	'<div class="grid-iconbox"><div style="color:white;font-style:italic;margin-top:-15px;padding-bottom:5px;" class="grid-labelbox">'+characterInfo[0]+'</div>'+
            '<img alt="" style="top:25px;" class="grid-icon" src="'+initData.stateImageUrls.baseUrl+initData.stateImageUrls.fileNames[state.id].file_name+'">'+
            '<img alt="" style="position:relative;top:-5px;left:0px;margin-left:-73px;" src="'+initData.stateImageUrls.baseUrlSystem+'button-close-shadow-overlay.png">'+
			'</div><div style="margin-top:-5px;" class="grid-labelbox">'+state.label+'</div></a></div></li>');

		if (d.length<nbcData.results.count)
			d.push('<li class="lijn no-text">|</li>');

	}

	$('#gemaakte-keuzes').html(d.join(''));
	
	if (d.length==0)
		$('.sub-header-wrapper').css('display','none');
	else
		$('.sub-header-wrapper').css('display','block');

}

function updateStates(id) {

	$('a[id^="state-"]').addClass('ui-disabled');

	var keys = Object.keys(nbcData.countPerState);
	for (var i in keys) {
		
		$('#state-'+keys[i]).removeClass('ui-disabled');
	}

}

function toonDier(p) {

	allAjaxHandle = $.ajax({
		url : '../species/taxon_overview.php',
		type: 'POST',
		data : ({
			id : p.id,
//			type : p.type,
			back : p.back,
			hotwords: false,
			navigation: false,
			time : getTimestamp()
		}),
		success : function (data) {
			if (data) {
				$('#dier-content').html(data);
				$('#dier-content-wrapper').css('visibility','visible');
				if(jQuery().prettyPhoto)
					nbcPrettyPhotoInit();
			}
		}
	});
}

function verbergDier() {

	$('#dier-content').html('');
	$('#dier-content-wrapper').css('visibility','hidden');

}

function openDiergroep(pId,tId,type) {

	var pre = '<a href="#" class="no-text terug-naar-het-dier" onclick="toonDier('+tId+',\''+type+'\');return false;">Terug naar het dier</a>';

	allAjaxHandle = $.ajax({
		url : '../module/topic.php',
		type: 'POST',
		data : ({
			id : pId,
			style : 'inner',
			time : getTimestamp()
		}),
		success : function (data) {
			if (data) {
				$('#dier-content').html(pre + data);
				$('#dier-content-wrapper').css('visibility','visible');
				if(jQuery().prettyPhoto)
					nbcPrettyPhotoInit();
			}
		}
	});

}

function openDierLink()
{
	if (!startDier) return
	startDier=$('<textarea />').html(startDier).text(); // convert entities to characters
	var n=null;
	for(var i in nbcData.results)
	{
		if (nbcData.results[i].l.toLowerCase()==startDier.toLowerCase())
		{
			n=nbcData.results[i];
			break;
		}
	}

	if (n)
	{
		toonDier({id:n.i,type:n.y});
		for (var j=0;j<Math.floor(i/nbcPerPage);j++)
		{
			navigeren('volgende');
		}
	}
		
}


























