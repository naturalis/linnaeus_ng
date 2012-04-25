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
