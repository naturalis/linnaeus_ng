var projectId=null;

String.prototype.trim = function () {
    return this.replace(/^\s+|\s+$/g, "");
};

String.prototype.htmlEntities = function () {
	return this.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
};

(function($)
{
    $.fn.removeClassRegEx = function(regex)
    {
        var classes = $(this).attr('class');

        if(!classes || !regex) return false;

        var classArray = [];
        classes = classes.split(' ');

        for(var i=0, len=classes.length; i<len; i++)
            if(!classes[i].match(regex)) classArray.push(classes[i])

        $(this).attr('class', classArray.join(' '));
    };
    $.fn.size = function()
	{
		return $(this).length;
	};
})(jQuery);

function rndStr(length) {
	length = length ? length : 16;
	var s ='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	var d = '';
	for(var i=0;i<length;i++) {
		var n = Math.random()*s.length;
		n = Math.floor(n);
		d = d + s.substr(n,1);
	}
	return d;
}

function isArray(obj) {
   if (obj.constructor.toString().indexOf("Array") == -1)
      return false;
   else
      return true;
}


function ucwords(str) {
  //  discuss at: http://phpjs.org/functions/ucwords/
  // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // improved by: Waldo Malqui Silva
  // improved by: Robin
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Onno Marsman
  //    input by: James (http://www.james-bell.co.uk/)
  //   example 1: ucwords('kevin van  zonneveld');
  //   returns 1: 'Kevin Van  Zonneveld'
  //   example 2: ucwords('HELLO WORLD');
  //   returns 2: 'HELLO WORLD'

  return (str + '')
    .replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
      return $1.toUpperCase();
    });
}


//this function simply gets the window scroll position, works in all browsers
function getPageScroll() {
	var yScroll;
	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
	} else if (document.documentElement && document.documentElement.scrollTop) {
		yScroll = document.documentElement.scrollTop;
	} else if (document.body) {
		yScroll = document.body.scrollTop;
	}
	return yScroll
}

function addSlashes(str) {
	if (!str) return;
	str=str.replace(/\\/g,'\\\\');
	str=str.replace(/\'/g,'\\\'');
	str=str.replace(/\"/g,'\\"');
	str=str.replace(/\0/g,'\\0');
	return str;
}

function stripSlashes(str) {
	if (!str) return;
	str=str.replace(/\\'/g,'\'');
	str=str.replace(/\\"/g,'"');
	str=str.replace(/\\0/g,'\0');
	str=str.replace(/\\\\/g,'\\');
	return str;
}

function stripTags(str) {
	if (str)
		return str.replace(/(<([^>]+)>)/ig,"");
}

function allGetTimestamp() {

	var tsTimeStamp= new Date().getTime();

	return tsTimeStamp;

}

function setCursor(type,ele) {

	ele = ele ? '#'+ele.replace('#','') : 'body';

	$(ele).css('cursor',type ? type : 'default' );

}

function glossTextOver(id,caller) {

	if (!id) return;

	$('#hint-balloon').bind('click', function() {
		goGlossaryTerm(id);
	});

	var pos = $(caller).position();
	$('#hint-balloon').show();
	$('#hint-balloon').offset({ left: pos.left + 10, top: pos.top - $('#hint-balloon').height() - 12});

	$('#hint-balloon').load('../glossary/hint.php?id='+id);

	$('#hint-balloon').hover(
		  function () {},
		  function () {
			$('#hint-balloon').hide();
		  }
	);

}

function _(text) {

	// can be single string or array; returns single string or array.
	// needs local caching.
	var translation = $.ajax({
	        type: "POST",
	        async: false,
	        url: "../utilities/ajax_interface.php",
	        data: ({text: text, action: 'translate'})
	        }).responseText;

	return $.parseJSON(translation);

}



function getCurrentProjectId()
{
	return $('meta[name="lng-project-id"]').attr("content");
}




function showMedia(url,name) {
	// $.prettyPhoto.open(url,'',name);

	$.fancybox.open([
		{
			src : url,
			title : name,
			type : 'image'
		}
	]);
}

function showVideo(url,name) {
/*
	var content =
		'<video width="320" height="240" controls="controls">'+
			'<source src="'+url+'" type="mime_type" />'+
			'<embed src="'+url+'" width="320" height="240" />'+
		'</video> ';
*/
	var content = '<video src="' + url + '" controls>';

	showDialog(name,content);

}

var allIsDialogOpen = false;

function showDialog(title,content,vars,resize) {
	if ($('#jDialog').length!=0) {

		$('#jDialog').html(null);

		var buttons = [];

		if (vars.showOk)
			buttons = [{text:_("ok"), click: function() {jDialogOk();}}];

		buttons.push({text:_("sluiten"), click: function() {jDialogCancel();}});

		$("#jDialog").dialog({
			resizable: false,
			modal: true,
			title: title,
			autoOpen: false,
			buttons: buttons,
			width: "auto",
			position: { my: "center", at: "center", of: window }
		});

		$("#jDialog").html(content);
		$("#jDialog").dialog( "open" );
	} else {
		if (!vars) {
			vars = {};
			vars.width = 350;
		}
		vars.title = title ? title : '';

		$.modaldialog.prompt(content,vars);
		if (resize) {
			$('#dialog-content').css('min-height',0);
			$('#dialog-content-inner').css('min-height',0);
			$('#lookup-DialogContent').css('height','auto');
		}
	}

	allIsDialogOpen = true;
}

function closeDialog() {
	$('#filterDialogContainer').hide();
}

function getTimestamp() {

	var tsTimeStamp= new Date().getTime();

	return tsTimeStamp;

}

function addFormVal(name,val) {

	$('<input type="hidden" name="'+name+'">').val(val==null ? '' : val).appendTo('#theForm');

}

function goForm(url) {

	if (url) $('#theForm').attr('action',url);
	$('#theForm').submit();

}

function goTaxon(id,cat)
{
	var u = '../species/taxon.php?id='+id+(cat ? '&cat='+cat : '');
	window.open(chkPIDInLink(u,'pid='+getCurrentProjectId()),'_self');
}

function goHigherTaxon(id)
{
	var u = '../highertaxa/taxon.php?id='+id+(cat ? '&cat='+cat : '');
	window.open(chkPIDInLink(u,'pid='+getCurrentProjectId()),'_self');
}

function goMenuModule(id)
{
	var u = '../module/index.php?modId='+id;
	window.open(chkPIDInLink(u,'pid='+getCurrentProjectId()),'_self');
}

function goAlpha(letter,url) {
	//!
	addFormVal('letter',letter);
	goForm(url ? url : null);

}

function goLiterature(id)
{
	var u = '../literature/reference.php?id='+id;
	window.open(chkPIDInLink(u,'pid='+getCurrentProjectId()),'_self');
}

function goGlossaryTerm(id)
{
	var u = '../glossary/term.php?id='+id;
	window.open(chkPIDInLink(u,'pid='+getCurrentProjectId()),'_self');
}

function goModuleTopic(id,modId)
{
	var u = '../module/topic.php?id='+id+(modId ? '&modId='+modId : '');
	window.open(chkPIDInLink(u,'pid='+getCurrentProjectId()),'_self');
}

function goContent(id)
{
	var u = '../linnaeus/index.php?id='+id;
	window.open(chkPIDInLink(u,'pid='+getCurrentProjectId()),'_self');
}

function goContentPage(subject)
{
	var u = '../linnaeus/content.php?sub='+subject;
	window.open(chkPIDInLink(u,'pid='+getCurrentProjectId()),'_self');
}

function goMatrix(id)
{
	var u = '../matrixkey/use_matrix.php?id='+id;
	window.open(chkPIDInLink(u,'pid='+getCurrentProjectId()),'_self');
}

function goMap(id,url) {
	//!
	if (id) addFormVal('id',id);
	goForm(url ? url : '../mapkey/examine_species.php');
}

function goIntroductionTopic(id)
{
	var u = '../introduction/topic.php?id='+id;
	window.open(chkPIDInLink(u,'pid='+getCurrentProjectId()),'_self');
}

function goNavigator()
{
	var u = '../linnaeus/index.php';
	window.open(chkPIDInLink(u,'pid='+getCurrentProjectId()),'_self');
}

function goIconGrid()
{
	var u = '../linnaeus/index.php?show=icongrid';
	window.open(chkPIDInLink(u,'pid='+getCurrentProjectId()),'_self');
}

function goNavigate(id,field,url) {
	//!
	addFormVal(field ? field : 'start',id);
	goForm(url ? url : null);

}

var searchBoxSelected = false;
var searchKeyed = false;

function setSearchKeyed(mode) {

	searchKeyed = mode;

}

function checkForm() {

	if ($('#search').val()=='') return false;

	if (searchKeyed) $('#theForm').attr('action','../search/search.php');

	return true;

}

function doSearch() {

	if ($('#search').val()=='') return false;

	$('body').append('<form id="tempform"></form>');
    $('#tempform').
        attr('action','../search/search.php').attr('method','post').
        append('<input type="hidden" name="extended" value="1">').
        append('<input type="hidden" name="modules" value="*">').
        append('<input type="hidden" name="freeModules" value="*">').
        append('<input type="hidden" name="search" value="'+$('#search').val()+'">');
	$('#tempform').submit();
}

function onSearchBoxSelect(txt) {

	if (!searchBoxSelected) {

		$('#search').val(txt ? txt : '');
		$('#search').removeClass().addClass('search-input');
		searchBoxSelected = true;

	}

}

var requestVars = Array();

function addRequestVar(key,val) {

	requestVars[requestVars.length] = [key,val];

}

function doLanguageChange(languageId) {

	if (languageId) {

		addFormVal('languageId',languageId);

	} else {

		addFormVal('languageId',$('#languageSelect').val());
	}

	for(var i=0;i<requestVars.length;i++) {

		addFormVal(requestVars[i][0],requestVars[i][1]);

	}

	goForm();

}

function goIntLink(controller,url,params) {

	//alert(controller+'::'+url+'::'+params); return;

	if (params) {

		for(i in params) {

			var x = params[i].split(':');

			if (x[0]=='url')
				url = x[1];
			else
				addFormVal(x[0],x[1]);

		}

	}

	goForm('../'+controller+'/'+url);

}

var allHidden = true;

function showAllToggle() {

		$('#showAllToggle').removeClass('invisible').addClass('visible');
//		<span id="showAllToggleShow" class="visible">{t}show all{/t}</span>
	//	<span id="showAllToggleHide" class="invisible">{t}hide all{/t}</span>
}

function toggleAllHidden() {

	if (allHidden) {
		$('div[id^=hidden-]').removeClass('invisible').addClass('visible');
		$('div[id^=switch-]').removeClass('showHidden').addClass('hideHidden');
		allHidden = false;
	} else {
		$('div[id^=hidden-]').removeClass('visible').addClass('invisible');
		$('div[id^=switch-]').removeClass('hideHidden').addClass('showHidden');
		allHidden = true;
	}

}

function toggleHidden(id) {

	if ($('#hidden-'+id).attr('visible')=='1') {
		$('#hidden-'+id).removeClass('visible').addClass('invisible');
		$('#switch-'+id).removeClass('hideHidden').addClass('showHidden');
		$('#hidden-'+id).attr('visible','0');
	} else {
		$('#hidden-'+id).removeClass('invisible').addClass('visible');
		$('#switch-'+id).removeClass('showHidden').addClass('hideHidden');
		$('#hidden-'+id).attr('visible','1');
	}

}

function chkPIDInLink(h,p) {

	/*
		h: url to check
		p: paramter & value ("p=123") to check for
	*/

	if (h && h.indexOf('javascript:')==-1 && h.indexOf('mailto:')==-1) {

		var url = $.url(h); // pass in a URI as a string and parse that

		/*
			source - the whole url being parsed
			protocol - eg. http, https, file, etc
			host - eg. www.mydomain.com, localhost etc
			port - eg. 80
			relative - the relative path to the file including the querystring (eg. /folder/dir/index.html?item=value)
			path - the path to the file (eg. /folder/dir/index.html)
			directory - the directory part of the path (eg. /folder/dir/)
			file - the basename of the file eg. index.html
			query - the entire querystring if it exists, eg. item=value&item2=value2
			fragment (also available as anchor) - the entire string after the # symbol

			bug! none of these retain the first '..' in a href '../glossary/term.php?id=105238'
			hence the juggling with fragments below

		*/

		if (url.attr('fragment').length!==0) {

			h = h.replace('#'+url.attr('fragment'),'');

		}

		if (url.attr('query').length==0) {
			h = h +'?'+p;
		} else
		if (url.attr('query').indexOf(p)==-1) {
			h = h +'&'+p;
		}

		h = h + (url.attr('fragment').length!==0 ? '#'+url.attr('fragment') : '');

		return h;

	}

}

function chkPIDInLinks(pid,par) {

	var p = par+'='+pid;

	$('a').each(function() {
		// Assume new window links are not related to Linnaeus; don not append epi
		if ($(this).attr('target') !== '_blank') {

			$(this).attr('href', chkPIDInLink($(this).attr('href'), p));

		}
	});

}

var searchResultIndexActive=null;

function showSearchIndex() {

	$.ajax({
		url : '../search/ajax_interface.php',
		type: "POST",
		data : ({
			'action' : 'get_search_result_index' ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			if (searchResultIndexActive)
				allLookupSetSelectedId(searchResultIndexActive);
			allLookupNavigateOverrideDialogTitle(_('Search results'));
			allLookupShowDialog(data);
			if (searchResultIndexActive)
				allLookupScrollToSelectedId();
		}
	});

}

function hint(caller,txt) {

	if (!$('#hint-box')) return;

	$('#hint-box').toggle();

	if (caller) {
		var pos = $(caller).offset();
		$('#hint-box').offset({left:pos.left,top:pos.top+25});
		// top+15 is a neat vertical outline, but we use 25 so the cursor doesn't cover the first few letters
	}
	if (txt) $('#hint-box').html(txt);

	$('#hint-box').bind('click', function() {
		hint();
	});
}

function hintHide()
{
	$('#hint-box').toggle(false);
}

function setSessionVar(variable,value) {

	$.ajax({
		url : '../utilities/ajax_interface.php',
		type: "POST",
		data : ({
			'action' : 'set_session' ,
			'var' : variable,
			'val' : value,
			'time' : allGetTimestamp()
		})
	});

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

function getSessionVar(variable,callback) {

	$.ajax({
		url : '../utilities/ajax_interface.php',
		type: "POST",
		data : ({
			'action' : 'get_session' ,
			'var' : variable,
			'time' : allGetTimestamp()
		}),
		success : function (data)
		{
			if (callback) callback(data);
		}
	});

}

$(function() {
    $('.decode').each( function(count,enc) { //foreach encoded DOM element
        encodedData = jQuery(enc).html(); //grab encoded text
        decodedData = encodedData.replace(/[a-zA-Z]/g, function(char){ //foreach character
            return String.fromCharCode( //decode string
                /**
                 * char is equal/less than 'Z' (i.e. a  capital letter), then compare upper case Z unicode
                 * else compare lower case Z unicode.
                 *
                 * if 'Z' unicode greater/equal than current char (i.e. char is 'Z','z' or a symbol) then
                 * return it, else transpose unicode by 26 to return decoded letter
                 *
                 * can't remember where I found this, and yes it makes my head hurt a little!
                 */
                (char<="Z"?90:122)>=(char=char.charCodeAt(0)+13)?char:char -26
            );
        });
        $(enc).html(decodedData); // replace text
    });
});