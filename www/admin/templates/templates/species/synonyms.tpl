{include file="../shared/admin-header.tpl"}

<div id="page-main">


<p>
	<input type="hidden" name="taxon_id" id="taxon_id" value="{$id}" />
	<input type="button" value="{t}main page{/t}" onclick="window.open('taxon.php?id={$taxon.id}','_top')" />
</p>



{if $synonyms|@count==0}
{t}No synonyms have been defined for this taxon.{/t}
{else}
<table>
	<tr>
		<th style="width:350px;">{t}synonym{/t}</td>
		<th style="width:300px;">{t}author{/t}</td>
		<th style="width:55px;">{t}move up{/t}</td>
		<th style="width:65px;">{t}move down{/t}</td>
		<th>delete</td>
	</tr>
	{section name=i loop=$synonyms}
	<tr class="tr-highlight">
		<td>{$synonyms[i].synonym}</td>
		<td>{$synonyms[i].author}</td>
		{if $smarty.section.i.first}
		<td></td>
		{else}
		<td
			style="text-align:center" 
			class="a" 
			onclick="taxonSynonymAction({$synonyms[i].id},'up');">
			&uarr;
		</td>
		{/if}
		{if $smarty.section.i.last}
		<td></td>
		{else}
		<td
			style="text-align:center" 
			class="a" 
			onclick="taxonSynonymAction({$synonyms[i].id},'down');">
			&darr;
		</td>
		{/if}
		<td
			style="text-align:center" 
			class="a" 
			onclick="taxonSynonymAction({$synonyms[i].id},'delete');">
			x
		</td>
	</tr>
	{/section}
</table>
{/if}
<hr style="color:#eee;height:1px" />
<form method="post" action="" id="theForm">
<input type="hidden" name="action" id="action" value="" />
<input type="hidden" name="synonym_id" id="synonym_id" value="" />
<input type="hidden" name="id" value="{$id}" />
<input type="hidden" name="rnd" value="{$rnd}" />
<table>
<tr><td colspan="2">{t}Add a new synonym:{/t}</td></tr>
<tr><td>{t}synonym:{/t}</td><td><input type="text" name="synonym" maxlength="128" /></td></tr>
<tr><td>{t}author:{/t}</td><td><input type="text" name="author" maxlength="255" /></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><input type="submit" value="{t}save{/t}" /></td></tr>
</table>
</form>
</div>


{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
allLookupNavigateOverrideUrl('synonyms.php?id=%s');
{literal}
});
</script>
{/literal}


{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}