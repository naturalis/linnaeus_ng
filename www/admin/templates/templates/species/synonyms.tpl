{include file="../shared/admin-header.tpl"}

<div id="page-main">
<table>
<tr>
	<td colspan="6">{t _s1=$taxon}Synonyms for taxon "%s":{/t}</td>
</tr>
<tr><td colspan="6">&nbsp;</td></tr>
<tr>
	<th style="width:200px;">{t}synonym{/t}</td>
	<th style="width:250px;">{t}remark{/t}</td>
	<th style="width:150px;">{t}literature{/t}</td>
	<th style="width:55px;">{t}move up{/t}</td>
	<th style="width:65px;">{t}move down{/t}</td>
	<th>delete</td>
</tr>
{section name=i loop=$synonyms}
<tr class="tr-highlight">
	<td>{$synonyms[i].synonym}</td>
	<td>{$synonyms[i].remark}</td>
	<td>(literature)</td>
	{if $smarty.section.i.first}
	<td></td>
	{else}
	<td
		style="text-align:center" 
		class="pseudo-a" 
		onclick="taxonSynonymAction({$synonyms[i].id},'up');">
		&uarr;
	</td>
	{/if}
	{if $smarty.section.i.last}
	<td></td>
	{else}
	<td
		style="text-align:center" 
		class="pseudo-a" 
		onclick="taxonSynonymAction({$synonyms[i].id},'down');">
		&darr;
	</td>
	{/if}
	<td
		style="text-align:center" 
		class="pseudo-a" 
		onclick="taxonSynonymAction({$synonyms[i].id},'delete');">
		x
	</td>
</tr>
{/section}
{if $smarty.section.i.total==0}
<tr><td colspan="6">{t}No synonyms have been defined for this taxon.{/t}</td></tr>
{/if}
</table>
<br />
<form method="post" action="" id="theForm">
<input type="hidden" name="action" id="action" value="" />
<input type="hidden" name="synonym_id" id="synonym_id" value="" />
<input type="hidden" name="id" value="{$id}" />
<input type="hidden" name="rnd" value="{$rnd}" />
<table>
<tr><td colspan="2">{t}Add a new synonym:{/t}</td></tr>
<tr><td>synonym:</td><td><input type="text" name="synonym" maxlength="32" /></td></tr>
<tr><td>remark:</td><td><input type="text" name="remark" maxlength="255" /></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><input type="submit" value="{t}save{/t}" />&nbsp;<input type="button" onclick="window.open('list.php','_self');" value="{t}back{/t}" /></td></tr>
</table>
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}


