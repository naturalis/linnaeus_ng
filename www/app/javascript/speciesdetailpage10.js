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

	ajaxGetData(url);

}

function doDataCategory(obj) {

	var s = '';

	for (var i=0;i<obj.results.length;i++) {

		var d = obj.results[i];

		if (d && d.numOfResults> 0) {

			s = s + '<span class="">'+d.label+'</span><br />\n<hr class="">\n';

			for (var j=0;j<d.data.length;j++) {
				
				var t = d.data[j];
				
				switch(d.label) {
					
					case 'Species descriptions':
						s = s +
							'<span class="">'+
							'<a href="'+lngBaseURL+'species/taxon.php?id='+(t.taxon_id)+'&cat='+(t.cat)+'">'+
							t.taxon+
							'</a></span><br />\n';
						break;

					case 'Species media':
						s = s + '<span class="">'+
						'<a href="'+lngBaseURL+'species/taxon.php?id='+(t.taxon_id)+'&cat=media">'+
						(t.label ? t.label : t.content)+
						'</a></span><br />\n';
						break;

					case 'Species names':
					case 'Species synonyms':
					case 'Species common names':
						s = s + '<span class="">'+
						'<a href="'+lngBaseURL+'species/taxon.php?id='+(t.taxon_id)+'&cat=names">'+
						t.label+': '+t.taxon_id+
						'</a></span><br />\n';
						break;

					case 'Dichotomous key choices':
					case 'Dichotomous key steps':
						s = s +
							'<span class="">'+
							'<a href="'+lngBaseURL+'key/">'+
							(t.label ? t.label : t.content)+
							'</a></span><br />\n';
						break;

					case 'Literary references':
						s = s +
							'<span class="">'+
							'<a href="'+lngBaseURL+'literature/reference.php?id='+t.id+'">'+
							(t.label ? t.label : t.content)+
							'</a></span><br />\n';
						break;

					case 'Glossary terms':
					case 'Glossary synonyms':
					case 'Glossary media':
						s = s +
							'<span class="">'+
							'<a href="'+lngBaseURL+'glossary/term.php?id='+t.id+'">'+
							(t.term ? t.term : (t.label ? t.label : t.content))+
							'</a></span><br />\n';
						break;

					case 'Matrix key matrices':
					case 'Matrix key characteristics':
					case 'Matrix key states':
						s = s +
							'<span class="">'+
							'<a href="'+lngBaseURL+'matrixkey/identify.php">'+
							(t.name ? t.name : (t.label ? t.label : t.content))+
							'</a></span><br />\n';
						break;

					case 'geographical data':
						s = s +
							'<span class="">'+
							'<a href="'+lngBaseURL+'mapkey/">'+
							(t.label ? t.label : t.content)+
							'</a></span><br />\n';
						break;
				}
				
			}

			s = s + '<span class="">&nbsp;</span>\n';

		}

	}

	return s;






	for (var i=0;i<obj.length;i++) {

		var d = obj[i];

		if (d.data && d.data.length>0) {
			s = s + '<p>\n<b>'+d.label+'</b>\n';
			for(var j=0;j<d.data.length;j++) {

				if (d.label=='Species descriptions') {
					s = s + 'In the <a href="'+lngBaseURL+'species/taxon.php?p='+pId+'&id='+d.data[j].taxon_id+'">description</a> of "'+d.data[j].taxon+'".<br/>\n';

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

	s = s + doDataCategory(data.species);
	s = s + doDataCategory(data.glossary);
	s = s + doDataCategory(data.literature);
	s = s + doDataCategory(data.dichkey);
	s = s + doDataCategory(data.matrixkey);
	s = s + doDataCategory(data.content);
	s = s + doDataCategory(data.map);
	
	$('#general-content').html(s);

}


getLinnaeusData(testUrl);
