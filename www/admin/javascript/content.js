function contentSaveContent(language,editorName) {

	var id = $('#subject').val();
	
	if (id==undefined) return;
	
	var content = tinyMCE.get(editorName).getContent();

	$.ajax({
		url : "ajax_interface.php" ,
		type: "POST",
		data : ({
			'action' : 'save_content' ,
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

function contentSaveContentDefault() {

	contentSaveContent(allDefaultLanguage,'content-default');

}

function contentSaveContentActive() {

	contentSaveContent(allActiveLanguage,'content-other');

}

function contentSaveContentAll() {
	
	contentSaveContentDefault();
	contentSaveContentActive();

}

function contentGetContent(language,editorName) {

	var id = $('#subject').val();
	
	if (id==undefined) return;

	$.ajax({
		url : "ajax_interface.php" ,
		type: "POST",
		data : ({
			'action' : 'get_content' ,
			'id' : id ,
			'language' : language ,
			'time' : allGetTimestamp()			
		}),
		success : function (data) {
			tinyMCE.get(editorName).setContent(data ? data : '');
			allHideLoadingDiv();
		}
	});

}


function contentGetContentDefault() {

	contentGetContent(allDefaultLanguage,'content-default');

}

function contentGetContentActive() {

	contentGetContent(allActiveLanguage,'content-other');

}

function contentGetDataAll() {

	allShowLoadingDiv();
	contentGetContentActive();
	contentGetContentDefault();

}
