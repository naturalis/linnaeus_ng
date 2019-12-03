var interfaceOldVals = Array();
var interfaceBeingEdited = Array();
var interfaceFinalCounter = null;
var interfaceNextStart = -1;

function interfaceSaveLabel(id,lId,newVal,msgId,postFunction) {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'save_translation' ,
			'param' : {
				'id': id,
				'lan': lId,
				'newVal': newVal
			} , 
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			$('#'+msgId).html(data).fadeOut(1000);
			if (postFunction) eval(postFunction);
		}
	});
	
}

function interfaceEnableTransEdit(ele) {
	
	var idx = $(ele).attr('counter');

	var oId = $(ele).attr('id');
	if (interfaceBeingEdited[idx]==true || oId==undefined || oId=='') return;
	
	var nId = 'trans2-'+oId;

	// LINNA-944: escape double quotes, otherwise entry is truncated
	interfaceOldVals[idx] = $(ele).html().replace(/"/g, "&quot;");

	$(ele).html('<input type="text" id="'+nId+'" value="'+interfaceOldVals[idx]+'" style="width:240px">');

	$('#trans2-'+oId).keyup(function(e) {
		interfaceDoKeyUp(e.which,idx,oId,nId);
	});
	
	$('#'+nId).focus();
	interfaceBeingEdited[idx]=true;
	
}

function interfaceDoKeyUp(key,idx,oId,nId) {
	
	if (key==13) { // return

		var x = (parseInt(idx)+1);
		if (x>interfaceFinalCounter && interfaceNextStart!=-1) {
			$('<input type="hidden" name="immediateEdit">').val('1').appendTo('#theForm');
			var postFunction = 'goNavigate(interfaceNextStart);';
		} else {
			var postFunction = 'interfaceEnableTransEdit($(\'[counter='+x+']\'));';
		}

		var newVal = $('#'+nId).val();
		if (newVal != interfaceOldVals[idx]) {
			var bits = oId.split('-');
			interfaceSaveLabel(bits[1],bits[2],newVal,'msg-'+idx,postFunction);
		}
		$('#'+oId).html(newVal);
		interfaceBeingEdited[idx]=false;

	} else

	if (key==27) { //esc
		$('#'+oId).html(interfaceOldVals[idx]);
		interfaceBeingEdited[idx]=false;
	}
	
}

function interfaceDeleteTag(id) {

	if ($('#currStart').val()) $('<input type="hidden" name="start">').val($('#currStart').val()).appendTo('#theForm');
	$('<input type="hidden" name="action">').val('delete').appendTo('#theForm');
	$('<input type="hidden" name="id">').val(id).appendTo('#theForm');
	$('#theForm').submit();

}
