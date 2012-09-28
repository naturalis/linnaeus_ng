function keyDoChoice(id) {

	addFormVal('choice',id);
	addFormVal('step',null);
	goForm('../key/');

}

function keyDoStep(id) {

	addFormVal('choice',null);
	addFormVal('step',id);
	goForm('../key/');

}

var keyFullPathVisibility = false;

function keyToggleFullPath() {

	var id = '#path-full';

	if (keyFullPathVisibility) {

		$(id).removeClass().addClass('full-invisible');

	} else {

		var pos = $('#toggle').position();
		$(id).removeClass().addClass('full');
		$(id).offset({ left: pos.left, top: pos.top+23});

	}

	keyFullPathVisibility = !keyFullPathVisibility;

}

function getData(action) {

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			'action' : action 
		}),
		success : function (data) {
			//alert(data);
			obj = $.parseJSON(data);
		}
	})
	
}

function showRemaining() {

	$('#excluded').css('display','none');
	$('#remaining').css('display','block');
	$('#eLi').removeClass('category-active');
	$('#rLi').addClass('category-active');
	getData('store_remaining');

}

function showExcluded() {

	$('#remaining').css('display','none');
	$('#excluded').css('display','block');
	$('#rLi').removeClass('category-active');
	$('#eLi').addClass('category-active');
	getData('store_excluded');

}

