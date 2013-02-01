var nbcCharacters = Array();
var nbcStart = 0;
var nbcPerPage = 0;
var nbcData;
var nbcCurrPage = 0;
var nbcLastPage = 0;
var nbcImageRoot;
var nbcPaginate = true;
var nbcPopupWidths = {small:350,medium:425,large:620};
var nbcStatevalue = String;

function nbcToggleGroup(id) {

	if ($('#character-group-'+id).css('display')=='none') {
		$('#character-group-'+id).removeClass('hidden').addClass('visible');
		$('#character-item-'+id).removeClass('closed').addClass('open');
	} else {
		$('#character-group-'+id).removeClass('visible').addClass('hidden');
		$('#character-item-'+id).removeClass('open').addClass('closed');
	}
	
} 

function nbcAddCharacter(c) {

	nbcCharacters[c.id] = c;
	
}

function nbcShowStates(id) {

	var c = nbcCharacters[id];

	if (c) {

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
				showDialog(c.label,data);
				$('#dialog').css('width', (c.type=='media' ? nbcPopupWidths.large : nbcPopupWidths.medium));
				$.modaldialog.reinitPosition({top:175,height:400});

			}
		});

	}

}

function nbcProcessResults() {
	
	nbcSetPageParameters();
	nbcClearResults();
	nbcClearOverhead();
	nbcClearPaging();
	if (nbcData.results) nbcPrintResults();
	if (nbcData.count) nbcPrintOverhead(); 
	if (nbcData.count) nbcPrintPaging();	

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
			if (p && p.closeDialog==true) closeDialog();
			if (p && p.refreshGroups==true) nbcRefreshGroupMenu();
		}
	});
	
}

function nbcSetPageParameters() {
	nbcPerPage = nbcData.count.perpage;
}

function nbcClearResults() {
	$('#results-container').html('');
}

var tplNBCResult;

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
	
	if (data.d) {

		var states = Array();
		
		for(var i in data.d) {
			
			if (data.d[i].characteristic.indexOf('|')!=false) {
				var t = data.d[i].characteristic.split('|');
				t = t[0];
			} else {
				var t = data.d[i].characteristic;
			}
			
			states.push('<i>'+t +'</i>: '+data.d[i].state.label);
		}

	}

	var id = data.y+'-'+data.i;
	
	return '<div class="result'+(data.h ? ' resultHighlight' : '')+'" id="res-'+id+'">'+
			'<div class="resultImageHolder">'+
				'<a rel="prettyPhoto[gallery]" href="'+data.m+'" title="'+escape(photoLabel)+'">'+
					'<img class="result" height="207" width="145" src="'+data.m+'" title="'+data.p+'" />'+
				'</a>'+
			'</div>'+
			'<div style="min-height:50px">'+
				(data.u ? '<a href="'+(data.u)+'" target="_blank">' : '') +
					(data.s!=data.l ? data.l+'<br />' : '')+
					'<span class="scientificName">'+(data.s)+'</span>'+
					(data.u ? '</a>' : '')  +
			'</div>'+
			(data.g ? '<img class="gender" height="17" width="8" src="'+nbcImageRoot+data.g+'.png" title="'+data.g+'" />' : '' )+
			(data.r ? '<a class="similarBtn" href="#" onclick="nbcShowSimilar('+(data.i)+',\''+(data.t ? 'v' : 't')+'\');" target="_self">gelijkende soorten</a>' : '' ) +
			(states ?
'<div id="det-'+id+'" style="padding:1px;background:#fff;border:1px dotted black;width:169px;display:none;z-index:999;left:-14px"><div style="width:160px;"><ul style="margin:0;padding:0px 0px 0px 15px;"><li>'+
states.join('</li><li>')+'</li></ul></div><a href="#" onclick="veryTemporary(\''+id+'\',\'off\');">x</a></div><span id="tog-'+id+'">'+
'<a style="position:relative;top:'+(data.r ? '-15' : '0')+'px" href="#" onclick="veryTemporary(\''+id+'\',\'on\');">details</a></span>' : '')+
		'</div>'+
		'</div>';
	
}

function veryTemporary(id,state) {

	$('#det-'+id).css('display',(state=='on' ? 'block' : 'none'));
	$('#tog-'+id).css('display',(state=='off' ? 'block' : 'none'));

}

function nbcPrintResults() {
	
	var results = nbcData.results;
	var s = '';

	for(var i=0;i<results.length;i++) {
		if ((i>=nbcStart && i<nbcStart+nbcPerPage) || nbcPaginate==false) {
			s = s + nbcFormatResult(results[i]);
		}
	}

	$('#results-container').html(s);

	nbcPrettyPhotoInit();	
	
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

	$('#result-count').html(
		sprintf(
			'<strong style="color:#333">%s</strong> %s',
			count.results,
			sprintf(
				_('(van %s) objecten in huidige selectie'),
					sprintf(
						'<strong style="color:#777;">%s</strong>',
						count.all
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
		$("#paging-header").append('<li><a href="#" onclick="nbcBrowse(\'p\')">&lt;&lt;</a></li>');
	
	if (nbcLastPage>1) { 
	
		for (var i=0;i<nbcLastPage;i++) {
	
			if (i==nbcCurrPage)
				$("#paging-header").append('<li><strong>'+(i+1)+'</strong></li>');
		    else
				$("#paging-header").append('<li><a href="#" onclick="nbcBrowse('+i+')">'+(i+1)+'</a></li>');
	
		}
		
	}

	if (nbcLastPage > 1 && nbcCurrPage<nbcLastPage-1)
		$("#paging-header").append('<li><a href="#" onclick="nbcBrowse(\'n\')">&gt;&gt;</a></li>');

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
		'<label>Gelijkende soorten van: '+label+'</label>'+
		'<a class="clearSimilarSelection" href="#" onclick="nbcCloseSimilar()">&lt;&lt; terug</a>'
	);
	$('#similarSpeciesHeader').removeClass('hidden').addClass('visible');
}

function nbcSetState(p) {

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
			//alert(data);
			if (p.norefresh!==false)
				nbcGetResults({closeDialog:true,refreshGroups:true,});
		}
	});
	
}

function nbcSetStateValue(state) {
	
	var state = state ? state : $('#state-id').val();

	nbcSetState({state:state,value:nbcStatevalue});
		
}

function nbcClearStateValue(state) {

	$('#range-value').val('');
	nbcSetState({state:state,clearState:true});
		
}

function nbcRefreshGroupMenu() {

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_groups' ,
			time : getTimestamp()
		}),
		success : function (data) {
			//alert(data);
			data = $.parseJSON(data);
			if (data.groups) nbcBuildGroupMenu(data);
		}
	});

	
}

function nbcBuildGroupMenu(data) {

	$('#facet-categories-menu').html();
	
	var d= Array();
	
	//console.dir(data);
	
	for (var i in data.groups) {
		var v = data.groups[i];
		var openGroup = false;
		var s = 
			'<li id="character-item-'+v.id+'" class="closed"><a href="#" onclick="nbcToggleGroup('+v.id+')">'+v.label+'</a>'+
				'<ul id="character-group-'+v.id+'" class="facets hidden">';

		for (var j in v.chars) {
			var c = data.groups[i].chars[j];
			var foo = c.label.split('|')
			if (foo[0] && foo[1]) {
				var cLabel = foo[0];
				var cText = foo[1]
			} else {
				var cLabel = c.label
				var cText = '';
			}
			
			s = s + '<li><a class="facetLink" href="#" onclick="nbcShowStates('+c.id+')">'+cLabel+' '+c.value+'</a>';
			
			if (data.activeChars[c.id]) {
				openGroup = true;
				s = s + '<span>';
				var cK = 0;
				for (k in data.storedStates) {
					var s = data.storedStates[k];
					if (s.characteristic_id==c.id) {
						s = s + 
							'<div class="facetValueHolder">'+s.value+' '+s.label+
							'<a href="#" class="removeBtn" onclick="$(\'#action2\').val(\'clear\');$(\'#id2\').val(\''+cK+'\');$(\'#form2\').submit();">'+_('(deselecteer)')+'</a></div>';
					}
				}
			}
			
			if (openGroup)
				s = s + '<script> \n nbcToggleGroup('+v.id+'); \n </script>';

		}
				
		d.push(s);
	}
	
	$('#facet-categories-menu').html(d.join('\n'));

}
