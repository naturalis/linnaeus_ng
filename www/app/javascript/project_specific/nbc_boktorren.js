var nbcCharacters = Array();
var nbcStart = 0;
var nbcPerPage = 0;
var nbcData;
var nbcCurrPage = 0;
var nbcLastPage = 0;

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

				$('#dialog').css('width', (c.type=='media' ? 610 : 425));
				$.modaldialog.reinitPosition({top:175});

			}
		});

	}

}

function nbcGetResults() {

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_results_nbc' ,
			time : getTimestamp()
		}),
		success : function (data) {
			nbcData = $.parseJSON(data);
			nbcSetPageParameters();
			nbcClearResults();
			nbcClearOverhead();
			nbcClearPaging();
			if (nbcData.results) nbcPrintResults();
			if (nbcData.count) nbcPrintOverhead(); 
			if (nbcData.count) nbcPrintPaging();
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
			l = label 
			g = gender (absent when not a variation)
			s = scientific name 
			m = image url 
			p = photographer credit 
			u = remote url
    */

	var scientificName = (data.s!=data.l) ? data.s : '';
	var photoLabel = data.l+(data.g ? ' <img class="gender" height="17" width="8" src="http://determinatie.nederlandsesoorten.nl/images/'+data.g+'.png" title="'+data.g+'" />' : '' );

	return '<div class="result">'+
			'<div class="resultImageHolder">'+
				'<a rel="prettyPhoto[gallery]" href="'+data.m+'" title="'+escape(photoLabel)+'">'+
					'<img class="result" height="207" width="145" src="'+data.m+'" title="'+data.p+'" />'+
				'</a>'+
			'</div>'+
			'<div>'+
				(data.u ? '<a href="'+(data.u)+'" target="_blanc">' : '') +
					(data.l)+'<br />'+
					'<span class="scientificName">'+(scientificName)+'</span>'+
					(data.u ? '</a>' : '')  +
			'</div>'+
			(data.g ? '<img class="gender" height="17" width="8" src="http://determinatie.nederlandsesoorten.nl/images/'+data.g+'.png" title="'+data.g+'" />' : '' )+
			'<a class="similarBtn" href="" target="_self">'+
				'gelijkende soorten'+
			'</a>'+
		'</div>';
}


function nbcPrintResults() {

	var results = nbcData.results;
	var s = '';
	
	for(var i=0;i<results.length;i++) {
		if (i>=nbcStart && i<nbcStart+nbcPerPage) {
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
	
	for (var i=0;i<nbcLastPage;i++) {

		if (i==nbcCurrPage)
			$("#paging-header").append('<li><strong>'+(i+1)+'</strong></li>');
	    else
			$("#paging-header").append('<li><a href="#" onclick="nbcBrowse('+i+')">'+(i+1)+'</a></li>');

	}

	if (nbcLastPage > 1 && nbcCurrPage<nbcLastPage-1)
		$("#paging-header").append('<li><a href="#" onclick="nbcBrowse(\'n\')">&gt;&gt;</a></li>');

	$("#paging-footer").html($("#paging-header").html());
}

function nbcBrowse(id) {

	if (id=='n') {
	    nbcStart = nbcStart+nbcPerPage;
	} else 
	if (id=='p') {
    	nbcStart = nbcStart-nbcPerPage;
	} else {
		nbcStart = id * nbcPerPage;
	}
			
	nbcClearResults();
	nbcPrintResults();
	nbcClearPaging();
	nbcPrintPaging();

}
