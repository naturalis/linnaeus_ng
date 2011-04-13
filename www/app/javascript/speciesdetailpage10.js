// THIS SHOULD GO!
function ajaxGetData(url,id) {

	var xmlHttp;
	var returnValue = '';
	try  {
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}
	catch (e) {
		// Internet Explorer
		try {
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			try {
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) {
				alert("Your browser does not support AJAX.");
				return false;
			}
		}
	}
	
	xmlHttp.onreadystatechange=function() {
		if(xmlHttp.readyState==4) {
			doLinnaeusData(xmlHttp.responseText);
		}
	}

	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);

}

var testUrl = 'http://linnaeus/app/views/utilities/res.linnaeusng.php?s=Giraffa+camelopardalis';
var lngBaseURL = '/app/views/';
var pId = 64;

function getLinnaeusData(url) {
return;
	ajaxGetData(url);

}

function doDataCategory(obj) {

	var s = '';

	for (var i=0;i<obj.length;i++) {

		var d = obj[i];

		if (d.data && d.data.length>0) {
			s = s + '<p>\n<b>'+d.label+'</b>\n';
			for(var j=0;j<d.data.length;j++) {

				if (d.label=='Species descriptions') {
//					alert(dumpObj(d.data[j]));
					s = s + 'In the <a href="'+lngBaseURL+'species/taxon.php?p='+pId+'&id='+d.data[j].taxon_id+'">description</a> of "'+d.data[j].taxon+'".<br/>\n';
/*
NaN
undefined	id: 3792
undefined	taxon_id: 20809
undefined	content: <i>Giraffa camelopardalis</i> (Linnaeus, 1758)<br /><br />Giraffe<br /><br /><b>Description</b><br />Tallest land mammal, up to 6 meters. Has blotched brown patterning on the body. Wt 500-800 kg.<br /><br /><b>Similar species</b><br />None.<br /><br /><b>Habitat and social behaviour</b><br />Open savanna and bushland. Found in dispersed groups of 2 - 20 animals.<br /><br /><b>Distribution</b><br />http://www.tanzaniamammals.org/uploads/maps/l079_giraffe.jpg<br /><br /><b>IUCN Red List Category:</b> Lower Risk, conservation dependent.
undefined	cat: 234
*/




				}
			}
			s = s + '</p>\n\n';
		}

	}
	
	return s;
	
}

function doLinnaeusData(content) {

	data = $.parseJSON(content);

	var s = '';

	s = s + doDataCategory(data.species.results);
	s = s + doDataCategory(data.glossary.results);
	s = s + doDataCategory(data.literature.results);
	s = s + doDataCategory(data.dichkey.results);
	s = s + doDataCategory(data.matrixkey.results);
	s = s + doDataCategory(data.map.results);
	
	alert(s);

}


getLinnaeusData(testUrl);



/*
// print functions per section
sectionFunctions = new Array();
	sectionFunctions[0] = 'printLinks(data,jSONsourceName)';

var maxLinkDisplayLength = 75;
var first = true;

var htmlLinkData =
	'<tr><td><div style="margin-top: 0px; margin-bottom: 20px;"><p class="stronglink" style="margin-top: 3px; margin-bottom: 3px;"><span class="nounderline">'+
	'<a itemtitle="%label%" href="%link%" target="_blank"><img src="images/link.gif" alt="'+texts[10]+'" width="15" border="0" height="9"></a></span>'+
	'<a itemtitle="%label%" href="%link%" target="_blank">%label%</a></p><p class="smallgray" style="margin-top: 0px; margin-bottom: 2px;">%linkDisplay%</p>'+
	'<p style="margin-top: 0px; margin-bottom: 0px;">%text%</p></div></td></tr>';

function makeHtmlLinkData(title,link,text) {
	var linkDisplay = (link.length > maxLinkDisplayLength ? link.substring(0,maxLinkDisplayLength)+'...' : link);
	return htmlLinkData.replace(/%label%/g,title).replace(/%link%/g,link).replace(/%linkDisplay%/g,linkDisplay).replace(/%text%/g,text);
}

function printLinks(data,jSONsourceName) {
	if (data.links == undefined || data.links[0].list.length == 0) return;

	var str = '';
	for (var i=0;i<data.links[0].list.length;i++) {
		var dummy = data.links[0].list[i];
		str = str + makeHtmlLinkData(unescape(dummy.title),unescape(dummy.link),unescape(dummy.text));
	}

	data.link = data.links[0].link;
	var sourceData = checkSourceAndLink(data,jSONsourceName);

	document.getElementById('data0').innerHTML =
		document.getElementById('data0').innerHTML +
		makeTable(
			(first == false ? htmlHr : '' )+
			makeSourceHeader('sect0',0,sourceData[0],sourceData[1]) +
			str + '<tr><td>' + htmlHr + '</td></tr>'
		);
	first = false;
}

function cleanUp() {
	var sourceData = new Array();
	if (document.getElementById('data0').innerHTML=='') document.getElementById('data0').innerHTML = makeTable(texts[0]);
}
*/