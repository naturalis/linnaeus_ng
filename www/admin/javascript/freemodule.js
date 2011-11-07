function freemodSaveContent(language,type) {

	var id = $('#id').val();

	if (id==undefined) return;

	tinyMCE.triggerSave();

	var topic = $('#topic-'+type).val();
	var content = tinyMCE.get('content-'+type).getContent();

	$.ajax({
		url : "ajax_interface.php" ,
		type: "POST",
		data : ({
			'action' : 'save_content' ,
			'id' : id ,
			'language' : language ,
			'topic' : topic,
			'content' : content ,
			'time' : allGetTimestamp()			
		}),
		success : function (data) {
			allSetMessage(data);
		}
	});

}

function freemodSaveContentDefault() {

	freemodSaveContent(allDefaultLanguage,'default');

}

function freemodSaveContentActive() {

	freemodSaveContent(allActiveLanguage,'other');

}

function freemodSaveContentAll() {
	
	freemodSaveContentDefault();
	freemodSaveContentActive();

}

function freemodGetContent(language,type) {

	var id = $('#id').val();

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
			tinyMCE.get('content-'+type).setContent('');
			$('#topic-'+type).val('');
			obj = $.parseJSON(data);
			if (obj) {
				if (obj.topic) $('#topic-'+type).val(obj.topic);
				if (obj.content) tinyMCE.get('content-'+type).setContent(obj.content);
			}
			allHideLoadingDiv();
		}
	});

}


function freemodGetContentDefault() {

	freemodGetContent(allDefaultLanguage,'default');

}

function freemodGetContentActive() {

	freemodGetContent(allActiveLanguage,'other');

}

function freemodGetDataAll() {

	allShowLoadingDiv();
	freemodGetContentDefault();
	freemodGetContentActive();

}

function freemodDeletePageImage(id) {

	if (confirm(_('Are you sure?'))) {

		freemodSaveContentAll();
		$('#action').val('deleteImage');
		$('#theForm').submit();
		
	}

}


function freemodDeletePage(id) {

	if (!allDoubleDeleteConfirm(_('the page'),$('#topic-default').val())) return;

	$('#action').val('delete');
	$('#theForm').submit();
	

}

function freemodDoPreview() {

	freemodSaveContentAll();
	$("#action").val('preview');
	$('#theForm').submit();

}


function freemodSaveContentAll() {
	
	freemodSaveContentDefault();
	freemodSaveContentActive();

}