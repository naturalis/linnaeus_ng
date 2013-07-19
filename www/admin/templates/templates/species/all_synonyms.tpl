{include file="../shared/admin-header.tpl"}

<div id="page-main">

{if $synonyms|@count==0}
{t}No synonyms have been defined.{/t}
{else}
<p class="instruction-text">
Double-click a synonym or author (of the empty cell where the author should be) to edit. When editing, Enter saves the new entry, Escape cancels the edit.
<span class="message-error">Be aware: clicking the delete button immediately deletes the synonym, without confirmation.</span>
</p>
<table>
	<tr>
		<th style="width:10px" title="{t}corresponding taxon{/t}">taxon</th>
		<th style="width:450px">{t}synonym{/t}</th>
		<th style="width:250px">{t}author{/t}</th>
		<th title="{t}delete synonym{/t}">delete</th>
	</tr>
	{foreach from=$synonyms item=v}
	<tr class="tr-highlight" id="syn-{$v.id}">
		<td style="white-space:nowrap"><a href="synonyms.php?id={$v.taxon_id}" style="color:#777">{$v.taxon}</a></td>
		<td ondblclick="taxonSynonymEditSyn(this,{$v.id});">{$v.synonym}</td>
		<td ondblclick="taxonSynonymEditAuth(this,{$v.id});">{$v.author}</td>
		<td style="text-align:center" class="a" onclick="taxonEasySynonymDelete({$v.id});">x</td>
	</tr>
	{/foreach}
</table>
{/if}
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