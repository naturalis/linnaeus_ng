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

	

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : data,
		success : function (data)
		{
			console.log(data);
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
			var author=t.authors;
		}
		else
		{
			var author=t.author;
		}
			
		if (action.indexOf('title')!=-1)
			buffer.push('<li class="lit-index-item"><a href="edit.php?id='+t.id+'">'+t.label+', '+author+'</a></li>');
		else
			buffer.push('<li class="lit-index-item"><a href="edit.php?id='+t.id+'">'+author+', '+t.label+'</a></li>');
	}

	$('#lit2-result-list').html('<ul>'+buffer.join('')+'</ul>');
	
}

var authors=Array();

function storeAuthor(p)
{
	authors.push(p);
}

function addAuthorField()
{
	for(var k=0;k<99;k++)
	{
		if ($("#actor_id-"+k).length==0)
			break;
	}
	
	var buffer=Array()
	buffer.push('<option value="">-</option>');
	for(var i in authors)
	{
		buffer.push('<option value="'+authors[i].id+'">'+authors[i].name+'</option>');
	}

	$('#authors').html(
		$('#authors').html()+
		'<span id="actor_id-'+k+'"><select name="actor_id[]">'+buffer.join('')+'</select>'+
		'<a class="edit" href="#" onclick="removeAuthorField('+k+');return false;">verwijderen</a><br /></span>')
}

function removeAuthorField(k)
{
	$('#actor_id-'+k).remove();
	$('#actor_remove-'+k).remove();
}