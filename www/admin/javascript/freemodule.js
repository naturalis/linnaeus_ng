

function freemodSaveContent(language,type)
{

	var id = $('#id').val();

	if (id==undefined) return;
	if ($('#topic-'+type).val()==undefined) return;

	tinyMCE.triggerSave();

	var topic = $('#topic-'+type).val();
	var content = tinyMCE.get('content-'+type).getContent();
	var hide_from_index = $('#hide_from_index' ).prop( 'checked' ) ? 1 : 0;
	
	$.ajax({
		url : "ajax_interface.php" ,
		type: "POST",
		data : ({
			'action' : 'save_content' ,
			'id' : id ,
			'language' : language ,
			'topic' : topic,
			'content' : content ,
			'hide_from_index' : hide_from_index ,
			'time' : allGetTimestamp()			
		}),
		success : function (data)
		{
			//console.log(data);
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
	if ($('#topic-'+type).val()==undefined) return;

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
			tMCEFirstUndoPurge('content-'+type);
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

	if (allDefaultLanguage)
		$('#theForm').append('<input type="hidden" name="language-default" value="'+allDefaultLanguage+'">').val(allDefaultLanguage);

	if (allActiveLanguage)
		$('#theForm').append('<input type="hidden" name="language-other" value="'+allActiveLanguage+'">').val(allActiveLanguage);

	var hide_from_index = $('#hide_from_index' ).prop( 'checked' ) ? '1' : '0'
	$('#theForm').append('<input type="hidden" name="hide_from_index" value="'+hide_from_index+'">').val(hide_from_index);

	$("#action").val('preview');
	$('#theForm').submit();

}


function freemodSaveContentAll() {
	
	freemodSaveContentDefault();
	freemodSaveContentActive();

}

function freemodRunAutoSave() {

	if (!autoSaveInit) freemodSaveContentAll();

	autoSaveInit = false;

	setTimeout("freemodRunAutoSave()", autoSaveFreq);

}

function freemodSortAlpha() {

	$('#theForm').append('<input type="hidden" name="sortAlpha" value="1">').val('1');
	$('#theForm').submit();

}