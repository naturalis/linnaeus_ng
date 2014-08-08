{include file="../shared/admin-header.tpl"}
<script>

function literature2LookupList(caller,action,letter) 
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
	
	data={
		'action' : 'reference_lookup' ,
		'time' : allGetTimestamp()
	};

	if (action=='lookup_title' || action=='lookup_title_letter')
		data.search_title=text;

	if (action=='lookup_author' || action=='lookup_author_letter')
		data.search_author=text;

	if (action=='lookup_title_letter' || action=='lookup_author_letter')
		data.match_start='1';


	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : data,
		success : function (data)
		{
			//console.log(data);
			if (data) build_list(caller,$.parseJSON(data));
		}
	});

}

function build_list(caller,data)
{
	var buffer=Array();
	
	//$('#result-count').html('Found '+data.results.length);

	for(var i in data.results)
	{
		var t=data.results[i];
		if (!t.id)
			continue;
		buffer.push('<li><a href="edit.php?id='+t.id+'">'+t.author+' - '+t.label+'</a></li>');
	}

	$('#result-list-screen').html('<ul>'+buffer.join('')+'</ul>');
	
}

</script>
<style>

li {
	white-space:nowrap;
	overflow:hidden;
	list-style:none;
	margin-left:-36px;
	list-style-position:outside;
}

.click-letter {
	padding:0 2px 0 2px;
}

</style>
<div id="page-main">
	<table class="alphabet">
		<tr>
			<td>
				Zoek op titel:
			</td>
			<td>
				<input type="text" name="" id="lookup-input-title" onkeyup="literature2LookupList(this,'lookup_title');" />
				
			</td>
			<td>
				{foreach from=$titleAlphabet item=v}
				<a href="#" class="click-letter" onclick="literature2LookupList(this,'lookup_title_letter','{$v.letter}');">{$v.letter|@strtoupper}</a>
				{/foreach}
			</td>
		</tr>
		<tr>
			<td>
				Zoek op auteur:
			</td>
			<td>
				<input type="text" name="" id="lookup-input-author" onkeyup="literature2LookupList(this,'lookup_author');" />
			</td>
			<td>
				{foreach from=$authorAlphabet item=v}
				{if $v.letter}
				<a href="#" class="click-letter" onclick="literature2LookupList(this,'lookup_author_letter','{$v.letter}');">{$v.letter|@strtoupper}</a>
				{/if}
				{/foreach}
			</td>
		</tr>
	</table>
	<span id="result-count"></span>
	<div id="result-list-screen">
	</div>

</div>

{include file="../shared/admin-footer.tpl"}
