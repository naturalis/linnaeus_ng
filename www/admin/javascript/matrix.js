function matrixGetContent(action,id) {

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : action ,
			'id' : id ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {

			setOverlayContent(data);

		}
	});
	

}

function maxtrixCharacteristicAdd() {

	matrixGetContent('page_char_add');

	showOverlay();

}


function maxtrixTaxonAdd() {

	showOverlay();
	
	setOverlayContent();

}
