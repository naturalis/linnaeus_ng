var taxonActivePageTitle = false;
var taxonActiveTaxonId = false;
var taxonNewLanguage = false;
var taxonActivePage = false;
var taxonPages = Array();
var taxonPageStates = Array();
var taxonPublishStates = Array();
var taxonSaveType = 'auto';
var taxonTargetDiv = false;
var taxonTaxonParent = Array();
var taxonRanks = Array();
var taxonCanHaveHybrid = Array();
var taxonExecAfterSave = false;
var taxonRankBorder = false;
var taxonCommonnameLanguages = Array();
var taxonHigherTaxa = false;
var taxonCopyableTaxa = Array();
var taxonSubmitButtonLabel = null;;
var taxonSubGenusRankId = null;

//GENERAL
function taxonGeneralDeleteLabels(id,action)
{
	allAjaxHandle = $.ajax({
		url : "ajax_interface_mgmt.php",
		type: "POST",
		data : ({
			'action' : action ,
			'id' : id ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data)
		{
			location.href = location.href;
		}
	});

}

//CATEGORIES
function taxonPageDelete(page,name) {

	if (!allDoubleDeleteConfirm(_('the page'),name)) return;

	taxonGeneralDeleteLabels(page,'delete_page');

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
				allSetMessage(_('saved'));
			}
		}
	});

	$('#namecell'+id).html(
		'<span onclick="taxonEditTaxonName('+id+')" id="name'+id+'" class="a">'+
		newName+'</span>'
	);

}

function taxonEditTaxonName(id) {

	$('#namecell'+id).html(
		'<input class="taxonNameEdit" id="edit'+id+'" value="'+$('#name'+id).html()+'" />'+
		'<input type="button" value="save" onclick="taxonSaveEditedTaxonName('+id+')" />'
	);

}

function taxonAddPage(page) {
	//[id,[names],default?]
	// names[-1] contains the system label, names[x] the name in language x
	taxonPages[taxonPages.length] = page;
}


function taxonDrawTaxonLanguages(fnc,includeDef) {

	if (allLanguages.length<=1) return;

	fnc = fnc || 'taxonSwitchLanguage';

	var buffer = '';

	for (var i=0;i<allLanguages.length;i++) {

		if (allLanguages[i][2]!=1 || includeDef==true) {
			buffer = buffer+
				'<span class="project-language'+
				(allLanguages[i][0]==allActiveLanguage ?
					'-active"' :
					'" class="a" onclick="'+fnc+'('+allLanguages[i][0]+');'
				)+
				'">'+
				allLanguages[i][1]+
				'</span>&nbsp;';
		}

		if (allLanguages[i][0]==allDefaultLanguage) var def = allLanguages[i][1];
	}



//	$('#taxon-language-other-language').html(buffer);
	$('[id^="taxon-language-other-language"]').html(buffer);

//	$('#taxon-language-default-language').html(def);
	$('[id^="taxon-language-default-language"]').html(def);

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

		if ((i+1)%8==0) {
			buffer=buffer+'</tr><tr>';
		}
		//if (taxonHigherTaxa && i==0) break;
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
			'language' : allDefaultLanguage ,
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

	if (taxonActiveTaxonId.length==0 || allActiveLanguage===false) return;

	taxonGetData(
		taxonActiveTaxonId,
		allActiveLanguage,
		taxonActivePage,
		'taxon-content-active',
		updateInterface
	);

}

function taxonGetDataDefault(updateInterface) {

	if (taxonActiveTaxonId.length==0) return;

	taxonGetData(
		taxonActiveTaxonId,
		allDefaultLanguage,
		taxonActivePage,
		'taxon-content-default',
		updateInterface
	);

}

function taxonSwitchLanguage(language) {

	allAjaxAsynchMode = false;
	taxonSaveDataActive();
	allActiveLanguage = language;
	taxonGetDataActive(true);
	allAjaxAsynchMode = true;

}

function taxonSwitchPage(page) {

	allAjaxAsynchMode = false;
	taxonSaveDataAll();
	taxonActivePage = page;
	taxonGetDataAll();
	allAjaxAsynchMode = true;

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
	taxonExecAfterSave = "window.open('branches.php','_self');";
	taxonSaveDataDefault();

}

function taxonSaveDataActive() {

	if (taxonActiveTaxonId.length==0 || allActiveLanguage===false) return;

	taxonSaveData(
		taxonActiveTaxonId,
		allActiveLanguage,
		taxonActivePage,
		tinyMCE.get('taxon-content-active').getContent(),
		'taxon-content-active'
	);

}

function taxonSaveDataDefault() {

	if (taxonActiveTaxonId.length==0) return;

	taxonSaveData(
		taxonActiveTaxonId,
		allDefaultLanguage,
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

			obj = $.parseJSON(data);

			$('#taxon-name-input').val(obj.title ? obj.title : '');
			tinyMCE.get(editorName).setContent(obj.content ? obj.content : '');
			taxonActivePage = obj.page_id;

			if (language!=allDefaultLanguage) {
				taxonPublishStates[1] = obj.publish;
				allActiveLanguage = obj.language_id;
			} else {
				taxonPublishStates[0] = obj.publish;
			}

			if (updateInterface) {
				taxonUpdateInterface();
			}
			tMCEFirstUndoPurge(editorName);
		}
	});

}

function taxonUpdateInterface() {

	taxonDrawTaxonLanguages();
	taxonUpdatePageBlock();
	taxonDrawPublishBlocks();
	allHideLoadingDiv();
	taxonEnableButtons();

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
		async: allAjaxAsynchMode ,
		success: function (data) {
			//alert(data);
			if (data.indexOf('<error>')>=0) {

				alert(data.replace('<error>',''));

			} else
			if (data.indexOf('<msg>')>=0) {

				allSetMessage(data.replace('<msg>',''),2000);

			} else {

				obj = $.parseJSON(data);

				if (obj) {

					if (obj.modified==true) {
						if (taxonSaveType=='manual') tinyMCE.get(editorName).setContent(obj.content ? obj.content : '');
						allSetMessage(_('saved (could not save certain HTML-tags)'));
					} else {
						allSetMessage(_('saved'));
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

function taxonDeleteData(id,name) {

	if (!id) id = taxonActiveTaxonId;

	if (id.length==0) return;

	if (!name) name = $('#taxon-name').val();

	if (!allDoubleDeleteConfirm('all content in all languages for taxon',name)) return;

	$.ajax({
		url : "ajax_interface.php",
		data : ({
			'id' : id ,
			'action' : 'delete_taxon' ,
			'page' : taxonActivePage ,
			'time': allGetTimestamp()
		}),
		type: "POST",
		async: allAjaxAsynchMode ,
		success: function (data) {
			//alert(data);
			if (data=='<redirect>') {
				window.open('delete.php?id='+id+'&time='+allGetTimestamp(),'_self');
			} else {
				window.open('manage.php','_self');
			}

		}
	});

}

function taxonPublishContent(type,state) {

	if (taxonActiveTaxonId.length==0) return;

	if (state==1 && type=='default') {

		if (tinyMCE.get('taxon-content-default').getContent().length==0) {
			alert(_('Nothing to publish.'));
			return;
		}
		taxonSaveDataDefault();

	} else
	if (state==1 && type=='active') {

		if (tinyMCE.get('taxon-content-active').getContent().length==0) {
			alert(_('Nothing to publish.'));
			return;
		}
		taxonSaveDataDefault();
		taxonSaveDataActive();

	}

	$.ajax({
		url : "ajax_interface.php",
		data : ({
			'id' : taxonActiveTaxonId ,
			'action' : 'publish_content' ,
			'language' : (type=='active' ? allActiveLanguage : allDefaultLanguage) ,
			'page' : taxonActivePage ,
			'state' : state ,
			'time' : allGetTimestamp()
		}),
		type: "POST",
		success: function(data){
			if (data=='<ok>') {
				taxonPublishStates[type=='active' ? 1 : 0] = state;
				taxonDrawPublishBlocks();
				taxonUpdatePageBlock();
			} else {
				alert(data);
			}
		}
	});

}


function taxonDrawPublishBlocks() {

	if (taxonPublishStates[0]=='1')
		$('#taxon-language-default-publish').html(
			sprintf(
				_('(This page has been published in this language. Click %shere%s to unpublish.)'),
				'<span class="a" onclick="taxonPublishContent(\'default\',\'0\');">',
				'</span>'
			)
		);
	else
		$('#taxon-language-default-publish').html(
			sprintf(
				_('(This page has not been published in this language. Click %shere%s to publish.)'),
				'<span class="a" onclick="taxonPublishContent(\'default\',1);">',
				'</span>'
			)
		);

	if (taxonPublishStates[1]=='1')
		$('#taxon-language-other-publish').html(
			sprintf(
				_('(This page has been published in this language. Click %shere%s to unpublish.)'),
				'<span class="a" onclick="taxonPublishContent(\'active\',\'0\');">',
				'</span>'
			)
		);
	else
		$('#taxon-language-other-publish').html(
			sprintf(
				_('(This page has not been published in this language. Click %shere%s to publish.)'),
				'<span class="a" onclick="taxonPublishContent(\'active\',1);">',
				'</span>'
			)
		);

}

function taxonRunAutoSave() {

	if (!autoSaveInit) taxonSaveDataAll();

	autoSaveInit = false;

	setTimeout("taxonRunAutoSave()", autoSaveFreq);

}

function taxonGetUndo() {

	$.post(
		"ajax_interface.php",
		{
			'id' : $('#taxon_id').val() ,
			'action' : 'get_taxon_undo' ,
			'time' : allGetTimestamp()
		},
		function(data){

			//alert(data);//return;

			if (data) {

				obj = $.parseJSON(data);

				if (obj.page_id!=taxonActivePage) {

					if (taxonPages.inArray(obj.page_id,0) >= 0) {

						taxonActivePage = obj.page_id;

					} else {

						allSetMessage(_('cannot undo'));

						return;
					}

				}

				if (obj.language_id==allDefaultLanguage) {

					tinyMCE.get('taxon-content-default').setContent(obj.content ? obj.content : '');
					taxonPublishStates[0] = obj.publish;

				} else
				if (obj.language_id==allActiveLanguage) {

					tinyMCE.get('taxon-content-active').setContent(obj.content ? obj.content : '');
					taxonPublishStates[1] = obj.publish;

				} else
				if (allLanguages.inArray(obj.language_id,0) >= 0) {

					allActiveLanguage = obj.language_id;
					tinyMCE.get('taxon-content-active').setContent(obj.content ? obj.content : '');
					taxonPublishStates[1] = obj.publish;

				} else {

					allSetMessage(_('cannot undo'));

					return;

				}

				if (obj.language_id==allDefaultLanguage) {
					taxonGetDataActive(false);
				} else
				if (obj.language_id==allActiveLanguage) {
					taxonGetDataDefault(false);
				}

				taxonUpdateInterface();

				allSetMessage(_('recovered'));

			} else {

					allSetMessage(_('cannot undo'));

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

		t = t + '<span onclick="taxonGetCoL(\''+(d.name ? d.name : '' )+'\','+(d.id ? d.id : '' )+',false,true)" class="a">'+
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
				alert(data.replace('<error>',''));
			} else {
				try {
					$('#'+taxonTargetDiv).html(taxonParseCoLResult(data));
					$('#col-result-instruction').css('visibility','visible');
					if (subdiv) taxonRepositionResults();
				} catch(err) {
					if (!allAjaxAborted) alert(_('An unknown error occurred'));
				}
  			}
		}
	});

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
		async: allAjaxAsynchMode,
		success : function (data) {
		if (data.indexOf('<error>')>=0) {
				alert(data.replace('<error>',''));
			} else {
				alert(_('Data saved'));
			}
		}
	});

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
var taxonMediaSaveButtonClicked = false;
var taxonMediaDescBeforeEdit = false;
var taxonMediaFiles = Array();

function taxonMediaSaveDesc(ele,id) {

	var val = $('#'+ele).val();

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'save_media_desc' ,
			'id' : id ,
			'description' : val ,
			'language' : allActiveLanguage ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			if(data=='<ok>') allSetMessage(_('saved'));
		}
	});

}

function taxonMediaGetDescriptions() {

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'get_media_descs' ,
			'id' : $('#taxon_id').val() ,
			'language' : allActiveLanguage ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			//alert(data);
			if (data) {
				obj = $.parseJSON(data);
				if (obj) {
					for(var i=0;i<obj.length;i++) {
						$('#media-'+obj[i].id).val(obj[i].description);
					}
				}
			}
			allHideLoadingDiv();
		}
	});

}

function taxonMediaChangeLanguage(lan) {

	allShowLoadingDiv();
	taxonMediaSaveDesc();
	allActiveLanguage = lan;
	taxonDrawTaxonLanguages('taxonMediaChangeLanguage',true);
	taxonMediaGetDescriptions();

}

function taxonMediaFileStore(file) {

	taxonMediaFiles[taxonMediaFiles.length] = file;

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
		async: allAjaxAsynchMode,
		success : function (data) {
			//alert(data);
			if(data=='<ok>') {
				allSetMessage(type+' '+_('deleted'));
				$('#media-row-'+id).remove();
			}
		}
	});

}


//RANKS, CATEGORIES & SECTIONS
var taxonKingdom = Array();
var taxonAddedRanks = Array();
var taxonCoLRanks = Array();

function taxonAddCoLRanks() {

	taxonAddedRanks = taxonCoLRanks;
	taxonRankBorder=taxonAddedRanks[taxonAddedRanks.length-1][0];
	taxonShowSelectedRanks();

}

function taxonAddRank(id,noparent,updateInterface) {

	if (updateInterface==undefined) updateInterface = true;
	if (noparent==undefined) noparent = false;

	taxonAddedRanks[taxonAddedRanks.length]=[id,$('#rank-'+id).html(),noparent];
	taxonOrderSelectedRanks();
	if (updateInterface) taxonShowSelectedRanks();

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

	if (taxonAddedRanks.length==0) {

		taxonAddRank(taxonKingdom[0]);
		taxonRankBorder=taxonAddedRanks[taxonAddedRanks.length-1][0];

	}

	if (taxonRankBorder==id) {

		if (taxonAddedRanks.length>1)
			taxonRankBorder=taxonAddedRanks[taxonAddedRanks.length-2][0];
		else
			taxonRankBorder=taxonAddedRanks[taxonAddedRanks.length-1][0];

	}

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

function taxonMoveBorder(id) {

	taxonRankBorder = id;
	taxonShowSelectedRanks();

}

function taxonShowSelectedRanks()
{
	var first = true;
	var b= '';

	if (taxonRankBorder==false && taxonAddedRanks.length>1)
	{
		taxonRankBorder = taxonAddedRanks[(taxonAddedRanks.length)-1][0];
	}

	for (var i=0;i<taxonAddedRanks.length;i++)
	{
//		if (taxonAddedRanks[i][2]==true && first==true) {
//			b = b + '<tr><td>------------------------------------</td</tr>'+"\n";
//			first = false;
//		}

		if (taxonAddedRanks[i][0]==taxonRankBorder)
		{
			b = b +
					'<tr>'+
						'<td id="sub1" colspan="2" class="rankRedLine"></td>'+
						'<td class="rankRedLineEnd"></td>'+
					'</tr>'+
					"\n";
		}

		b = b +
			'<tr class="tr-highlight">'+
				'<td colspan="2" class="rankSelectedRank" rankId="'+taxonAddedRanks[i][0]+'" '+
					'ondblclick="taxonRemoveRank(this.attributes.rankId.value)">'+
					taxonAddedRanks[i][1]+
				'</td>'+
				'<td>'+
					(i>=0 && i<taxonAddedRanks.length-1 && taxonAddedRanks[i+1][0]==taxonRankBorder ?
						'<span class="rankArrow" onclick="taxonMoveBorder('+taxonAddedRanks[i][0]+');">&uarr;</span>' : '')+
					(i>=0 && i<taxonAddedRanks.length-1 && taxonAddedRanks[i][0]==taxonRankBorder ?
						'<span class="rankArrow" onclick="taxonMoveBorder('+taxonAddedRanks[i+1][0]+');">&darr;</span>' : '') +
				'</td>'+
			'</tr>'+
			"\n";
	}

	$('#selected-ranks').html('<table id="selectedRanksTable">'+b+'</table>');
}

function taxonSaveRanks()
{
	for (var i=0;i<taxonAddedRanks.length;i++)
	{
		$('<input type="hidden" name="ranks[]" value="'+taxonAddedRanks[i][0]+'">').appendTo('#theForm');
	}
	$('<input type=hidden name="higherTaxaBorder" value="'+taxonRankBorder+'">').appendTo('#theForm');
	$('#theForm').submit();
}

function taxonAddRankId(rank)
{
	taxonRanks[taxonRanks.length] = rank;
}

function taxonGeneralSave(id,label,type,action,alturl)
{
	url=alturl ? alturl : "ajax_interface.php";

	allAjaxHandle = $.ajax({
		url : url,
		type: "POST",
		data : ({
			'action' : action ,
			'id' : id ,
			'label' : label ,
			'language' :  type=='default' ? allDefaultLanguage : allActiveLanguage ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data)
		{
			allSetMessage(data);
		}
	});

}

function taxonGeneralSetLabels(obj,language,idField,labelField)
{
	for(var i=0;i<taxonRanks.length;i++)
	{
		if (language==allDefaultLanguage)
		{
			$('#default-'+taxonRanks[i]).val('');
		} 
		else 
		{
			$('#other-'+taxonRanks[i]).val('');
		}
	}

	if (obj)
	{
		for(var i=0;i<obj.length;i++)
		{
			var id = eval('obj[i].'+idField);
			var val = eval('obj[i].'+labelField);

			if (language==allDefaultLanguage)
			{
				$('#default-'+id).val(val);
				if (obj[i].direction) $('#default-'+id).attr('dir',obj[i].direction);
			} 
			else 
			{
				$('#other-'+id).val(val);
				if (obj[i].direction) $('#other-'+id).attr('dir',obj[i].direction);
			}
		}
	}
}

var ajaxAltUrl;

function taxonSaveRankLabel(id,label,type)
{
	taxonGeneralSave(id,label,type,'save_rank_label','ajax_interface_mgmt.php');
}

function taxonSetRankLabels(obj,language)
{
	taxonGeneralSetLabels(obj,language,'project_rank_id','label');
}

function taxonGetRankLabels(language)
{
	allGeneralGetLabels(language,'get_rank_labels',taxonSetRankLabels,null,'ajax_interface_mgmt.php');
}

function taxonGetRankByParent(nomessage)
{
	var id = $('#parent-id option:selected').val();

	if (id==-1 || id==undefined) return;

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'get_rank_by_parent' ,
			'id' : id ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			//alert(data);
			$('#taxon-name').val('');
			$('#formatted-example').html('');

			if (data=='-1' && !nomessage) {
				$('#rank-message').removeClass().addClass('message-error');
				$('#rank-message').html(_('That taxon cannot have child taxa.'))
			} else {
				if (!nomessage) {
					$('#rank-message').removeClass().addClass('message-no-error');
					//$('#rank-message').html(_('Ok'))
					$('#rank-message').html('')
				}

				$('#rank-id').val(data);

				if ($('#rank-id').val()==null) {
					$('#rank-message').removeClass().addClass('message-error');
					$('#rank-message').html(_('That taxon cannot have child taxa.'))
				} else {
					taxonAddNamePart(data);
				}
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
		$('#hybrid-message').html(_('A taxon of that rank cannot be a hybrid.'))
	} else {
		$('#hybrid-message').removeClass().addClass('message-no-error');
		$('#hybrid-message').html(_('Ok'))
	}

}

function taxonSavePageTitle(id,label,type) {

	taxonGeneralSave(id,label,type,'save_page_title');

}


function taxonSetPageLabels(obj,language) {

	taxonGeneralSetLabels(obj,language,'page_id','title');

}

function taxonGetPageLabels(language)
{
	allGeneralGetLabels(language,'get_page_labels',taxonSetPageLabels,'ajax_interface_mgmt.php');
}

function taxonSaveSectionTitle(id,label,type) {

	taxonGeneralSave(id,label,type,'save_section_title');

}

function taxonSectionDelete(id,name) {

	if (!confirm(sprintf(_('Are you sure you want to delete %s "%s"?'),'the section',name)))
		return;

	taxonGeneralDeleteLabels(id,'delete_section_title');

}

function taxonSetSectionLabels(obj,language) {

	taxonGeneralSetLabels(obj,language,'section_id','label');

}

function taxonGetSectionLabels(language) {

	allGeneralGetLabels(language,'get_section_titles',taxonSetSectionLabels);

}

function taxonGenericNameAction(id,action,type) {

	if (action=='delete' && !confirm(_('Are you sure?'))) return;

	$('#'+type+'_id').val(id);
	$('#action').val(action);
	$('#theForm').submit()

}

function taxonSynonymAction(id,action) {

	taxonGenericNameAction(id,action,'synonym');

}

function taxonCommonNameAction(id,action) {

	taxonGenericNameAction(id,action,'commonname');

}

function taxonCommonNameSubmit() {

	if ($('#commonname').val()=='' && $('#transliteration').val()=='') {

		alert(_('You have to enter a common name and/or a transliteration.'));

	} else {

		$('#theForm').submit();

	}

}


function taxonSetCommonnameLabels(obj,language) {

	for(var j=0;j<taxonCommonnameLanguages.length;j++) {

		if (language == allDefaultLanguage)
			$('#default-'+j).val('');
		else
		if (language == allActiveLanguage)
			$('#other-'+j).val('');

	}

	if (obj) {

		for(var i=0;i<obj.length;i++) {

				for(var j=0;j<taxonCommonnameLanguages.length;j++) {

				if (taxonCommonnameLanguages[j]==obj[i].label_language_id) {

					if (language == allDefaultLanguage)
						$('#default-'+j).val(obj[i].label);
					else
					if (language == allActiveLanguage)
						$('#other-'+j).val(obj[i].label);

				}

			}

		}

	}

}

function taxonGetCommonnameLabels(language) {

	allGeneralGetLabels(language,'get_language_labels',taxonSetCommonnameLabels);

}

function taxonSaveLanguageLabel(id,label,type) {

	taxonGeneralSave(id,label,type,'save_language_label');

}

function taxonOrphanChangeSelect(ele) {

	$('#'+ele.id.replace('parent','attach')).attr('checked',true);

}

function taxonDoPreview() {

	if (allDefaultLanguage)
		$('#theForm').append('<input type="hidden" name="language-default" value="'+allDefaultLanguage+'">').val(allDefaultLanguage);

	if (allActiveLanguage)
		$('#theForm').append('<input type="hidden" name="language-other" value="'+allActiveLanguage+'">').val(allActiveLanguage);

	$('#theForm').append('<input type="hidden" name="activePage" value="'+taxonActivePage+'">').val(taxonActivePage);

	$("#action").val('save_and_preview');
	$('#theForm').submit();

}

function taxonStoreCopyableTaxa(id) {

	taxonCopyableTaxa.push(id);

}

function taxonAddNamePart(parentId) {

	if (taxonHigherTaxa) return;

	$('#taxon-name').focus();

	if ($('#parent-id :selected').attr('rank_id')==$('#rank-id :selected').attr('ideal_parent_id')) {

		$('#taxon-name').val($('#parent-id :selected').attr('name').trim()+' ');

	} else
	// hardcoded excpetion for subgenera!
	if (parentId==taxonSubGenusRankId) {

		allAjaxHandle = $.ajax({
			url : "ajax_interface.php",
			type: "POST",
			data : ({
				'action' : 'get_subgenus_child_name_prefix' ,
				'id' : $('#parent-id').val() ,
				'time' : allGetTimestamp()
			}),
			success : function (data) {
				$('#taxon-name').val(data);
			}
		});

	} else {

		return;
	}

}

function taxonChangeOverviewPicture(ele) {

	var isChecked = ele.checked;
	$('[id^=overview-]').attr('checked',false);
	$(ele).attr('checked',isChecked);

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'set_overview' ,
			'id' : ele.id.replace('overview-','') ,
			'taxon_id' : $('#taxon_id').val() ,
			'state' : isChecked ? '1' : '0',
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			if(data=='<ok>') {
				allSetMessage(_('saved'));
			}
		}
	});

}

function taxonChangeSubmitButtonLabel(ele) {

	if (taxonSubmitButtonLabel==null) taxonSubmitButtonLabel = $('#submit').val();

	$('#submit').val(taxonSubmitButtonLabel+' '+$('#rank-id :selected').text().toLowerCase());

}

function taxonChangeMediaOrder(id,mId,dir) {

	$('<input type="hidden" name="move">').val(dir).appendTo('#theForm');
	$('<input type="hidden" name="mId">').val(mId).appendTo('#theForm');
	$('#theForm').attr('action','media.php?id='+id)
	$('#theForm').submit();

//	$.post(, { id: id, mId: mId, move: dir } );

}

function taxonEnableButtons() {

	$('input[disabledOnLoad="1"]').each(function(){
		$(this).attr('disabled',null);
	});


}

function taxonGetFormattedPreview() {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'get_formatted_name' ,
			'name' : $('#taxon-name').val() ,
			'rank_id' : $('#rank-id').val() ,
			'parent_id' :$('#parent-id').val() ,
			'is_hybrid' :$('#is-hybrid').val() ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			$('#formatted-example').html(data);
		}
	});

}

function taxonOverrideSaveNew() {

	$('<input type="hidden" name="override">').val('1').appendTo('#theForm');
	$('#theForm').submit();

}

function taxonDeleteVariation(id,name) {

	if (!confirm(sprintf(_('Are you sure you want to delete the variation "%s"?'),name))) return;

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'delete_variation' ,
			'id' : id ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			$('#var-row-'+id).remove();
		}
	});

}

function taxonBlankOutRanks() {

	$('#rank-id > option').each(function(){

		$(this).attr('disabled',false);

	});

	var d = $('#parent-id :selected').attr('root_rank_id');

	if (d==undefined) return;

	$('#rank-id > option').each(function(){
		if ($(this).attr('root_rank_id')<=d) $(this).attr('disabled',true);
	});

}

function taxonSortTaxaAlpha(sortAll) {

	if (!confirm(_('Are you sure you want to permanently sort the taxa alphabetically?')))
		return;

	if (sortAll==true)
		$('#theForm').append('<input type="hidden" name="sortAll" value="1">').val('1');

	$('#theForm').append('<input type="hidden" name="sortAlpha" value="1">').val('1');
	$('#theForm').submit();

}


function taxonSortTaxaTaxonomic() {

	if (!confirm(_('Are you sure you want to permanently sort the taxa alphabetically per taxonomic level?')))
		return;

	$('#theForm').append('<input type="hidden" name="sortTaxonLevel" value="1">').val('1');
	$('#theForm').submit();

}



function taxonEasySynonymDelete(id) {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'delete_synonym' ,
			'id' : id ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			if(data=='<ok>')
				$('#syn-'+id).remove();
			else
				alert('An error occurred.');
		}
	});

}

function taxonEasyCommonDelete(id) {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'delete_common' ,
			'id' : id ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			if(data=='<ok>')
				$('#com-'+id).remove();
			else
				alert('An error occurred.');
		}
	});

}


var sEditModes = aEditModes = Array();
var sOldVals = aOldVals = Array();

function saveEditVal(event,ele) {

	if (event.keyCode!==13 && event.keyCode!==27) return;

	var col = $(ele).attr('col');
	var id = $(ele).attr('synid');

	if (col=='s' && sEditModes[id]==false) return;
	if (col=='a' && aEditModes[id]==false) return;

	if (col=='s') {
		sEditModes[id]=false;
		oldVal=sOldVals[id];
	} else
	if (col=='a') {
		aEditModes[id]=false;
		oldVal=aOldVals[id];
	}

	if (event.keyCode==13) {

		var newVal = $(ele).val();

		$(ele).parent().html(newVal);

		allAjaxHandle = $.ajax({
			url : "ajax_interface.php",
			type: "POST",
			data : ({
				'action' : 'save_synonym_data' ,
				'id' : id ,
				'val' : newVal ,
				'col' : col ,
				'time' : allGetTimestamp()
			}),
			async: allAjaxAsynchMode,
			success : function (data) {
				allSetMessage(data=='<ok>' ? _('saved') : _('an error occurred'));
			}
		});

	} else {

		$(ele).parent().html(oldVal);

	}

}

function taxonSynonymEditSyn(ele,id) {

	if (sEditModes[id]==true) return;
	var oldVal = $(ele).html();
	var x = $(ele).html('<input type="text" id="s'+id+'" col="s" synid="'+id+'" value="" style="width:100%" onkeyup="saveEditVal(event,this)">');
	$('#s'+id).val(oldVal).focus();
	sEditModes[id]=true;
	sOldVals[id]=oldVal;

}

function taxonSynonymEditAuth(ele,id) {

	if (aEditModes[id]==true) return;
	var oldVal = $(ele).html();
	var x = $(ele).html('<input type="text" id="a'+id+'" col="a" synid="'+id+'" value="" style="width:100%" onkeyup="saveEditVal(event,this)">');
	$('#a'+id).val(oldVal).focus();
	aEditModes[id]=true;
	aOldVals[id]=oldVal;

}
