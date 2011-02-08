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

	contentSaveContent(allDefaultLanguage,'taxon-content-default');

}

function contentSaveContentActive() {

	contentSaveContent(allActiveLanguage,'taxon-content-other');

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

	contentGetContent(allDefaultLanguage,'taxon-content-default');

}

function contentGetContentActive() {

	contentGetContent(allActiveLanguage,'taxon-content-other');

}

function contentGetDataAll() {

	allShowLoadingDiv();
	contentGetContentActive();
	contentGetContentDefault();

}
