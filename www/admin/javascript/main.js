function q(m) {

	$('#debug-message').html(m);

}

Array.prototype.inArray = function(value) {
	for (var i in this) { 
		if (this[i]==value) return i; 
	}
	return -1;
}

function isArray(obj) {
	
   if (obj.constructor.toString().indexOf("Array") == -1)
      return false;
   else
      return true;

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
		'Are you sure you want to delete '+element+' "'+name+'"?\n'+
		'All corresponding data will be irreversibly deleted.'
		)) {
	
		return (confirm(
			'Final confirmation:\n'+
			'Are you sure you want to delete '+element+' "'+name+'"?\n'+
			'ALL CORRESPONDING DATA WILL BE IRREVERSIBLY DELETED.'
			));
	
	} else {

		return false;

	}

}

function allSetMessage(msg,err) {

	$('#message-container').show();
	$('#message-container').html(msg).delay(1000).fadeOut(500);

}

function allAjaxAbort(handle) {

	if (handle) {
		handle.abort();
	} else
	if (allAjaxHandle) {
		alert('Aborting')
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
	$('#loadingdiv').offset({ left: $('#body-container').width()/2, top: $('#body-container').height() / 2});

}

function allHideLoadingDiv() {

	$('#loadingdiv').removeClass('loadingdiv-visible').addClass('loadingdiv-invisible');

}

