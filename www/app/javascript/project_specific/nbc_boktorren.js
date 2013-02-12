var nbcStart = 0;
var nbcPerPage = 16;	// default, reset in identify.php
var nbcPerLine = 4;		// default, reset in identify.php
var nbcData;
var nbcFullDatasetCount = 0;
var nbcCurrPage = 0;
var nbcLastPage = 0;
var nbcImageRoot;
var nbcPaginate = true;
var nbcStatevalue = '';
var nbcDetailShowStates = Array();
var nbcSearchTerm = '';
var nbcShownItems = Array();
var nbcLabelShowAll = '';
var nbcLabelHideAll = '';
var nbcLabelClose = '';
var nbcLabelDetails = '';
var nbcLabelBack = '';
var nbcLabelSimilarSpecies = '';

function nbcToggleGroup(id) {

	if ($('#character-group-'+id).css('display')=='none') {
		$('#character-group-'+id).removeClass('hidden').addClass('visible');
		$('#character-item-'+id).removeClass('closed').addClass('open');
	} else {
		$('#character-group-'+id).removeClass('visible').addClass('hidden');
		$('#character-item-'+id).removeClass('open').addClass('closed');
	}
	
} 

// dialog button function, called from main.js::showDialog 
function jDialogOk() {

	nbcSetStateValue();

}

// dialog button function, called from main.js::showDialog 
function jDialogCancel() {

	closeDialog();

}

function nbcShowStates(id) {

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_formatted_states' ,
			id : id , 
			time : getTimestamp()
		}),
		success : function (data) {
			//alert(data);
			data = $.parseJSON(data);
			showDialog(
				data.character.label,
				data.page,
				{width:data.width,height:data.height,showOk:data.showOk}
			);
		}
	});

}

function nbcGetResults(p) {

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_results_nbc',
			params : p,
			time : getTimestamp(),
		}),
		success : function (data) {
			//alert(data);
			nbcData = $.parseJSON(data);
			nbcProcessResults();
			if (p && p.action=='similar') nbcPrintSimilarHeader();
			if (p && p.closeDialog==true) jDialogCancel();
			if (p && p.refreshGroups==true) nbcRefreshGroupMenu();
		}
	});
	
}

function nbcProcessResults(resetStart) {
	if (resetStart!==false) nbcStart = 0;
	nbcClearResults();
	nbcClearOverhead();
	nbcClearPaging();
	if (nbcData.results) nbcPrintResults();
	if (nbcData.count) nbcPrintOverhead(); 
	if (nbcData.count) nbcPrintPaging();
	//nbcResetClearButton(); // now called in nbcPrintResults()
}

function nbcResetClearButton() {

	if (nbcData.paramCount==0) {
		$('#clearSelectionContainer').removeClass('ghosted').addClass('ghosted');
	} else {
		$('#clearSelectionContainer').removeClass('ghosted');
	}

}

function nbcClearResults() {
	$('#results-container').html('');
}

function nbcFormatResult(data) {

	/*
		data.
			i = id 
			t = taxon id (absent when not a variation) 
			y = type: t(axon) or v(ariation)
			l = label 
			g = gender (absent when not a variation)
			s = scientific name 
			m = image url 
			p = photographer credit 
			u = remote url
			r = number of similars
			h = highlight (bool)
			d = full species details (only when comparing or resultset has only one taxon/variation)
    */

	var photoLabel = data.l+(data.g ? ' <img class="gender" height="17" width="8" src="'+nbcImageRoot+data.g+'.png" title="'+data.g+'" />' : '' );
	
	var id = data.y+'-'+data.i;

	if (data.d) {

		var states = Array();
		
		for(var i in data.d) {
			
			if (data.d[i].characteristic.indexOf('|')!=false) {
				var t = data.d[i].characteristic.split('|');
				t = t[0];
			} else {
				var t = data.d[i].characteristic;
			}
			
			states.push('<span class="resultDetailLabel">'+t +':</span> <span class="resultDetailValue">'+data.d[i].state.label+'</span>');
		}
		
		nbcShownItems.push(id);

	}

	return '<div class="result'+(data.h ? ' resultHighlight' : '')+'" id="res-'+id+'">'+
			'<div class="resultImageHolder">'+
				'<a rel="prettyPhoto[gallery]" href="'+data.m+'" title="'+escape(photoLabel)+'">'+
					'<img class="resultImageHolder" src="'+data.m+'" title="'+data.p+'" />'+
				'</a>'+
			'</div>'+
			'<div class="scientificNameHolder">'+
				(data.u ? '<a href="'+(data.u)+'" target="_blank">' : '') + (data.s!=data.l ? data.l+'<br />' : '')+
					'<span class="scientificName">'+(data.s)+'</span>'+
				(data.u ? '</a>' : '')  +
			'</div>'+
			(data.g ? '<img class="gender" src="'+nbcImageRoot+data.g+'.png" title="'+data.g+'" />' : '' )+
			(data.r ? '<a class="similarBtn" href="#" onclick="nbcShowSimilar('+(data.i)+',\''+(data.t ? 'v' : 't')+'\');return false;" target="_self">'+nbcLabelSimilarSpecies+'</a>' : '' ) +
			(states ? 
				'<span>'+
					'<a id="tog-'+id+'" style="position:relative;top:'+(data.r ? '-15' : '0')+'px" href="#" onclick="nbcToggleSpeciesDetail(\''+id+'\');return false;">'+nbcLabelDetails+'</a>'+
				'</span>' +
				'<div id="det-'+id+'" class="resultDetails">'+
					'<ul>'+
						'<li>'+states.join('</li><li>')+'</li>'+
					'</ul>'+
				'</div>' 
				: '')+
		'</div>';
	
}

function nbcToggleSpeciesDetail(id) {

	nbcDetailShowStates[id] = nbcDetailShowStates[id] ? !nbcDetailShowStates[id] : true;
	
	$('#det-'+id).css('display',(nbcDetailShowStates[id] ? 'block' : 'none'));
	$('#tog-'+id).html(nbcDetailShowStates[id] ? nbcLabelClose : nbcLabelDetails);
	
}

function nbcToggleAllSpeciesDetail() {
	
	for (var i in nbcShownItems)
		nbcToggleSpeciesDetail(nbcShownItems[i]);

	if ($('#showAllLabel').html()==nbcLabelShowAll)
		$('#showAllLabel').html(nbcLabelHideAll);
	else
		$('#showAllLabel').html(nbcLabelShowAll);

}

function nbcPrintResults() {

	var results = nbcData.results;
	var s = '';
	var d = 0;

	s = '<div class="resultRow">';

	for(var i=0;i<results.length;i++) {
		if ((i>=nbcStart && i<nbcStart+nbcPerPage) || nbcPaginate==false) {
			s = s + nbcFormatResult(results[i]);
			if (++d==nbcPerLine) {
				s = s + '</div><br/><div class="resultRow">';
				d=0;
			}
		}
	}

	s = s + '</div>';

	$('#results-container').html(s);

	nbcPrettyPhotoInit();
	nbcResetClearButton();	
	
}

function nbcPrettyPhotoInit() {

 	$("a[rel^='prettyPhoto']").prettyPhoto({
 		opacity: 0.70, 
		show_title: false,
 		overlay_gallery: false,
 		social_tools: false
 	});

}

function nbcClearOverhead() {
	$('#result-count').html('');
	$('#similarSpeciesHeader').removeClass('visible').addClass('hidden');
	$('#similarSpeciesHeader').html('');
}

function nbcClearPaging() {
	$('#paging-header').html('');	
	$('#paging-footer').html('');	
}

function nbcPrintOverhead() {

	var count = nbcData.count;
	
	nbcFullDatasetCount = (nbcFullDatasetCount==0) ? count.all : nbcFullDatasetCount;
	
	$('#result-count').html(
		sprintf(
			'<strong style="color:#333">%s</strong> %s',
			count.results,
			sprintf(
				_('(van %s) objecten in huidige selectie'),
					sprintf(
						'<strong style="color:#777;">%s</strong>',
						nbcFullDatasetCount
					)
				)
			)
		);

}

function nbcPrintPaging() {

	var count = nbcData.count;

	nbcLastPage = Math.ceil(count.results / nbcPerPage);
	nbcCurrPage = Math.floor(nbcStart / nbcPerPage);

	if (nbcLastPage > 1 && nbcCurrPage!=0)
		$("#paging-header").append('<li><a href="#" onclick="nbcBrowse(\'p\');return false;">&lt;&lt;</a></li>');
	
	if (nbcLastPage>1) { 
	
		for (var i=0;i<nbcLastPage;i++) {
	
			if (i==nbcCurrPage)
				$("#paging-header").append('<li><strong>'+(i+1)+'</strong></li>');
		    else
				$("#paging-header").append('<li><a href="#" onclick="nbcBrowse('+i+');return false;">'+(i+1)+'</a></li>');
	
		}
		
	}

	if (nbcLastPage > 1 && nbcCurrPage<nbcLastPage-1)
		$("#paging-header").append('<li><a href="#" onclick="nbcBrowse(\'n\');return false;" class="last">&gt;&gt;</a></li>');

	$("#paging-footer").html($("#paging-header").html());
}

function nbcSaveSessionSetting(name,value) {

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'save_session_setting' ,
			setting : { name : name, value: value },
			id : null,
			time : getTimestamp()
		}),
		success : function (data) {
			//alert(data);
		}
	});
	
}

function nbcBrowse(id) {

	if (id=='n')
	    nbcStart = nbcStart+nbcPerPage;
	else 
	if (id=='p')
    	nbcStart = nbcStart-nbcPerPage;
	else
		nbcStart = id * nbcPerPage;
			
	nbcSaveSessionSetting('nbcStart',nbcStart);
	nbcClearResults();
	nbcPrintResults();
	nbcClearPaging();
	nbcPrintPaging();

}

function nbcSetPaginate(state) {

	nbcPaginate = state;
	
}

function nbcShowSimilar(id,type) {

	nbcSetPaginate(false);
	nbcGetResults({action: 'similar', id: id, type: type});
	nbcSaveSessionSetting('nbcSimilar',[id,type]);
	
}

function nbcCloseSimilar() {

	nbcSetPaginate(true);
	nbcGetResults();
	nbcSaveSessionSetting('nbcSimilar');
	
}

function nbcPrintSimilarHeader() {

	var label = nbcData.results[0].l;

	$('#similarSpeciesHeader').html(
		sprintf(_('Gelijkende soorten van %s'),'<span id="similarSpeciesName">'+label+'</span>')+
		'<br />'+
		'<a class="clearSimilarSelection" href="#" onclick="nbcCloseSimilar();return false;">'+nbcLabelBack+'</a>'+
		' | '+
		'<a class="clearSimilarSelection" href="#" onclick="nbcToggleAllSpeciesDetail();return false;" id="showAllLabel">'+nbcLabelShowAll+'</a>'
	);
	$('#similarSpeciesHeader').removeClass('hidden').addClass('visible');
}

function nbcSetState(p) {
	
	nbcSetPaginate(true);

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : (p && p.clearState) ? 'clear_state' : 'set_state' ,
			state : p.state,
			value : p.value,
			id : null,
			time : getTimestamp()
		}),
		success : function (data) {
			if (p.norefresh!==true)
				nbcGetResults({closeDialog:true,refreshGroups:true,});
		}
	});
	
}

function nbcSetStateValue(state) {
	
	var state = state ? state : $('#state-id').html();

	nbcSetState({state:state,value:nbcStatevalue});
		
}

function nbcClearStateValue(state) {

	$('#state-value').val('');
	nbcSetState({state:state,clearState:true});
		
}

function nbcRefreshGroupMenu() {

	if (nbcData.menu) nbcBuildGroupMenu(nbcData.menu);
	
}


function nbcBuildGroupMenu(data) {

	$('#facet-categories-menu').html('');
	
	var d = Array();

	for (var i in data.groups) {

		var v = data.groups[i];
		var openGroup = false;

		var s = 
			'<li id="character-item-'+v.id+'" class="closed"><a href="#" onclick="nbcToggleGroup('+v.id+');return false;">'+v.label+'</a></li>'+
			'<ul id="character-group-'+v.id+'" class="hidden">';			

		for (var j in v.chars) {

			var c = data.groups[i].chars[j];

			s = s + '<li class="inner'+(j==(v.chars.length-1) ? ' last' : '')+'"><a class="facetLink" href="#" onclick="nbcShowStates('+c.id+');return false;">'+c.label+(c.value ? ' '+c.value : '')+'</a>';
			
			if (data.activeChars[c.id]) {
				openGroup = true;
				s = s + '<span>';
				for (k in data.storedStates) {
					var state = data.storedStates[k];
					if (state.characteristic_id==c.id) {
						var dummy = state.type=='f' ? state.type+':'+state.characteristic_id : state.val;
						s = s + 
							'<div class="facetValueHolder">'+
								(state.value ? state.value+' ' : '')+
								(state.label ? state.label+' ' : '')+
								(state.separationCoefficient ? ' ('+state.separationCoefficient+') ' : '')+
								'<a href="#" class="removeBtn" onclick="nbcClearStateValue(\''+dummy+'\');return false;">'+
								'<img src="'+nbcImageRoot+'clearSelection.gif">'+
								'</a>'+
							'</div>';

					}
				}
				
				s = s + '</span>';

			}

			s = s  +'</li>';

		}

		s = s  +'</ul>';
		
		if (openGroup)
			s = s + '<script> \n nbcToggleGroup('+v.id+'); \n </script>';
				
		d.push(s);
	}
	
	$('#facet-categories-menu').html('<ul>'+d.join('\n')+'</ul>');

}

function nbcBindDialogKeyUp() {

	$('#state-value').keyup(function(e) {
		if (e.keyCode==13) {
			// return
			nbcSetStateValue();
		}
		return;
	});

}

function nbcClearSearchTerm() {
	nbcSearchTerm='';
	$('#inlineformsearchInput').val('');
}

function nbcDoSearch() {

	var str = $('#inlineformsearchInput').val().trim();
	
	if ((str.length==0) || (str==nbcSearchTerm)) return false;
	
	nbcSearchTerm=str;
	nbcSetPaginate(true);
	nbcSetState({norefresh:true,clearState:true});

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'do_search',
			params : {term: nbcSearchTerm},
			time : getTimestamp(),
		}),
		success : function (data) {
			//alert(data);
			nbcData = $.parseJSON(data);
			nbcProcessResults();
			nbcPrintSearchHeader();
			nbcSaveSessionSetting('nbcSearch',nbcSearchTerm);
		}
	});

	return false; // necessary to suppress submit of form

}

function nbcPrintSearchHeader() {

	$('#similarSpeciesHeader').html(
		sprintf(_('Zoekresultaten voor %s'),'<span id="searchedForTerm">'+nbcSearchTerm+'</span>')+'<br />'+
		'<a class="clearSimilarSelection" href="#" onclick="nbcCloseSearch();return false;">'+nbcLabelBack+'</a>'
	);
	$('#similarSpeciesHeader').removeClass('hidden').addClass('visible');
}

function nbcCloseSearch() {

	nbcGetResults();
	nbcSaveSessionSetting('nbcSearch');
	$('#inlineformsearchInput').val('');

}

function nbcInit() {

	nbcLabelClose = _('sluiten');
	nbcLabelDetails = _('onderscheidende kenmerken');
	nbcLabelBack = _('terug');
	nbcLabelSimilarSpecies = _('gelijkende soorten');
	nbcLabelShowAll = _('alle kenmerken tonen');
	nbcLabelHideAll = _('alle kenmerken verbergen');

}






























