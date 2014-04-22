{include file="../shared/admin-header.tpl"}
<style>
.taxonomy input[name*=valid_name]{
	width:135px;
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
		<td>Rank:</td><td>
		<select name="rank" id="rank">
		{foreach from=$ranks item=v}
			<option value="{$v.id}" {if $v.id==$concept.rank_id} selected="selected"{/if}>{$v.rank}</option>
		{/foreach}
		</select>
		</td>
	</tr>
	<tr>
		<td>Parent:</td>
		<td id="parent">{$parent.taxon}</td>
		<td>
			<a class="inline-href" href="#" onclick="taxonomyEditParent()">edit</a>
			<a class="inline-href" href="#" onclick="taxonomyRevertParent()">revert</a>
		</td>
	</tr>
	<tr>
		<td>Valid name:</td><td></td>
	</tr>
</table>
<table>
	<tr>
		<td><input type="text" name="valid_name[uninomial]" id="uninomial" value="{$names.valid_name.uninomial}" /></td>
		<td><input type="text" name="valid_name[specific_epithet]" id="specific_epithet" value="{$names.valid_name.specific_epithet}" /></td>
		<td><input type="text" name="valid_name[infra_specific_epithet]" id="infra_specific_epithet" value="{$names.valid_name.infra_specific_epithet}" /></td>
		<td><input type="text" name="valid_name[authorship]" id="authorship" value="{$names.valid_name.authorship}" /></td>

		<td><input type="text" name="valid_name[name_author]" id="name_author" class="lighter" value="{$names.valid_name.name_author}" /></td>
		<td><input type="text" name="valid_name[authorship_year]" id="authorship_year" class="lighter year" value="{$names.valid_name.authorship_year}" />
			<a class="inline-href" href="#" onclick="taxonomyRevertValidName()">revert</a>
		
		</td>

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
</div>

<script type="text/JavaScript">
$(document).ready(function()
{
	prevnew=parent= { id: {$parent.id} , taxon: '{$parent.taxon}' };

	valid_name = {
		uninomial: '{$names.valid_name.uninomial}',
		specific_epithet: '{$names.valid_name.specific_epithet}',
		infra_specific_epithet: '{$names.valid_name.infra_specific_epithet}',
		authorship: '{$names.valid_name.authorship}',
		name_author: '{$names.valid_name.name_author}',
		authorship_year: '{$names.valid_name.authorship_year}'
	}
		
	currentrank = {$concept.base_rank};
	taxonomyCollectRanks();
	$( "#authorship" ).keyup(function() { taxonomyAuthorshipSplit(e) } );

	allLookupNavigateOverrideUrl('taxonomy.php?id=%s');

});
</script>




{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
