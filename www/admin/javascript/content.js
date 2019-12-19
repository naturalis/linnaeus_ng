var currentSubject = false;

function contentSaveContent(language,editorName,postFunction) {

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
			if (postFunction) eval(postFunction);
		}
	});

}

function contentSaveContentDefault(postFunction) {

	contentSaveContent(allDefaultLanguage,'content-default',postFunction);

}

function contentSaveContentActive(postFunction) {

	contentSaveContent(allActiveLanguage,'content-other',postFunction);

}

function contentSaveContentAll(postFunction) {
	
	contentSaveContentDefault();
	contentSaveContentActive(postFunction);

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
		success : function (data)
		{
			tinyMCE.get(editorName).setContent(data ? data : '');
			allHideLoadingDiv();
			tMCEFirstUndoPurge(editorName);
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

function contentSwitchPage() {
	
	if ((currentSubject!=false) && ($('#pageSelector option:selected').text()!==currentSubject)) {

		contentSaveContentAll("window.open($('#pageSelector option:selected').val(),'_self')");

	}

}

function contentPreviewContent() {
	
	contentSaveContentAll();
	contentSaveContentActive();
	$('#theForm').attr('action','preview.php');
	$('#theForm').submit();

}

function contentRunAutoSave() {

	if (!autoSaveInit) contentSaveContentAll();

	autoSaveInit = false;

	setTimeout("contentRunAutoSave()", autoSaveFreq);

}