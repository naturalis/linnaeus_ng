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
			buffer.push('<li class="lit-index-item"><a href="edit.php?id='+t.id+'">'+t.label+', '+author+'</a></li>');
		}
		else
		{
			buffer.push('<li class="lit-index-item"><a href="edit.php?id='+t.id+'">'+author+': '+t.label+'</a></li>');
		}
	}

	$('#lit2-result-list').html('<ul>'+buffer.join('')+'</ul>');
	
}

var authors=Array();
var organisations=Array();

function storeAuthor(p)
{
	authors.push(p);
}

function storeOrganisation(p)
{
	organisations.push(p);
}

function addAnyField(p)
{
	// find the next available author field
	for(var k=0;k<99;k++)
	{
		if ($('#'+p.id_root+'-'+k).length==0)
			break;
	}
	
	var buffer=Array()
	buffer.push('<option value="">-</option>');
	for(var i in p.values)
	{
		buffer.push('<option value="'+p.values[i].id+'">'+p.values[i].name+'</option>');
	}


	var currVals=Array;
	$('select[name^='+p.id_root+']').each(function(i){
		currVals[i]=$(this).val();
	});

	$('#'+p.container).html(
		$('#'+p.container).html()+
		'<span id="'+p.id_root+'-'+k+'"><select name="'+p.id_root+'[]">'+buffer.join('')+'</select>'+
		'<a class="edit" href="#" onclick="$(\'#'+p.id_root+'-'+k+'\').remove();return false;">verwijderen</a><br /></span>');

	$('select[name^='+p.id_root+']').each(function(i){
		$(this).val(currVals[i]);
	});

}

function addAuthorField()
{
	addAnyField({id_root:"actor_id",values:authors,container:"authors"});
}

function addOrganisationField()
{
	addAnyField({id_root:"organisation_id",values:organisations,container:"organisations"});
}

function removeAuthorField(k)
{
	$('#actor_id-'+k).remove();
	$('#actor_remove-'+k).remove();
}