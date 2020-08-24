var itemurl='edit.php';

function setItemUrl(url)
{
	itemurl=url;
}

function getItemUrl()
{
	return itemurl;
}

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

		var author="";
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

		if (action.indexOf('title')!=-1)
		{
			buffer.push(
				'<li class="lit-index-item"><a href="'+getItemUrl()+'?id='+t.id+'">'+
				t.label+
				( t.label && author ? ', ' : '' ) +
				author +
				'</a></li>'
				);
		}
		else
		{
			buffer.push(
				'<li class="lit-index-item"><a href="'+getItemUrl()+'?id='+t.id+'">'+author+': '+t.label+'</a></li>');
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
		'<a class="edit" href="#" onclick="$(\'#'+p.id_root+'-'+k+'\').remove();return false;">delete</a><br /></span>');

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


var elements=[];

function storeElement(id)
{
	elements.push( { id:id,seen:false } );
}

function markElementAsSeen(id)
{
	for(var i=0;i<elements.length;i++)
	{
		if (elements[i].id==id)
		{
			elements[i].seen=true;
		}
	}
}


function processMatches()
{
	var j=0;
	for(var i=0;i<elements.length;i++)
	{
		if (!elements[i].seen) j++
	}

	if (j!=0 && !confirm("You have not reviewed "+j+" matches.\nAre you sure you want to proceed?"))
	{
		return;
	}
	else
	{
		var target='bulk_process.php';
		if (document.URL.indexOf('bulk_process.php')!=-1) target='bulk_save.php';
		$('#theForm').attr('action',target).submit();
	}
}
