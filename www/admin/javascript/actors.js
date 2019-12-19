function actorsLookup(caller,action,letter) 
{
	if (!letter)
	{
		var text=$(caller).val();
	}
	else 
	{
		var text=letter;
	}
	
	$('#actor-result-list').html('');
	$('#result-count').html('');
	
	data={
		'action': 'get_actor',
		'time' : allGetTimestamp()
	};

	if (action=='lookup_company_letter' || action=='lookup_individual_letter')
	{
		data.match_start='1';
	}
	if (action=='lookup_company' || action=='lookup_company_letter')
	{
		data.is_company='1';
	}
	else
	{
		data.is_company='0';
	}

	data.search=text;

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : data,
		success : function (data)
		{
			//console.log(data);
			if (data) actorsBuildList(action,$.parseJSON(data));
		}
	});

}

function actorsBuildList(action,data)
{
	var buffer=Array();
	
	//$('#result-count').html('Found '+data.results.length);
	for(var i in data.results)
	{
		var t=data.results[i];
		if (!t.id)
			continue;
//		buffer.push('<li class="lit-index-item"><a href="edit.php?id='+t.id+'">'+t.name+'</a>'+(t.employer_name ? '('+t.employer_name+')' : '' )+'</li>');
		buffer.push('<li class="lit-index-item"><a href="edit.php?id='+t.id+'">'+t.name+(t.name_alt ? ' ('+t.name_alt+')' : '' )+'</a></li>');
	}

	$('#actor-result-list').html('<ul>'+buffer.join('')+'</ul>');
	
}