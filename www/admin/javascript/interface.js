var interfaceOldVals = Array();
var interfaceBeingEdited = Array();

function interfaceSaveLabel(id,lId,newVal,msgId) {

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
		}
	})
	
}

function interfaceEnableTransEdit(ele) {
	
	var idx = $(ele).attr('counter');

	var oId = $(ele).attr('id');
	if (interfaceBeingEdited[idx]==true || oId==undefined || oId=='') return;
	
	var nId = 'trans2-'+oId;
	
	interfaceOldVals[idx] = $(ele).html();
	
	$(ele).html('<input type="text" id="'+nId+'" value="'+interfaceOldVals[idx]+'" >');

	$('#trans2-'+oId).keyup(function(e) {
		interfaceDoKeyUp(e.which,idx,oId,nId);
	});
	
	$('#'+nId).focus();
	interfaceBeingEdited[idx]=true;
	
}

function interfaceDoKeyUp(key,idx,oId,nId) {
	
	if (key==13) { // return
		var newVal = $('#'+nId).val();
		if (newVal != interfaceOldVals[idx]) {
			var bits = oId.split('-');
			interfaceSaveLabel(bits[1],bits[2],newVal,'msg-'+idx);
		}
		$('#'+oId).html(newVal);
		interfaceBeingEdited[idx]=false;
	} else
	if (key==27) { //esc
		$('#'+oId).html(interfaceOldVals[idx]);
		interfaceBeingEdited[idx]=false;
	}
	
}