{include file="../shared/admin-header.tpl"}
<style>
.taxonomy input[name*=valid_name]{
	width:140px;
	margin-right:5px;
}
.taxonomy input[type=text].lighter{
	color:#999;
}
.taxonomy input[type=text].year{
	width: 40px;
}
.taxonomy tr.info-row td{
	color:#999;
	font-size:0.9em;
	font-style:italic;
}
ul.names-list {
	list-style-type: none;
	padding: 0px;
	margin: 0px;
}
.taxonomy a.inline-href {
	display:inline;
	margin-left:5px;
	color:#999;
	font-size:0.8em;
}

#parent-list {
	background-color:#fff;
	border:1px solid #666;
	width:350px;
	height:400px;
	position:absolute;
	display:none;
	overflow-y:scroll;
	overflow-x:hidden;
}

#parent-list ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}

#parent-list li {
    font-size: 0.9em;
    overflow-x: hidden;
    white-space: nowrap;
}

#parent-list li:hover {
	background-color:#eee;
}

</style>
<script>

	var parent={};
	var prevnew={};
	var currentrank=null;
	var ranks=Array();

	function taxonomyCollectRanks()
	{
		$("#rank > option").each(function() {
			ranks.push( { label:this.text, id:this.value } );
		});
	}

	function taxonomyAuthorshipSplit()
	{
		var str=$( "#authorship" ).val();
		$( "#name_author" ).val( str.substring(0,str.lastIndexOf(',')).replace(/^\(/,'') );
		$( "#authorship_year" ).val( str.substring(str.lastIndexOf(',')+1).replace(/\)$/,'') );
	}
	
	function taxonomyEditParent()
	{
		$( '#parent' ).html('<input type="text" id="parent-list-input">');
		$( '#parent-list-input' ).bind('keyup', function(e) { species_lookup_list(e); } );
		$( '#parent-list-input' ).focus();
	}
	
	function taxonomyCloseList()
	{
		$( '#parent-list' ).html('');
		$( '#parent-list' ).toggle(false);
	}

	function taxonomySetParent(ele)
	{
		$( '#parent' ).html($(ele).text());
		$( '#new_parent_id' ).val($(ele).attr('taxon-id'));
		$( '#revert-link' ).toggle(true);
		prevnew = { id: $(ele).attr('taxon-id'), taxon: $(ele).text() };
		taxonomyCloseList();
	}

	function taxonomyRevertParent()
	{
		$( '#parent' ).html(parent.taxon);
		$( '#new_parent_id' ).val(null);
		taxonomyCloseList();
	}

	function taxonomySetPreviousChoice()
	{
		$( '#parent' ).html(prevnew.taxon);
		$( '#new_parent_id' ).val(prevnew.id);
	}

	function species_lookup_list(e) 
	{
		if (e.keyCode == 27)
		{ 
			taxonomySetPreviousChoice();
			taxonomyCloseList();
			return;
		}
		
		var text=$('#parent-list-input').val();

		$.ajax({
			url : "../species/ajax_interface.php",
			type: "POST",
			data : ({
				'action' : 'get_lookup_list' ,
				'search' : text,
				'get_all' : 0,
				'match_start' : 0,
				'taxa_only': 1,
				'max_results': 25,
				'formatted': 0,
				'rank_above': currentrank,
				'time' : allGetTimestamp()
			}),
			success : function (data)
			{	
				build_list($.parseJSON(data));
			}
		});

	}
	
	function build_list(data)
	{
		var buffer=Array();

		buffer.push('<li><a href="#" onclick="taxonomyCloseList();taxonomySetPreviousChoice();" style="margin-bottom:10px">close</a></li>');

		for(var i in data.results)
		{
			var t=data.results[i];
			
			if (t.label)
			{
				for(var r in ranks)
				{
					if (ranks[r].id==t.rank_id)
						var rank=ranks[r].label;
				}
				buffer.push('<li><a href="#" onclick="taxonomySetParent(this)" taxon-id="'+t.id+'">'+t.label+'</a> ('+rank+')</li>');
			}
		}

		$('#parent-list').html('<ul>'+buffer.join('')+'</ul>');

		allStickElementUnderElement('parent','parent-list');
  
		$('#parent-list').toggle(true);
		
	}
	

</script>


<div id="parent-list"></div>

<div id="page-main" class="taxonomy">

<h2>{$concept.taxon}</h2>

<form method=post>
<input type="hidden" name="id" value="{$concept.id}">
<input type="hidden" name="new_parent_id" id="new_parent_id" value="">
<input type="hidden" name="action" value="save">
<input type="hidden" name="rnd" value="{$rnd}">

<table>
	<tr>
		<td>Parent:</td>
		<td id="parent">{$parent.taxon}</td>
		<td>
			<a class="inline-href" href="#" onclick="taxonomyEditParent()">edit</a>
			<a class="inline-href" href="#" onclick="taxonomyRevertParent()">revert</a>
		</td>
	</tr>
	<tr>
		<td>Rank:</td><td>
		<select name="rank" id="rank">
		{foreach from=$ranks item=v}
		<option value="{$v.id}" {if $v.id==$concept.rank_id} selected="selected"{/if}>{$v.rank}</option>
		{/foreach}
		</select>
		</td>
	</tr>
	<tr>
		<td>Valid name:</td><td></td>
	</tr>
</table>
<table>
	<tr>
		<td><input type="text" name="valid_name[uninomial]" value="{$names.valid_name.uninomial}" /></td>
		<td><input type="text" name="valid_name[specific_epithet]" value="{$names.valid_name.specific_epithet}" /></td>
		<td><input type="text" name="valid_name[infra_specific_epithet]" value="{$names.valid_name.infra_specific_epithet}" /></td>
		<td><input type="text" name="valid_name[authorship]" id="authorship" value="{$names.valid_name.authorship}" /></td>

		<td><input type="text" name="valid_name[name_author]" id="name_author" class="lighter" value="{$names.valid_name.name_author}" /></td>
		<td><input type="text" name="valid_name[authorship_year]" id="authorship_year" class="lighter year" value="{$names.valid_name.authorship_year}" /></td>

	</tr>
	<tr class="info-row">
		<td>uninomial</td>
		<td>epithet</td>
		<td>infra specific epithet</td>
		<td>authorship</td>
		<td>name author</td>
		<td>year</td>
	</tr>
</table>
<input type="submit" value="save" />
</form>

<!--
Other names:
<ul class="names-list">
{foreach from=$names.list item=v}
<li>{$v.name}</li>
{/foreach}
</ul>
</div>
-->


<script type="text/JavaScript">
$(document).ready(function()
{
	prevnew = parent = { id: {$parent.id} ,taxon: '{$parent.taxon}' };
	currentrank = {$concept.base_rank};
	taxonomyCollectRanks();
	$( "#authorship" ).keyup(function() { taxonomyAuthorshipSplit(e) } );
});
</script>




{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
