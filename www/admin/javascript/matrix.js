function matrixGetMatrixContent(language, action, target) {

	var id = $('#id').val();

	if (id==undefined) return;

	$.ajax({
		url : "ajax_interface.php" ,
		type: "POST",
		data : ({
			'action' : action ,
			'id' : id ,
			'language' : language ,
			'time' : allGetTimestamp()			
		}),
		success : function (data) {
			$('#'+target).val(data ? data : '');
			allHideLoadingDiv();
		}
	});

}


function matrixGetMatrixName(language) {

	var target = language==allActiveLanguage ? 'matrix-other' : 'matrix-default';

	matrixGetMatrixContent(language,'get_matrix_name',target);

}

function matrixGetCharacteristicLabel(language) {

	var target = language==allActiveLanguage ? 'characteristic-other' : 'characteristic-default';

	matrixGetMatrixContent(language,'get_characteristic_label',target);

}

function matrixGetStateLabel(language) {

	var target = language==allActiveLanguage ? 'label-other' : 'label-default';

	matrixGetMatrixContent(language,'get_state_label',target);

}

function matrixGetStateText(language) {

	var target = language==allActiveLanguage ? 'text-other' : 'text-default';

	matrixGetMatrixContent(language,'get_state_text',target);

}

function matrixSaveContent(language, action, content) {

	var id = $('#id').val();

	if (id==undefined) return;

	$.ajax({
		url : "ajax_interface.php" ,
		type: "POST",
		data : ({
			'action' : action ,
			'id' : id ,
			'language' : language ,
			'content' : content ,
			'time' : allGetTimestamp()			
		}),
		success : function (data) {
			allSetMessage(data);
		}
	});

}


function matrixSaveMatrixName(language) {

	var src = language==allActiveLanguage ? 'matrix-other' : 'matrix-default';

	matrixSaveContent(language, 'save_matrix_name',$('#'+src).val());

}


function matrixSaveCharacteristicLabel(language) {

	var src = language==allActiveLanguage ? 'characteristic-other' : 'characteristic-default';

	matrixSaveContent(language, 'save_characteristic_label',$('#'+src).val());

}


function matrixSaveMatrixNameAll() {

	matrixSaveMatrixName(allDefaultLanguage);
	matrixSaveMatrixName(allActiveLanguage);

}

function matrixSaveCharacteristicLabelAll() {

	matrixSaveCharacteristicLabel(allDefaultLanguage);
	matrixSaveCharacteristicLabel(allActiveLanguage);

}


function matrixSaveStateLabel(language) {

	var src = language==allActiveLanguage ? 'label-other' : 'label-default';

	matrixSaveContent(language, 'save_state_label',$('#'+src).val());

}

function matrixSaveStateText(language) {

	var src = language==allActiveLanguage ? 'text-other' : 'text-default';

	matrixSaveContent(language, 'save_state_text',$('#'+src).val());

}

function matrixMatrixDelete(id,matrix) {

	if (!allDoubleDeleteConfirm(_('matrix'),matrix)) return;

	$('#id').val(id);
	$('#action').val('delete');
	$('#theForm').submit();

}

function matrixSetStates(obj) {

	for(var i=0;i<obj.length;i++) {

		$('#states').
			append('<option ondblclick="window.open(\'state.php?id='+obj[i].id+'\',\'_self\')" value="'+obj[i].id+'">'+obj[i].label+'</option>').val(obj[i].id);

	}

	$("#states :last").removeAttr('selected');
	$("#states :first").attr('selected','selected');

}

function matrixGetStates(id) {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'get_states' ,
			'id' : id , 
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			//alert(data);
			obj = $.parseJSON(data);
			if (obj) matrixSetStates(obj);
		}
	});

}

function matrixSaveCharacteristic() {

	if ($('#characteristic-default').val()=='') {

		alert(_('You must enter a name.'));
		$('#characteristic-default').focus();

	} else {
	
		matrixSaveCharacteristicLabelAll();
		$('#theForm').submit()

	}

}

function matrixDeleteCharacteristic(name) {

	if(allDoubleDeleteConfirm(_('characteristic'),name)) {

		$('#action').val('delete');
		$('#theForm').submit();

	}

}

function matrixEditStateClick() {

	var c = $('#characteristics').val();
	var s = $('#states').val();

	if (c!=null && s!=null) window.open('state.php?char='+c+'&id='+s,'_self');

}

function maxtrixSetStateButtonLabel() {

	$('#newStateButton').html(sprintf(_('add new for "%s"'),$('#characteristics :selected').text().substr(0,$('#characteristics :selected').text().lastIndexOf(' '))));

}

function matrixCharacteristicsChange() {

	$('#states').find('*').remove();
	$('#states')[0].options.length = 0;

	matrixGetStates($('#characteristics').val());
	maxtrixSetStateButtonLabel();

}

function matrixAddStateClick() {

	var id = $('#characteristics').val();

	if (id==null) return;
	
	window.open('state.php?char='+id,'_self');

}

function matrixCheckStateForm() {

	if($('#label') && $('#label').val()=='') {

		alert(_('A name is required.'));
		$('#label').focus();
		
	} else
	if($('#text') && $('#text').val()=='') {

		alert(_('Text is required.'));
		$('#text').focus();
		
	} else
	if($('#uploadedfile') && $('#uploadedfile').val()=='')  {

		alert(_('A file is required.'));
		$('#uploadedfile').focus();

	} else
	if($('#lower') && $('#lower').val()=='')  {

		alert(_('A lower boundary is required.'));
		$('#lower').focus();

	} else
	if($('#upper') && $('#upper').val()=='')  {

		alert(_('An upper boundary is required.'));
		$('#upper').focus();

	} else
	if($('#mean') && $('#mean').val()=='')  {

		alert(_('A mean value is required.'));
		$('#mean').focus();

	} else
	if($('#sd') && $('#sd').val()=='')  {

		alert(_('A value for the standard deviation is required.'));
		$('#sd').focus();

	} else {

		$('#theForm').submit()

	}

}

function matrixSetTaxa(obj) {

	$('#taxa').find('*').remove();
	$('#taxa')[0].options.length = 0;

	if (!obj) return;

	if (obj.taxa) {

		for(var i=0;i<obj.taxa.length;i++) {
	
			$('#taxa').
				append('<option ondblclick="matrixDeleteTaxon()" value="'+obj.taxa[i].id+'">'+obj.taxa[i].taxon+'</option>').val(obj.taxa[i].id);
	
		}
		
	}

	if (obj.matrices) {

		$('#taxa').
			append('<option disabled="disabled">----------------------------------------------------------------------------------------------------</option>');

		for(var i=0;i<obj.matrices.length;i++) {
	
			$('#taxa').
				append('<option value="mx-'+obj.matrices[i].id+'">'+obj.matrices[i].default_name+'</option>').val('mx-'+obj.matrices[i].id);
	
		}
		
	}

}

function matrixDeleteTaxon() {

	var id = $("#taxa :selected").val()

	if (id==undefined || !confirm(_('Are you sure?'))) return;

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'remove_taxon' ,
			'id' : id , 
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			//alert(data);
			obj = $.parseJSON(data);
			matrixSetTaxa(obj);
		}
	});
	
}

function matrixAddLink(characteristic,taxon,state) {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'add_link' ,
			'characteristic' : characteristic , 
			'taxon' : taxon , 
			'state' : state , 
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			matrixGetLinks();
		}
	});
	
}

function matrixDeleteLink(id) {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'delete_link' ,
			'id' : id , 
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			matrixGetLinks();
		}
	});
	
}

function matrixGetLinks() {

	var characteristic = $("#characteristics :selected").val()
	var taxon = $("#taxa :selected").val()

	if (taxon==undefined || characteristic==undefined) return;

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'get_links',
			'characteristic' : characteristic, 
			'taxon' : taxon, 
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			//alert(data);
			matrixSetLinks($.parseJSON(data));
		}
	});
	
}

function matrixSetLinks(obj) {

	$('#links').find('*').remove();
	$('#links')[0].options.length = 0;

	if (!obj) return;

	for(var i=0;i<obj.length;i++) {

		$('#links').
			append('<option ondblclick="matrixDeleteLinks()" value="'+obj[i].id+'">'+obj[i].state+'</option>').val(obj[i].id);

	}

}

function matrixAddLinkClick() {

	var characteristic = $("#characteristics :selected").val()
	var state = $("#states :selected").val()
	var taxonOrMatrix = $("#taxa :selected").val()

	if (characteristic==undefined || taxonOrMatrix==undefined || state==undefined) return;
	
	matrixAddLink(characteristic,taxonOrMatrix,state);

}

function matrixRemoveLink() {

	var id = $("#links :selected").val()

	if (id==undefined || !confirm(_('Are you sure?'))) return;

	matrixDeleteLink(id);

}

function matrixGeneralSave(id,label,type,action) {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : action ,
			'id' : id , 
			'label' : label , 
			'language' :  type=='default' ? allDefaultLanguage : allActiveLanguage ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			//alert(data);
			allSetMessage(data);

		}
	});

}


function matrixSetActiveState(id) {

	$('#char-'+id).attr('selected', 'selected');

}

function matrixShowSortStates() {
	
	var id = $("#characteristics :selected").val();
	
	if (id==undefined) return;
	
	window.open('state_sort.php?sId='+id,'_self');
	
}
