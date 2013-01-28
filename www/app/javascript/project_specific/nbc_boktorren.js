var nbcCharacters = Array();
var nbcStart = 0;
var nbcPerPage = 0;
var nbcData;
var nbcCurrPage = 0;
var nbcLastPage = 0;
var nbcImageRoot;
var nbcPaginate = true;
var nbcPopupSizes = {small:350,medium:425,large:620};

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
				$('#dialog').css('width', (c.type=='media' ? nbcPopupSizes.large : nbcPopupSizes.medium));
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

		}
	});
	
}

function nbcSetPageParameters() {
	nbcPerPage = nbcData.count.perpage;
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
    */

	var photoLabel = data.l+(data.g ? ' <img class="gender" height="17" width="8" src="'+nbcImageRoot+data.g+'.png" title="'+data.g+'" />' : '' );

	return '<div class="result'+(data.h ? ' resultHighlight' : '')+'" id="res-'+data.y+'-'+data.i+'">'+
			'<div class="resultImageHolder">'+
				'<a rel="prettyPhoto[gallery]" href="'+data.m+'" title="'+escape(photoLabel)+'">'+
					'<img class="result" height="207" width="145" src="'+data.m+'" title="'+data.p+'" />'+
				'</a>'+
			'</div>'+
			'<div>'+
				(data.u ? '<a href="'+(data.u)+'" target="_blanc">' : '') +
					(data.s!=data.l ? data.l+'<br />' : '')+
					'<span class="scientificName">'+(data.s)+'</span>'+
					(data.u ? '</a>' : '')  +
			'</div>'+
			(data.g ? '<img class="gender" height="17" width="8" src="'+nbcImageRoot+data.g+'.png" title="'+data.g+'" />' : '' )+
			(data.r ? '<a class="similarBtn" href="#" onclick="nbcShowSimilar('+(data.i)+',\''+(data.t ? 'v' : 't')+'\');" target="_self">gelijkende soorten</a>' : '' ) +
		'</div>';
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
		'<strong style="color:#333">'+(count.results)+'</strong> (van <strong style="color:#777;">'+(count.all)+'</strong>) objecten in huidige selectie'
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
