var MAX_DUMP_DEPTH = 10;

function dumpObj(obj, name, indent, depth) {

	  if (depth > MAX_DUMP_DEPTH) {

			 return indent + name + ": <Maximum Depth Reached>\n";

	  }

	  if (typeof obj == "object") {

			 var child = null;

			 var output = indent + name + "\n";

			 indent += "\t";

			 for (var item in obj)

			 {

				   try {

						  child = obj[item];

				   } catch (e) {

						  child = "<Unable to Evaluate>";

				   }

				   if (typeof child == "object") {

						  output += dumpObj(child, item, indent, depth + 1);

				   } else {

						  output += indent + item + ": " + child + "\n";

				   }

			 }

			 return output;

	  } else {

			 return obj;

	  }

}

function taxonContentOpenLiteratureLink(id) {
	alert('to be implemented');
}

function taxonContentOpenMediaLink(id) {
	alert('to be implemented');
}

function taxonContentOpenGlossaryLink(id) {
	alert('to be implemented');
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

function isArray(obj) {
   if (obj.constructor.toString().indexOf("Array") == -1)
      return false;
   else
      return true;
}

function showDialog(content,title) {

	$.modaldialog.prompt(content, {
		title : title ? title : _('Enter value'),
		width: 350
	});

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

	addFormVal('id',id);
	addFormVal('cat',cat ? cat : null);
	goForm('../species/taxon.php');

}

function goHigherTaxon(id) {

	addFormVal('id',id);
	goForm('../highertaxa/taxon.php');

}

function goMenuModule(id) {

	addFormVal('modId',id);
	goForm('../module/');

}

function goAlpha(letter,url) {

	addFormVal('letter',letter);
	goForm(url ? url : null);

}

function goLiterature(id) {

	addFormVal('id',id);
	goForm('../literature/reference.php');

}

function goGlossaryTerm(id) {

	addFormVal('id',id);
	goForm('../glossary/term.php');

}

function goModuleTopic(id,modId) {

	if (modId) addFormVal('modId',modId);
	addFormVal('id',id);
	goForm('../module/topic.php');

}

function goContent(id) {

	addFormVal('id',id);
	goForm('../linnaeus/');

}

function goMatrix(id) {

	addFormVal('id',id);
	goForm('../matrixkey/use_matrix.php');

}

function goNavigate(id,field,url) {

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

	if (searchKeyed) $('#theForm').attr('action','../linnaeus/search.php');

	return true;

}

function doSearch() {

	if (!searchBoxSelected) return false;
	if ($('#search').val()=='') return false;

	$('#theForm').attr('action','../linnaeus/search.php');
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
















