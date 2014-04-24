{include file="../shared/admin-header.tpl"}
<script>

function literature2_lookup_list(caller,action,letter) 
{
	if (!letter)
	{
		var text=$(caller).val();
		if (text.length<2)
			return;
	}
	else 
	{
		var text=letter;
	}
	
	$('#result-list-screen').html('');
	$('#result-count').html('');

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : action ,
			'search' : text,
			'time' : allGetTimestamp()
		}),
		success : function (data)
		{
			if (data) build_list(caller,$.parseJSON(data));
		}
	});

}

function build_list(caller,data)
{
	var buffer=Array();
	
	$('#result-count').html('Found '+data.results.length);

	for(var i in data.results)
	{
		var t=data.results[i];
		if (!t.id)
			continue;
		buffer.push('<li><a href="edit.php?id='+t.id+'">'+t.label+'</a></li>');
	}

	$('#result-list-screen').html('<ul>'+buffer.join('')+'</ul>');
	
}

</script>
<div id="page-main">
	<table class="alphabet">
		<tr>
			<td>
				Find by title:
			</td>
			<td>
				{foreach from=$titleAlphabet item=v}
				<a href="#" onclick="literature2_lookup_list(this,'lookup_title_letter','{$v.letter}');">{$v.letter}</a>
				{/foreach}
			</td>
			<td>
				<input type="text" name="" id="lookup-input-title" onkeyup="literature2_lookup_list(this,'lookup_title');" />
				
			</td>
		</tr>
		<tr>
			<td>
				Find by author:
			</td>
			<td>
				{foreach from=$authorAlphabet item=v}
				<a href="#" onclick="literature2_lookup_list(this,'lookup_author_letter','{$v.letter}');">{$v.letter}</a>
				{/foreach}
			</td>
			<td>
				<input type="text" name="" id="lookup-input-author" onkeyup="literature2_lookup_list(this,'lookup_author');" />
			</td>
		</tr>
	</table>
	<span id="result-count"></span>
	<div id="result-list-screen">
	</div>

</div>

{include file="../shared/admin-footer.tpl"}
