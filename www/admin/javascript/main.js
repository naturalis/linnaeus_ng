Array.prototype.inArray = function(value,key) {
	if (key==undefined) {
		for (var i in this) {
			if (this[i]==value) return i;
		}
	} else {
		for (var i in this) {
			if (this[i][key]==value) return i;
		}
	}
	return -1;
}

Array.prototype.empty = function() {
	while(this.length>0)
	{
		this.pop();
	}
}

String.prototype.repeat = function( num )
{
    return new Array( num + 1 ).join( this );
}

function q(m) {

	$('#debug-message').html(m);

}

function addSlashes(str) {
	str=str.replace(/\\/g,'\\\\');
	str=str.replace(/\'/g,'\\\'');
	str=str.replace(/\"/g,'\\"');
	str=str.replace(/\0/g,'\\0');
	return str;
}
function stripSlashes(str) {
	str=str.replace(/\\'/g,'\'');
	str=str.replace(/\\"/g,'"');
	str=str.replace(/\\0/g,'\0');
	str=str.replace(/\\\\/g,'\\');
	return str;
}

function isArray(obj) {

   if (obj.constructor.toString().indexOf("Array") == -1)
      return false;
   else
      return true;

}

function runFunction(name, arguments)
{
    var fn = window[name];
    if(typeof fn !== 'function')
        return;

    fn.apply(window, arguments);
}

/*
	usage:
	var example_template = "<div><span>%TITLE%</span>%CONTENT%</div>";
	var example_object = {
		TITLE: "this is the title",
		CONTENT: "<p>some content</p>"
	};
	var html = templateReplace( example_template , example_object	);
*/

function templateReplace(str_template, obj_replace)
{
	$.each(obj_replace, function(find, str_replace) {
		str_template = str_template.replace(RegExp("\\%" + find + "\\%","gi"),str_replace );
	});

	return str_template;
}


function prettyDialog(p)
{
	var b=[{ text: p.closetext ? p.closetext : _('Close'), click:function() { $( this ).dialog( "close" ); } }];
	if (p.buttons) b=p.buttons;

	$( "#dialog-message" ).dialog({
		draggable: false,
		resizable: false,
		modal: true,
		title: p.title,
		height: p.height ? p.height : 600,
		width: p.width ? p.width : 500,
		buttons: b
	});

	$( "#dialog-message-body-content" ).html(p.content);

};


var allShouldTranslate = true;
var allTranslations = Array();

function _(text) {

	if (!allShouldTranslate) return text;

	for(var i=0;i<allTranslations.length;i++) {
		if (allTranslations[i][0]==text) {
			return allTranslations[i][1];
		}
	}

	var translation = $.ajax({
	        type: "POST",
	        async: false,
	        url: "../utilities/ajax_interface.php",
	        data: ({text: text, action: 'translate'})
	        }).responseText;

	if (translation.length>0)
	{
		allTranslations[allTranslations.length]=[text,translation];
		return translation;
	}
	else
	{
		return text;
	}

}

function allPrevValSetUp(id) {

	var sel = $('#'+id);
	sel.attr('prev',sel.val());
	sel.change(function(data){
		var jqThis = $(this);
		sel.attr('prev',jqThis.val());
	});


}

var allAjaxHandle = false;
var allAjaxAborted = false;
var allAjaxAsynchMode = true;

function allGetTimestamp() {

	var tsTimeStamp= new Date().getTime();

	return tsTimeStamp;

}

function allTableColumnSort(col) {

	$('#key').val(col);
	$('#sortForm').submit();
}

function allToggleHelpVisibility() {

	$('#body-visible').toggleClass('body-collapsed body-visible');

}

function allDoubleDeleteConfirm(element,name) {


	if (confirm(
		sprintf(_('Are you sure you want to delete %s "%s"?'),element,name)+"\n"+
		_('Deletion will be irreversible.')
		)) {

		return (confirm(
			_('Final confirmation:')+"\n"+
			sprintf(_('Are you sure you want to delete %s "%s"?'),element,name)+"\n"+
			_('DELETION WILL BE IRREVERSIBLE.')
			));

	} else {

		return false;

	}

}

function allSetMessage(msg,delay)
{
	$('#message-container').show();
	$('#message-container').html(msg).delay(delay==undefined?1000:delay).fadeOut(500);
}

function allAjaxAbort(handle) {

	if (handle) {
		handle.abort();
	} else
	if (allAjaxHandle) {
		alert(_('Aborting'))
		allAjaxHandle.abort();
		allAjaxAborted = true;
	}

}

var autoSaveFreq = 120000;
var autoSaveInit = true;


function allSetAutoSaveFreq(freq) {

	autoSaveFreq = freq;

}

function allShowLoadingDiv() {

	$('#loadingdiv').removeClass('loadingdiv-invisible').addClass('loadingdiv-visible');
	$('#loadingdiv').offset({ left: $('#body-container').width()/2, top:  $(window).height()/2});

}

function allHideLoadingDiv() {

	$('#loadingdiv').removeClass('loadingdiv-visible').addClass('loadingdiv-invisible');

}

function allScrollTo(pos) {

	$('html, body').animate({scrollTop: pos}, 0);

}

var allLanguages = Array();
var allDefaultLanguage = false;
var allActiveLanguage = false;

function allAddLanguage(lan) {
	//[id,name,default?]
	allLanguages[allLanguages.length] = lan;

	if (lan[2]==1) allDefaultLanguage = lan[0];

}


function allDrawLanguages() {

	var b='';

	for(var i=0;i<allLanguages.length;i++) {
		if (allLanguages[i][2]!=1) {
			b = b +
				'<span class="project-language'+
					(allLanguages[i][0]==allActiveLanguage ? '-active' : '' )+
					'" onclick="allSwitchLanguage('+ allLanguages[i][0] +')">' +
				allLanguages[i][1] +
				'</span>&nbsp;';
		} else {
			allDefaultLanguage = allLanguages[i][0];
		}
	}

	$('#project-language-tabs').html(b);

}

var allActiveView = false;

function allSwitchLanguage(language) {

	// before switch
	switch (allActiveView) {
		case 'introduction':
			allAjaxAsynchMode = false;
			contentSaveContentActive();
			break;
		case 'freemodule':
			allAjaxAsynchMode = false;
			freemodSaveContentActive();
			break;
	}

	allActiveLanguage = language;
	allDrawLanguages();

	// after switch
	switch (allActiveView) {
		case 'ranklabels':
			taxonGetRankLabels(allActiveLanguage);
			break;
		case 'page':
			taxonGetPageLabels(allActiveLanguage);
			break;
		case 'sections':
			taxonGetSectionLabels(allActiveLanguage);
			break;
		case 'commonnames':
			taxonGetCommonnameLabels(allActiveLanguage);
			break;
		case 'keystepedit':
			keyGetKeystepContent(allActiveLanguage);
			break;
		case 'introduction':
			contentGetContentActive();
			allAjaxAsynchMode = true;
			break;
		case 'freemodule':
			freemodGetContentActive();
			allAjaxAsynchMode = true;
			break;
		case 'matrixname':
			matrixGetMatrixName(allActiveLanguage);
			break;
		case 'matrixchar':
			matrixGetCharacteristicLabel(allActiveLanguage);
			break;
		case 'matrixstate':
			matrixGetStateLabel(allActiveLanguage);
			matrixGetStateText(allActiveLanguage);
			break;
		case 'geotypes':
			mapGetTypeLabels(allActiveLanguage);
			break;

	}

}

function allGeneralGetLabels(language,action,postFunction,id,alturl)
{
	/*
		please take note that it depends on the url of the file
		calling this function exactly *which* version of
		ajax_interface.php is called. for instance, called from
		  /admin/views/key/step_show.php
		it will be
		  /admin/views/key/ajax_interface.php
		while called from
		  /admin/views/species/ranklabels.php
		it will be
		  /admin/views/species/ajax_interface.php
		which is an entirely different file
	*/

	allShowLoadingDiv();

	url=alturl ? alturl : "ajax_interface.php";

	allAjaxHandle = $.ajax({
		url : url,
		type: "POST",
		data : ({
			'action' : action ,
			'language' : language ,
			'id' : (id ? id : false) ,
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data)
		{
			//console.log( data);
			if (data.length) {
                obj = $.parseJSON(data);
                runFunction(postFunction, [obj, language]);
            }
			allHideLoadingDiv();
		}
	})

}


function allShowMedia(url,name) {

	if (!url) return;
	$.prettyPhoto.open(url,'',name);

}

function goNavigate(val,form) {

	var formId = form ? '#'+form : '#theForm';

	$('<input type="hidden" name="start">').val(val).appendTo(formId);
	$(formId).submit();

}

function showDialog(title,content) {

	$.modaldialog.prompt(content, {
		title : title ? title : _('Enter value'),
		width: 350
	});

}
function allCreateButton(label,action,id,fixedHeight)
{
	//<script> allCreateButton('click me!','window.open(\'char.php?id=\'+$(\'#characteristics\').val(),\'_self\');',500); </script>
	document.write('<div class="all-fake-button" '+(fixedHeight ? 'style="height:'+fixedHeight+'px;"' : '' )+' '+(id ? 'id="'+id+'"' : '' )+' onmousedown="$(this).addClass(\'all-fake-button-shift\')"  onmouseup="$(this).removeClass(\'all-fake-button-shift\')"" onclick="'+action+'">'+label+'</div>');
}


function prettyPhotoInit()
{
 	$("a[rel^='prettyPhoto']").prettyPhoto({
		allow_resize:true,
		animation_speed:50,
 		opacity: 0.70,
		show_title: false,
 		overlay_gallery: false,
 		social_tools: false
 	});
}

function allSaveDragOrder(form,vari)
{
	/*
		usage:

		<table id="drag-list" class="grid">
		<tr type="drag-row" drag-id="{id}">

		$(document).ready(function(){

			allInitDragtable();

		})

	*/
	form = form ? form : 'theForm';
	vari = vari ? vari : 'newOrder';

	$('tr[type="drag-row"]').each(function(i){
		$('#'+form).append('<input type="hidden" name="'+vari+'[]" value="'+$(this).attr('drag-id')+'">').val($(this).attr('drag-id'));
	});

	$('#'+form).submit();
}

function allInitDragtable(functionOnDrop)
{
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

	$("#drag-list tbody").sortable({
		helper: fixHelper
	}).disableSelection();

	if (functionOnDrop) {
		$("#drag-list tbody").sortable({
			deactivate:function(event,ui) {
				functionOnDrop();
			}
		});
	}

}


function allSetSomething(name,value)
{
	$.ajax({
		type: "POST",
		async: false,
		url: "../utilities/ajax_interface.php",
		data: ({name: name, value: value, action: 'set_something'})
	}).success();
}

function allGetSomething(name,callback)
{
	$.ajax({
		type: "POST",
		async: false,
		url: "../utilities/ajax_interface.php",
		data: ({name: name, action: 'get_something'})
	}).success(function(data){callback($.parseJSON(data));});
}

function allStickElementUnderElement(ele1,ele2,resize)
{
	var inputField = $('#'+ele1);
	var fieldDiv = $('#'+ele2);
	var sf_pos    = inputField.offset();
	var sf_top    = sf_pos.top;
	var sf_left   = sf_pos.left;
	var sf_height = inputField.height();

	fieldDiv.css("position","absolute");
	fieldDiv.css("left", sf_left);
	fieldDiv.css("top", sf_top + sf_height + 6);

	if (resize===true)
	{
		var sf_width  = inputField.width();
		fieldDiv.css("width", sf_width);
	}

}

function delay(callback, ms) {
	var timer = 0;
	return function() {
		var context = this, args = arguments;
		clearTimeout(timer);
		timer = setTimeout(function () {
			callback.apply(context, args);
		}, ms || 0);
	};
}


$(document).ready(function()
{
	$('<div id="dialog-message" title="title" style="display:none;"><div id="dialog-message-body-content"></div></div>').appendTo('body');
});
