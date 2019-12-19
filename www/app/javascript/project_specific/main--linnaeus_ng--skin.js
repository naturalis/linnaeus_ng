var projectId=null;

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

function showVideo(url,name) {

	var content =
		'<video width="320" height="240" controls="controls">'+
			'<source src="'+url+'" type="mime_type" />'+
			'<embed src="'+url+'" width="320" height="240" />'+
		'</video> ';

	showDialog(name,content);

}

var allIsDialogOpen = false;

function showDialog(title,content,vars,resize) {

	if ($('#jDialog').length!=0) {

		$('#jDialog').html(null);

		/*		
		var buttons = {
			// callback functions must exist in the dialog's html (or elsewhere within scope)
			"ok": function() {jDialogOk();},
			"close": function() {jDialogCancel();}
		};
		
		if (!vars.showOk) 
			delete buttons.ok;
		*/

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
			width: "auto"
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

	if (!allIsDialogOpen) return;

	if ($('#jDialog').length!=0)
		$('#jDialog').dialog( "close" );
	else
		$('#dialog-close').click()

	allIsDialogOpen = false;


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
	if (!languageId)
	{
		languageId=$('#languageSelect').val();
	}
	var f=$('<form action="" method="post"></form>');
	f.append('<input type="hidden" name="languageId" value="'+languageId+'" />');
	f.appendTo('body').submit();
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

function doBackForm(url,data) {
	//?
	data = unescape(data);
	obj = $.parseJSON(data);

	for (var i=0;i<obj.length;i++) {

		addFormVal(obj[i].vari,obj[i].val);

	}

	addFormVal('backstep','1');
	goForm(url);

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
	
	if (h && h.indexOf('javascript:')==-1) {

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

		$(this).attr('href',chkPIDInLink($(this).attr('href'),p));
		
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
		$('#hint-box').offset({left:pos.left,top:pos.top+15});
	}
	if (txt) $('#hint-box').html(txt);

	$('#hint-box').bind('click', function() {
		hint();
	});
}
