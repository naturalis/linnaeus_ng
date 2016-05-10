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

	//console.dir( data );

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : data,
		success : function (data)
		{
			//console.log(data);
			if (data) lit2BuildList(action,$.parseJSON(data));
		}
	});

}

function lit2BuildList(action,data)
{
	var buffer=Array();
	
	//$('#result-count').html('Found '+data.results.length);

	for(var i in data.results)
	{
		var t=data.results[i];
		if (!t.id)
			continue;
			
		if (t.authors)
		{
			var author='';
			for (var k=0;k<t.authors.length;k++)
			{
				author+=(k>0?', ':'')+t.authors[k].name;
			}
		}
		else
		{
			var author=t.author;
		}
			
		if (action.indexOf('title')!=-1)
		{
			buffer.push(
				'<li class="lit-index-item"><a href="reference.php?id='+t.id+'">'+
				t.label+
				( author ? ', '+author : '' ) +
				'</a></li>'
				);
		}
		else
		{
			buffer.push(
				'<li class="lit-index-item"><a href="reference.php?id='+t.id+'">'+author+': '+t.label+'</a></li>');
		}
	}

	$('#lit2-result-list').html('<ul>'+buffer.join('')+'</ul>');
	
}
