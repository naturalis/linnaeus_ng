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

function q(m) {

	$('#debug-message').html(m);

}

function isArray(obj) {
	
   if (obj.constructor.toString().indexOf("Array") == -1)
      return false;
   else
      return true;

}

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

	allTranslations[allTranslations.length]=[text,translation];

	return translation;

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
		_('Deletion will be irreversibly.')
		)) {
	
		return (confirm(
			_('Final confirmation:')+"\n"+
			sprintf(_('Are you sure you want to delete %s "%s"?'),element,name)+"\n"+
			_('DELETION WILL BE IRREVERSIBLY.')
			));
	
	} else {

		return false;

	}

}

function allSetMessage(msg,delay) {

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

var heartbeatUserId = false;
var heartbeatApp = false;
var heartbeatCtrllr = false;
var heartbeatView = false;
var heartbeatParams = Array();
var heartbeatFreq = 120000;
var autosaveFreq = 120000;

function allSetHeartbeatFreq(freq) {

	heartbeatFreq = freq;

}

function allSetHeartbeat(userid,app,ctrllr,view,params) {

	if (userid) heartbeatUserId = userid;
	if (app) heartbeatApp = app;
	if (ctrllr) heartbeatCtrllr = ctrllr;
	if (view) heartbeatView = view;
	if (params) heartbeatParams = params;

	$.ajax({
		url : "../utilities/ajax_interface.php",
		type: "GET",
		data : ({
			'user_id' : heartbeatUserId ,
			'app' : heartbeatApp ,
			'ctrllr' : heartbeatCtrllr ,
			'view' : heartbeatView ,
			'params' : heartbeatParams ,
			'action' : 'heartbeat',
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			//alert(data);
		}
	});

	setTimeout ("allSetHeartbeat()", heartbeatFreq);

}

function allSetAutoSaveFreq(freq) {

	autosaveFreq = freq;

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

	allActiveLanguage = language;
	allDrawLanguages();

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
		case 'choiceedit':
			keyGetChoiceContent(allActiveLanguage);
			break;			
	}

}

function allGeneralGetLabels(language,action,postFunction,id) {

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

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : action ,
			'language' : language ,
			'id' : (id ? id : false) , 
			'time' : allGetTimestamp()
		}),
		async: allAjaxAsynchMode,
		success : function (data) {
			//alert(data);
			obj = $.parseJSON(data);
			eval(postFunction+'(obj,language)');
			allHideLoadingDiv();
		}
	})
	
}


function allShowMedia(url,name) {

	$.colorbox({
		href:url,
		title:name,
		transition:"elastic", 
		maxWidth:800,
		width:"100%",
		opacity:0
	});

}
