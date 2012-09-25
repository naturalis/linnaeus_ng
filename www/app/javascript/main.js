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
	return str.replace(/(<([^>]+)>)/ig,"");
}

function allGetTimestamp() {

	var tsTimeStamp= new Date().getTime();

	return tsTimeStamp;

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

var allTranslations = Array();

function _(text) {
	
	return text;

	/* TO BE DONE */
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
/*
function showMedia(url,name) {

	$.colorbox({
		href:url,
		title:name,
		transition:"elastic", 
		maxWidth:800,
		width:"100%",
		opacity:0
	});

}
*/
function showMedia(url,name) {
	$.prettyPhoto.open(url,'',name);
}

function showVideo(url,name) {

	var content =
		'<video width="320" height="240" controls="controls">'+
			'<source src="'+url+'" type="mime_type" />'+
			'<embed src="'+url+'" width="320" height="240" />'+
		'</video> ';

	showDialog(name,content);

}



function isArray(obj) {
   if (obj.constructor.toString().indexOf("Array") == -1)
      return false;
   else
      return true;
}

function showDialog(title,content,vars,resize) {

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

function closeDialog() {

	$('#dialog-close').click()

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

function goTaxon(id,cat) {
	//!
	addFormVal('id',id);
	addFormVal('cat',cat ? cat : null);
	goForm('../species/taxon.php');

}

function goHigherTaxon(id) {
	//!
	addFormVal('id',id);
	goForm('../highertaxa/taxon.php');

}

function goMenuModule(id) {
	//!
	addFormVal('modId',id);
	goForm('../module/');

}

function goAlpha(letter,url) {
	//!
	addFormVal('letter',letter);
	goForm(url ? url : null);

}

function goLiterature(id) {
	//!
	addFormVal('id',id);
	goForm('../literature/reference.php');

}

function goGlossaryTerm(id) {
	//!
	addFormVal('id',id);
	goForm('../glossary/term.php');

}

function goModuleTopic(id,modId) {
	//!
	if (modId) addFormVal('modId',modId);
	addFormVal('id',id);
	goForm('../module/topic.php');

}

function goContent(id) {
	//!
	addFormVal('id',id);
	goForm('../linnaeus/');

}

function goContentPage(subject) {
	//!
	addFormVal('sub',subject);
	goForm('../linnaeus/content.php');

}

function goMatrix(id) {
	//!
	addFormVal('id',id);
	goForm('../matrixkey/use_matrix.php');

}

function goMap(id,url) {
	//!
	if (id) addFormVal('id',id);
	goForm(url ? url : '../mapkey/examine_species.php');

}

function goIntroductionTopic(id) {
	//!
	addFormVal('id',id);
	goForm('../introduction/topic.php');

}

function goNavigator() {
	//!
	goForm('../linnaeus/index.php');

}

function goIconGrid() {
	//!
	addFormVal('show','icongrid');
	goForm('../linnaeus/index.php');

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

	if (!searchBoxSelected) return false;
	if ($('#search').val()=='') return false;

	$('#theForm').attr('action','../search/search.php');
	$('#theForm').submit();

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

function doLanguageChange() {

	addFormVal('languageId',$('#languageSelect').val());

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

function chkPIDInLinks(pid,par) {

	var p = par+'='+pid;

	$('a').each(function() {
		var h = $(this).attr('href');
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

		var d = String;

		if (url.attr('query').length==0) {
			d = h+'?'+p;
		} else
		if (url.attr('query').indexOf(p)==-1) {
			d = h+'&'+p;
		}
		
		d = d + (url.attr('fragment').length!==0 ? '#'+url.attr('fragment') : '');
		
		$(this).attr('href',d);
		
	});

}



































