var taxonActiveView = false;
var taxonActivePageTitle = false;
var taxonActiveLanguage = false;
var taxonActiveTaxonId = false;
var taxonNewLanguage = false;
var taxonDefaultLanguage = false;
var taxonLanguages = Array();
var taxonActivePage = false;
var taxonPages = Array();
var taxonPageStates = Array();
var taxonPublishState = false;
var taxonInitAutoSave = true;
var taxonSaveType = 'auto';
var taxonTargetDiv = false;
var taxonTaxonParent = Array();
var taxonRanks = Array();
var taxonCanHaveHybrid = Array();
var taxonExecAfterSave = false;

//GENERAL
function taxonGeneralDeleteLabels(id,action,name,itm) {

	if (!allDoubleDeleteConfirm(itm,name)) return;

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : action ,
			'id' : id , 
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			$('#theForm').submit();
		}
	});

}


//HEARTBEAT & USAGE
function taxonSetHeartbeat(userid,app,ctrllr,view) {

	var params = Array();

	params[0] = ['taxon_id',$('#taxon_id').val()];

	heartbeatParams = params;

	allSetHeartbeat(userid,app,ctrllr,view);

}

function taxonClearAllUsageCells() {

	$("td[id*='usage']").html('');

}

function taxonCheckLockOutStates() {

	$.ajax({
		url : "../utilities/ajax_interface.php",
		type: "GET",
		data : ({
			'action' : 'get_taxa_edit_states',
			'time': allGetTimestamp()
		}),
		success : function (data) {
			taxonClearAllUsageCells();
			if (data) {
				obj = $.parseJSON(data);
				for(var i=0;i<obj.length;i++) {
					if (obj[i].first_name.length > 0 || obj[i].last_name.length > 0) {
						$('#usage-'+obj[i].taxon_id).html(obj[i].first_name+' '+obj[i].last_name);
					}
				}
			}
		}
	});

	setTimeout ("taxonCheckLockOutStates()", heartbeatFreq/2);

}

//CATEGORIES
function taxonPageDelete(page,name) {

	taxonGeneralDeleteLabels(page,'delete_page',name,'the page');

}

function taxonSaveEditedTaxonName(id) {
	
	var newName = $('#edit'+id).val();

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'taxon_id' : id ,
			'taxon_name' : newName ,
			'action' : 'save_taxon_name',
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			if(data=='<ok>') {
				allSetMessage('saved');
			}
		}
	})

	$('#namecell'+id).html(
		'<span onclick="taxonEditTaxonName('+id+')" id="name'+id+'" class="pseudo-a">'+
		newName+'</span>'
	);

}

function taxonEditTaxonName(id) {

	$('#namecell'+id).html(
		'<input class="taxonNameEdit" id="edit'+id+'" value="'+$('#name'+id).html()+'" />'+
		'<input type="button" value="save" onclick="taxonSaveEditedTaxonName('+id+')" />'
	);

}

function taxonAddLanguage(lan) {
	//[id,name,default?]
	taxonLanguages[taxonLanguages.length] = lan;
	
	if (lan[2]==1) taxonDefaultLanguage = lan[0];

}

function taxonAddPage(page) {
	//[id,[names],default?]
	// names[-1] contains the system label, [x] the name in language x
	taxonPages[taxonPages.length] = page;
}


function taxonDrawTaxonLanguages(fnc,includeDef) {

	fnc = fnc || 'taxonSwitchLanguage';

	var buffer = '';

	for (var i=0;i<taxonLanguages.length;i++) {

		if (taxonLanguages[i][2]!=1 || includeDef==true) {
			buffer = buffer+
				'<span class="rank-language'+
				(taxonLanguages[i][0]==taxonActiveLanguage ? 
					'-active"' : 
					'" class="pseudo-a" onclick="'+fnc+'('+taxonLanguages[i][0]+');' 
				)+
				'">'+
				taxonLanguages[i][1]+
				'</span>&nbsp;';
		}
		
		if (taxonLanguages[i][0]==taxonDefaultLanguage) var def = taxonLanguages[i][1];
	}

	$('#taxon-language-div').html(buffer);

	$('#taxon-language-div-default').html(def+' *');

}

function taxonDrawPageBlock() {

	buffer = '<table class="taxon-pages-table"><tr>';

	for (var i=0;i<taxonPages.length;i++) {

		buffer = buffer+
			'<td class="taxon-page-cell' +
			(taxonPages[i][0]==taxonActivePage ? '-active' : '" onclick="taxonSwitchPage('+taxonPages[i][0]+');' ) +
			'">' +
			taxonPages[i][1][-1]+
			(taxonPages[i][2]==1 ?  ' *' : '') +
			'<br /><span class="taxon-page-publish-state">' +
			(taxonPageStates[taxonPages[i][0]]==1 ? 'published' : (taxonPageStates[taxonPages[i][0]] == 0 ? 'unpublished' : 'empty')) +
			'</span></td>';
	}

	buffer = buffer + '</tr></table>';

	$('#taxon-pages-table-div').html(buffer);

}

function taxonUpdatePageBlock() {

	$.ajax({
		url: "ajax_interface.php",
		data: {
			'id' : $('#taxon_id').val() ,
			'action' : 'get_page_states' ,
			'language' : taxonActiveLanguage ,
			'time' : allGetTimestamp()
		},
		success:
			function(data){
				for(var i=0;i<taxonPageStates.length;i++) {
					taxonPageStates[i]=-1;
				}
				if (data) {
					obj = $.parseJSON(data);
					for(var i=0;i<obj.length;i++) {
						taxonPageStates[obj[i].page_id] = obj[i].publish;
					}
				}
				taxonDrawPageBlock();
			}
			
		}
	);

}

function taxonGetDataAll() {

	allShowLoadingDiv();
	taxonGetDataActive(false);
	taxonGetDataDefault(true);

}

function taxonGetDataActive(updateInterface) {

	if (taxonActiveTaxonId.length==0 || taxonActiveLanguage===false) return;

	taxonGetData(
		taxonActiveTaxonId,
		taxonActiveLanguage,
		taxonActivePage,
		'taxon-content-active',
		updateInterface
	);

}

function taxonGetDataDefault(updateInterface) {

	if (taxonActiveTaxonId.length==0) return;

	taxonGetData(
		taxonActiveTaxonId,
		taxonDefaultLanguage,
		taxonActivePage,
		'taxon-content-default',
		updateInterface
	);

}

function taxonSwitchLanguage(language) {

	taxonSaveDataActive();
	taxonActiveLanguage = language;
	taxonGetDataActive(true);

}

function taxonSwitchPage(page) {

	taxonSaveDataAll();
	taxonActivePage = page;
	taxonGetDataAll();

}

function taxonSaveDataAll() {
	
	taxonSaveDataActive();
	taxonSaveDataDefault();

}

function taxonSaveDataManual() {

	taxonSaveType = 'manual';
	taxonSaveDataAll();

}

function taxonClose() {

	taxonSaveDataActive();
	taxonExecAfterSave = "window.open('list.php','_self');";
	taxonSaveDataDefault();

}

function taxonSaveDataActive() {

	if (taxonActiveTaxonId.length==0 || taxonActiveLanguage===false) return;

	taxonSaveData(
		taxonActiveTaxonId,
		taxonActiveLanguage,
		taxonActivePage,
		tinyMCE.get('taxon-content-active').getContent(),
		'taxon-content-active'
	);

}

function taxonSaveDataDefault() {

	if (taxonActiveTaxonId.length==0) return;

	taxonSaveData(
		taxonActiveTaxonId,
		taxonDefaultLanguage,
		taxonActivePage,
		tinyMCE.get('taxon-content-default').getContent(),
		'taxon-content-default'
	);

}


function taxonGetData(id,language,page,editorName,updateInterface) {

	$.ajax({
		url : "ajax_interface.php" ,
		type: "POST",
		data : ({
			'action' : 'get_taxon' ,
			'id' : id ,
			'language' : language ,
			'page' : page ,
			'time' : allGetTimestamp()			
		}),
		success : function (data) {
			//alert(data);
			obj = $.parseJSON(data);

			$('#taxon-name-input').val(obj.title ? obj.title : '');
			tinyMCE.get(editorName).setContent(obj.content ? obj.content : '');
			taxonActivePage = obj.page_id;
			taxonPublishState = obj.publish;

			if (language!=taxonDefaultLanguage) {
				taxonActiveLanguage = obj.language_id;
			}

			if (updateInterface) {
				taxonDrawTaxonLanguages();
				taxonUpdatePageBlock();
				taxonDrawPublishBlock();
				allHideLoadingDiv();
			}
		}
	});

}

function taxonSaveData(id,language,page,content,editorName) {

	$.ajax({
		url : "ajax_interface.php",
		data : ({
			'action' : 'save_taxon' ,
			'id' : id ,
			'content' : content , 
			'language' : language ,
			'page' : page ,
			'save_type' : taxonSaveType ,
			'time' : allGetTimestamp()	
		}),
		type: "POST",
		success: function(data){

			if (data.indexOf('<error>')>=0) {

				alert(data.replace('<error>',''));

			} else {

				obj = $.parseJSON(data);

				if (obj) {

					if (obj.modified==true) {
						if (taxonSaveType=='manual') tinyMCE.get(editorName).setContent(obj.content ? obj.content : '');
						allSetMessage('saved (could not save certain HTML-tags)');
					} else {
						allSetMessage('saved');
					}

				}
			}

			taxonSaveType = 'auto';

		},
		complete: function(){

			if (taxonExecAfterSave) eval(taxonExecAfterSave);

		}
	});

}

function taxonDeleteData() {

	if (taxonActiveTaxonId.length==0) return;

	if (!allDoubleDeleteConfirm('all content in all languages for taxon',$('#taxon-name').val())) return;

	$.post(
		"ajax_interface.php", 
		{
			'id' : taxonActiveTaxonId ,
			'action' : 'delete_taxon' ,
			'page' : taxonActivePage ,
			'time': allGetTimestamp()	
		},
		function(data){
			window.open('list.php','_self');
		}
	);

}







function taxonPublishContent(state) {
	
	if (taxonActiveTaxonId.length==0) return;

	if (state==1) taxonSaveDataAll();

	$.ajax({
		url : "ajax_interface.php",
		data : ({
			'id' : taxonActiveTaxonId ,
			'action' : 'publish_content' ,
			'language' : taxonActiveLanguage ,
			'page' : taxonActivePage ,
			'state' : state ,
			'time' : allGetTimestamp()	
		}),
		type: "GET",
		success: function(data){
			if (data=='<ok>') {
				taxonPublishState = state;
				taxonDrawPublishBlock();
				taxonUpdatePageBlock();
			}
		}
	});

}

function taxonDrawPublishBlock() {

	if (taxonPublishState=='1')
		$('#taxon-publish-table-div').html(
			'This page has been published. '+
			'<input type="button" value="unpublish" onclick="taxonPublishContent(0);" />'
		);
	else
		$('#taxon-publish-table-div').html(
			'This page has not been published. '+
			'<input type="button" value="publish" onclick="taxonPublishContent(1);" />'
		);

}





function taxonRunAutoSave() {

	if (!taxonInitAutoSave) taxonSaveTaxonContent();

	taxonInitAutoSave = false;

	setTimeout("taxonRunAutoSave()", autosaveFreq);

}

function taxonGetUndo() {

	$.post(
		"ajax_interface.php", 
		{
			'id' : $('#taxon_id').val() ,
			'action' : 'get_taxon_undo' ,
			'language' : taxonActiveLanguage ,
			'page' : taxonActivePage ,
			'time' : allGetTimestamp()			
		},
		function(data){
			if (data) {
				obj = $.parseJSON(data);
				$('#taxon-name-input').val(obj.title ? obj.title : '');
				tinyMCE.get('taxon-content-other').setContent(obj.content ? obj.content : '');
				taxonActiveLanguage = obj.language_id;
				taxonActivePage = obj.page_id;
				taxonPublishState = obj.publish;
				taxonDrawTaxonLanguages();
				taxonUpdatePageBlock();
				taxonDrawPublishBlock();
				allSetMessage('recovered');
			} else {
				allSetMessage('cannot undo');
			}
		}
	);

}











//CATALOGUE OF LIFE
var taxonCoLSingleLevel = false;

function taxonCoLMakeTableRow(d,symbol,level) {

	var x = '';

	if (level) {
		for(var i=0;i<level;i++) {
			x = x + symbol;
		}
	} else {
		x = symbol;
	}

	t = '<tr><td>'+x+'</td><td><span id="rank-'+d.id+'">'+d.rank+'</span>:</td><td>';

	if (d.rank.toLowerCase()=='genus' || 
		d.rank.toLowerCase()=='species' || 
		d.rank.toLowerCase()=='infraspecies') {
	
		t = t + '<span onclick="taxonGetCoL(\''+(d.name ? d.name : '' )+'\','+(d.id ? d.id : '' )+',false,true)" class="pseudo-a">'+
			'<span id="name-'+d.id+'">'+
			d.name + '</span></span>';

	} else {
	
		t = t + '<span id="name-'+d.id+'">' + d.name + '</span>';
	
	}

	t = t + '</td><td><input type=checkbox id="taxon-'+d.id+'" checked="checked" />'+
			'<input type="hidden" id="parent-id-'+d.id+'" value="'+(taxonTaxonParent.id==undefined ? '' : taxonTaxonParent.id)+'" />'+
			'<input type="hidden" id="parent-name-'+d.id+'" value="'+(taxonTaxonParent.name==undefined ? '' : taxonTaxonParent.name)+'" />'+
			'<input type="hidden" id="parent-rank-'+d.id+'" value="'+(taxonTaxonParent.rank==undefined ? '' : taxonTaxonParent.rank)+'" />'+
			'</td></tr>\n';

	return t;

}
	
function taxonCoLResultBuildChildTree(children,level) {

	if (children==undefined) return '';

	var b = '';

	for(var i=0;i<children.length;i++) {

		var d = children[i].taxon;
		
		b = b + taxonCoLMakeTableRow(d,'.',level);

		if (children[i].child_taxa && children[i].child_taxa.length > 0) {

			b = b + taxonCoLResultBuildChildTree(children[i].child_taxa,level+1);
			taxonTaxonParent = d;

		}

	}

	return b;

}

function taxonParseCoLResult(data) { 

	b = 'Results:\n<table>\n';
	obj = $.parseJSON(data);
	//return '<pre>'+dumpObj(obj);

	if (obj.parent_taxa) {

		for(var i=0;i<obj.parent_taxa.length;i++) {

			var d = obj.parent_taxa[i];
			
			b = b + taxonCoLMakeTableRow(d,'&para;');
			
			taxonTaxonParent = d;

		}
	}

	b = b + taxonCoLMakeTableRow(obj.taxon,'&rarr;');

	taxonTaxonParent = obj.taxon;

	b = b + taxonCoLResultBuildChildTree(obj.child_taxa,1);

	b = b + '</table>\n';

	return b;

}

function taxonGetCoL(name,id,singlelevel,subdiv) {

	if (!name) name = $('#taxon_name').val();

	if (name.length==0) return;

	allAjaxAborted = false;

	taxonCoLSingleLevel = singlelevel==undefined ? $('#single-child-level').is(':checked') : singlelevel ;
	taxonTargetDiv = subdiv ? 'col-subresult' : 'col-result';

	allShowLoadingDiv();

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'taxon_name' : name ,
			'taxon_id' : id ,
			'action' : 'get_col',
			'levels' : taxonCoLSingleLevel ? '1' : '0' ,
			'time' : allGetTimestamp()
		}),
		timeout : 300000 , // ms (5 mins)
		complete : function (XMLHttpRequest, textStatus) { 
			allHideLoadingDiv();

			if (textStatus=='timeout') {
				alert('Request timed-out');
			}
		}, 
		success : function (data) {
			if (data.indexOf('<error>')>=0) {
				alert(data.replace('<error>',''))
			} else {
				try {
					$('#'+taxonTargetDiv).html(taxonParseCoLResult(data));
					$('#col-result-instruction').css('visibility','visible');
					if (subdiv) taxonRepositionResults();
				} catch(err) {
					if (!allAjaxAborted) alert('An unknown error occurred')
				}
  			}			
		}
	})

}

function taxonRepositionResults() {

	var offset = $('#col-result').offset();

	$('#col-subresult').offset({ left: offset.left+390, top: offset.top});

}

function taxonSaveCoLTaxa(taxa) {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'data' : taxa ,
			'action' : 'save_col',
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			if (data.indexOf('<error>')>=0) {
				alert(data.replace('<error>',''))
			} else {
				alert('Data saved');
			}
		}
	})

}

function taxonSaveCoLResult() {

	var taxonTaxaToSave = Array();
	
	$("input:checkbox[id^='taxon']").each(function(index) {

		if ($(this).is(':checked')) {

			var id = $(this).attr('id').replace('taxon-','');

			taxonTaxaToSave[taxonTaxaToSave.length] = ([
				id,
				 $('#name-'+id).html(),
				 $('#rank-'+id).html(),
				 $('#parent-name-'+id).val()
			]);

		}

	});

	taxonSaveCoLTaxa(taxonTaxaToSave);

}



//MEDIA
var taxonMediaDescBeingEdited = false;
var taxonMediaSaveButtonClicked = false;
var taxonMediaDescBeforeEdit = false;
var taxonMediaIds = Array();

function taxonMediaDescriptionEdit(ele) {

	if (taxonMediaSaveButtonClicked) {
		taxonMediaSaveButtonClicked = false;
		return;
	}

	if (ele==taxonMediaDescBeingEdited) { 
		return;
	} else {
		taxonMediaDescBeforeEdit = $(ele).html();
		taxonMediaSaveDesc();
		$(taxonMediaDescBeingEdited).html($('#taxon-media-description').val());
	}


	$(ele).html(
		'<textarea id="taxon-media-description">'+
		$(ele).html()+
		'</textarea>'+
		'<div>'+
		'<input type="button" value="save" onclick="taxonMediaClickSave()" />&nbsp;'+
		'<input type="button" value="cancel" onclick="taxonMediaClickClose()" />'+
		'</div>'
	);

	$('#taxon-media-description').focus();

	taxonMediaDescBeingEdited = ele;

}

function taxonMediaSaveDesc() {

	if (taxonMediaDescBeingEdited==false) return;
	
	var val = $('#taxon-media-description').val();

	$(taxonMediaDescBeingEdited).html(val);

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'save_media_desc' ,
			'id' : taxonMediaDescBeingEdited.id.replace('media-','') ,
			'description' : val ,
			'language' : taxonActiveLanguage ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			if(data=='<ok>') {
				allSetMessage('saved');
			}			
		}
	});

}

function taxonMediaGetDescription() {
}

function taxonMediaGetDescriptions() {

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'get_media_descs' ,
			'language' : taxonActiveLanguage ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			if (data) {
				obj = $.parseJSON(data);
				if (obj) {
					for(var i=0;i<obj.length;i++) {
						$('#media-'+obj[i].id).html(obj[i].description);
					}
				}
			}			
			allHideLoadingDiv();
		}
	});

}

function taxonMediaClickSave() {
	
	taxonMediaSaveButtonClicked = true;
	taxonMediaSaveDesc();
	taxonMediaDescBeingEdited = false;

}

function taxonMediaClickClose() {

	taxonMediaSaveButtonClicked = true;
	$(taxonMediaDescBeingEdited).html(taxonMediaDescBeforeEdit);
	taxonMediaDescBeingEdited = false;

}

function taxonMediaChangeLanguage(lan) {

	allShowLoadingDiv();
	taxonMediaSaveDesc();
	taxonMediaDescBeingEdited = false;
	taxonActiveLanguage = lan;
	taxonDrawTaxonLanguages('taxonMediaChangeLanguage',true);
	taxonMediaGetDescriptions();

}

function taxonMediaAddId(id) {

	taxonMediaIds[taxonMediaIds.length] = id;

}

function taxonMediaDelete(id,type,name) {

	if (!allDoubleDeleteConfirm('the '+type,name)) return;

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'delete_media' ,
			'id' : id ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			//alert(data);
			if(data=='<ok>') {
				allSetMessage(type+' deleted');
				$('#media-row-'+id).remove();
			}			
		}
	});

}

function taxonMediaShowMedia(url,name) {

	$.colorbox({
		href:url,
		title:name,
		transition:"elastic", 
		maxWidth:800,
		width:"100%",
		opacity:0
	});

}


//RANKS, CATEGORIES & SECTIONS
var taxonKingdom = Array();
var taxonAddedRanks = Array();
var taxonCoLRanks = Array();

function taxonAddCoLRanks() {

	taxonAddedRanks = taxonCoLRanks;
	taxonShowSelectedRanks();

}

function taxonAddRank(id,noparent) {

	if (noparent==undefined) noparent = false;
	taxonAddedRanks[taxonAddedRanks.length]=[id,$('#rank-'+id).html(),noparent];
	taxonOrderSelectedRanks();
	taxonShowSelectedRanks();

}

function taxonRemoveRank(id) {

	if (id==taxonKingdom[0]) return;
	var t = Array();

	for (var i=0;i<taxonAddedRanks.length;i++) {
		if (taxonAddedRanks[i][0]!=id && id != null) {
			t[t.length]=taxonAddedRanks[i];
		}
	}

	taxonAddedRanks = t;
	
	if (taxonAddedRanks.length==0) taxonAddRank(taxonKingdom[0]);

	taxonOrderSelectedRanks();
	taxonShowSelectedRanks();

}

function taxonRemoveAll() {

	taxonRemoveRank();
	taxonShowSelectedRanks();	

}

function sortRankArray(a,b) {

	return (a[0] > b[0] ? 1 : (a[0] < b[0] ? -1 : 0));

}

function taxonOrderSelectedRanks() {

	var d = '|';
	var t = Array();
	for (var i=0;i<taxonAddedRanks.length;i++) {
		if (d.indexOf('|'+taxonAddedRanks[i][0]+'|')==-1) {
			d = d + taxonAddedRanks[i][0] + '|';
			t[t.length]=taxonAddedRanks[i];
		}
	}

	t.sort(sortRankArray)

	taxonAddedRanks = t;

}

function taxonShowSelectedRanks() {

	var first = true;
	
	$('#selected-ranks').children().remove();

	for (var i=0;i<taxonAddedRanks.length;i++) {

		if (taxonAddedRanks[i][2]==true && first==true) {
			$('<option disabled="disabled">').val('').text('------------------------------------').appendTo('#selected-ranks');
			first = false;
		}
		
		$('<option id="sel-rank-'+taxonAddedRanks[i][0]+'">').val(taxonAddedRanks[i][0]).text(taxonAddedRanks[i][1]).appendTo('#selected-ranks');
		$('#sel-rank-'+taxonAddedRanks[i][0]).dblclick( function () { taxonRemoveRank(this.value); });

	}

}

function taxonSaveRanks() {

	for (var i=0;i<taxonAddedRanks.length;i++) {

		$('<input type="hidden" name="ranks[]" value="'+taxonAddedRanks[i][0]+'">').appendTo('#theForm');

	}

	$('#theForm').submit();

}

function taxonAddRankId(rank) {

	taxonRanks[taxonRanks.length] = rank;

}

function taxonSwitchRankLanguage(language) {

	taxonActiveLanguage = language;
	taxonDrawRankLanguages();	
	switch (taxonActiveView) {
		case 'ranklabels':
			taxonGetRankLabels(taxonActiveLanguage);
			break;
		case 'page':
			taxonGetPageLabels(taxonActiveLanguage);
			break;			
		case 'sections':
			taxonGetSectionLabels(taxonActiveLanguage);
			break;			
	}

}

function taxonDrawRankLanguages() {
	
	var b='';

	for(var i=0;i<taxonLanguages.length;i++) {
		if (taxonLanguages[i][2]!=1) {
			b = b + 
				'<span class="rank-language'+
					(taxonLanguages[i][0]==taxonActiveLanguage ? '-active' : '' )+
					'" onclick="taxonSwitchRankLanguage('+ taxonLanguages[i][0] +')">' + 
				taxonLanguages[i][1] + 
				'</span>&nbsp;';
		} else {
			taxonDefaultLanguage = taxonLanguages[i][0];
		}
	}

	$('#language-tabs').html(b);

}

function taxonGeneralGetLabels(language,action,postFunction) {

	allShowLoadingDiv();

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : action ,
			'language' : language ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			obj = $.parseJSON(data);
			eval(postFunction+'(obj,language)');
			allHideLoadingDiv();
		}
	})
	
}

function taxonGeneralSave(id,label,type,action) {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : action ,
			'id' : id , 
			'label' : label , 
			'language' :  type=='default' ? taxonDefaultLanguage : taxonActiveLanguage ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			allSetMessage(data);
		}
	});

}

function taxonGeneralSetLabels(obj,language,idField,labelField) {

	for(var i=0;i<taxonRanks.length;i++) {
		if (language==taxonDefaultLanguage) {
			$('#default-'+taxonRanks[i]).val('');
		} else {
			$('#other-'+taxonRanks[i]).val('');
		}
	}

	if (obj) {
	
		for(var i=0;i<obj.length;i++) {
			var id = eval('obj[i].'+idField);
			var val = eval('obj[i].'+labelField);
			
			if (language==taxonDefaultLanguage) {
				$('#default-'+id).val(val);
				if (obj[i].direction) $('#default-'+id).attr('dir',obj[i].direction);
			} else {
				$('#other-'+id).val(val);
				if (obj[i].direction) $('#other-'+id).attr('dir',obj[i].direction);
			}
		}

	}

}

function taxonSaveRankLabel(id,label,type) {

	taxonGeneralSave(id,label,type,'save_rank_label');
	
}

function taxonSetRankLabels(obj,language) {

	taxonGeneralSetLabels(obj,language,'project_rank_id','label');

}

function taxonGetRankLabels(language) {
	
	taxonGeneralGetLabels(language,'get_rank_labels','taxonSetRankLabels');
	
}

function taxonCheckNewTaxonName() {

	if ($('#taxon-name').val().length==0) {
		$('#taxon-message').html('')
		return;
	}
	
	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'check_taxon_name' ,
			'taxon_name' : $('#taxon-name').val() ,
			'id' : $('#id').val() ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			//alert(data);
			if(data=='<ok>') {
				$('#taxon-message').removeClass().addClass('message-no-error');
				$('#taxon-message').html('Ok')
			} else {
				$('#taxon-message').removeClass().addClass('message-error');
				$('#taxon-message').html(data)
			}
		}
	});

}

function taxonGetRankByParent(nomessage) {

	var id = $('#parent-id option:selected').val();

	if (id == -1) {
		return;
	}

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'get_rank_by_parent' ,
			'id' : id ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {

			if (data=='-1' && !nomessage) {
				$('#rank-message').removeClass().addClass('message-error');
				$('#rank-message').html('That taxon cannot have child taxa.')
			} else {
				if (!nomessage) {
					$('#rank-message').removeClass().addClass('message-no-error');
					$('#rank-message').html('Ok')
				}
				$('#rank-id').val(data);
			}

			if (!nomessage) taxonCheckHybridCheck();

		}
	});

}

function taxonCheckHybridCheck() {

	if (!$('#hybrid').is(':checked')) {
		$('#hybrid-message').removeClass().addClass('message-no-error');
		$('#hybrid-message').html('')
		return;
	}

	if (taxonCanHaveHybrid.inArray($('#rank-id').val())==-1) {
		$('#hybrid-message').removeClass().addClass('message-error');
		$('#hybrid-message').html('A taxon of that rank cannot be a hybrid.')
	} else {
		$('#hybrid-message').removeClass().addClass('message-no-error');
		$('#hybrid-message').html('Ok')
	}

}

function taxonSavePageTitle(id,label,type) {

	taxonGeneralSave(id,label,type,'save_page_title');	

}


function taxonSetPageLabels(obj,language) {

	taxonGeneralSetLabels(obj,language,'page_id','title');

}

function taxonGetPageLabels(language) {

	taxonGeneralGetLabels(language,'get_page_labels','taxonSetPageLabels');

}

function taxonSaveSectionTitle(id,label,type) {
	
	taxonGeneralSave(id,label,type,'save_section_title');

}

function taxonSectionDelete(id,name) {
	
	taxonGeneralDeleteLabels(id,'delete_section_title',name,'the section');

}

function taxonSetSectionLabels(obj,language) {

	taxonGeneralSetLabels(obj,language,'section_id','label');

}

function taxonGetSectionLabels(language) {

	taxonGeneralGetLabels(language,'get_section_titles','taxonSetSectionLabels');
	
}

