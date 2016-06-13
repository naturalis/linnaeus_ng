var currentlookupstring='';

function lit2Lookup(caller,action,letter) 
{
	if (!letter)
	{
		var text=$(caller).val();
		if (text.length<1)
			return;
	}
	else 
	{
		var text=letter;
	}
	
	$('#lit2-result-list').html('');
	$('#result-count').html('');
	
	data={
		'action' : 'reference_lookup' ,
		'time' : allGetTimestamp()
	};

	if (action=='lookup_title_letter' || action=='lookup_author_letter')
	{
		data.match_start='1';
	}

	if (action=='lookup_title' || action=='lookup_title_letter')
	{
		data.search_title=text;
	}

	if (action=='lookup_author' || action=='lookup_author_letter')
	{
		data.search_author=text;
	}

	currentlookupstring=text;

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : data,
		success : function (data)
		{
			if (data) lit2BuildList(action,$.parseJSON(data));
		}
	});

}

function lit2BuildList(action,data)
{
	var buffer=Array();
	
	//$('#result-count').html('Found '+data.results.length);
	
	var rowtpl=fetchTemplate('reference-table-row');
	var highlighttpl=fetchTemplate('string-highlight').trim();
	var regexp=new RegExp(currentlookupstring,"gi");

	for(var i in data.results)
	{
		var t=data.results[i];

		if (!t.id)
			continue;

		if (t.unparsed==1)
			continue;
			
		var author='';
		if (t.authors)
		{
			for (var k=0;k<t.authors.length;k++)
			{
				author+=(k>0?', ':'')+t.authors[k].name;
			}
		}
		
		if (author.trim.length==0 && t.author)
		{
			author=t.author;
		}
			
		buffer.push(
			rowtpl
				.replace('%ID%',t.id)
				.replace('%AUTHOR%',( author ? author : '-' ))
				.replace('%YEAR%',t.date)
				.replace('%REFERENCE%',t.label.replace(regexp, highlighttpl.replace('%STRING%',currentlookupstring)))
		);
	
	}
	
	$('#lit2-result-list').html( fetchTemplate('reference-table').replace('%TBODY%',buffer.join("\n")));
	
}
