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

sprintfWrapper = {
 
	init : function () {
 
		if (typeof arguments == "undefined") { return null; }
		if (arguments.length < 1) { return null; }
		if (typeof arguments[0] != "string") { return null; }
		if (typeof RegExp == "undefined") { return null; }
 
		var string = arguments[0];
		var exp = new RegExp(/(%([%]|(\-)?(\+|\x20)?(0)?(\d+)?(\.(\d)?)?([bcdfosxX])))/g);
		var matches = new Array();
		var strings = new Array();
		var convCount = 0;
		var stringPosStart = 0;
		var stringPosEnd = 0;
		var matchPosEnd = 0;
		var newString = '';
		var match = null;
 
		while (match = exp.exec(string)) {
			if (match[9]) { convCount += 1; }
 
			stringPosStart = matchPosEnd;
			stringPosEnd = exp.lastIndex - match[0].length;
			strings[strings.length] = string.substring(stringPosStart, stringPosEnd);
 
			matchPosEnd = exp.lastIndex;
			matches[matches.length] = {
				match: match[0],
				left: match[3] ? true : false,
				sign: match[4] || '',
				pad: match[5] || ' ',
				min: match[6] || 0,
				precision: match[8],
				code: match[9] || '%',
				negative: parseInt(arguments[convCount]) < 0 ? true : false,
				argument: String(arguments[convCount])
			};
		}
		strings[strings.length] = string.substring(matchPosEnd);
 
		if (matches.length == 0) { return string; }
		if ((arguments.length - 1) < convCount) { return null; }
 
		var code = null;
		var match = null;
		var i = null;
 
		for (i=0; i<matches.length; i++) {
 
			if (matches[i].code == '%') { substitution = '%' }
			else if (matches[i].code == 'b') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(2));
				substitution = sprintfWrapper.convert(matches[i], true);
			}
			else if (matches[i].code == 'c') {
				matches[i].argument = String(String.fromCharCode(parseInt(Math.abs(parseInt(matches[i].argument)))));
				substitution = sprintfWrapper.convert(matches[i], true);
			}
			else if (matches[i].code == 'd') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 'f') {
				matches[i].argument = String(Math.abs(parseFloat(matches[i].argument)).toFixed(matches[i].precision ? matches[i].precision : 6));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 'o') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(8));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 's') {
				matches[i].argument = matches[i].argument.substring(0, matches[i].precision ? matches[i].precision : matches[i].argument.length)
				substitution = sprintfWrapper.convert(matches[i], true);
			}
			else if (matches[i].code == 'x') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(16));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 'X') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(16));
				substitution = sprintfWrapper.convert(matches[i]).toUpperCase();
			}
			else {
				substitution = matches[i].match;
			}
 
			newString += strings[i];
			newString += substitution;
 
		}
		newString += strings[i];
 
		return newString;
 
	},
 
	convert : function(match, nosign){
		if (nosign) {
			match.sign = '';
		} else {
			match.sign = match.negative ? '-' : match.sign;
		}
		var l = match.min - match.argument.length + 1 - match.sign.length;
		var pad = new Array(l < 0 ? 0 : l).join(match.pad);
		if (!match.left) {
			if (match.pad == "0" || nosign) {
				return match.sign + pad + match.argument;
			} else {
				return pad + match.sign + match.argument;
			}
		} else {
			if (match.pad == "0" || nosign) {
				return match.sign + match.argument + pad.replace(/0/g, ' ');
			} else {
				return match.sign + match.argument + pad;
			}
		}
	}
}

sprintf = sprintfWrapper.init;

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
		_('All corresponding data will be irreversibly deleted.')
		)) {
	
		return (confirm(
			_('Final confirmation:')+"\n"+
			sprintf(_('Are you sure you want to delete %s "%s"?'),element,name)+"\n"+
			_('ALL CORRESPONDING DATA WILL BE IRREVERSIBLY DELETED.')
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