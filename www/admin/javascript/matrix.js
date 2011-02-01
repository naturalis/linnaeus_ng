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
			obj = $.parseJSON(data);
			if (obj) matrixSetStates(obj);
		}
	});

}

function matrixSaveCharacteristic(id,label,type) {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'save_characteristic' ,
			'id' : id , 
			'label' : label , 
			'language' :  type=='default' ? allDefaultLanguage : allActiveLanguage ,
			'type' : $('#type').val(),
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
//			alert(data);
			allSetMessage(data);
		}
	});

}

function matrixDeleteCharacteristic() {

	if(allDoubleDeleteConfirm(_('characteristic'),$('#name').val())) {

		$('#action').val('delete');
		$('#theForm').submit();

	}

}

function maxtrixSetStateButtonLabel() {
	
	$('#newStateButton').val(sprintf(_('add new for "%s"'),$('#characteristics :selected').text().substr(0,$('#characteristics :selected').text().lastIndexOf(' '))));

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
	if($('#sd1') && $('#sd1').val()=='')  {

		alert(_('A value for one standard deviation is required.'));
		$('#sd1').focus();

	} else
	if($('#sd2') && $('#sd2').val()=='')  {

		alert(_('A value for two standard deviation is required.'));
		$('#sd2').focus();

	} else {

		$('#theForm').submit()

	}

}

function matrixSetTaxa(obj) {

	$('#taxa').find('*').remove();
	$('#taxa')[0].options.length = 0;

	if (!obj) return;

	for(var i=0;i<obj.length;i++) {

		$('#taxa').
			append('<option ondblclick="matrixDeleteTaxon()" value="'+obj[i].id+'">'+obj[i].taxon+'</option>').val(obj[i].id);

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
	var taxon = $("#taxa :selected").val()

	if (characteristic==undefined || taxon==undefined || state==undefined) return;
	
	matrixAddLink(characteristic,taxon,state);

}

function matrixRemoveLink() {

	var id = $("#links :selected").val()

	if (id==undefined || !confirm(_('Are you sure?'))) return;

	matrixDeleteLink(id);

}








