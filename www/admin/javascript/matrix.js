function matrixMatrixDelete(id,matrix) {

	if (!allDoubleDeleteConfirm(_('matrix'),matrix)) return;

	$('#id').val(id);
	$('#action').val('delete');
	$('#theForm').submit();

}

function matrixSetStates(obj) {

	for(var i=0;i<obj.length;i++) {

		$('#states').
			append('<option ondblclick="window.open(\'state.php?id='+obj[i].id+'\',\'_self\')" value="'+obj[i].id+'">'+obj[i].label+'</option>').
			val(obj[i].id);

	}

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















