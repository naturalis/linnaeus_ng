{include file="../shared/admin-header.tpl"}

<style>
.toggler {
	cursor:pointer;
}
.identical {
	color:red;
}
.similar{
	color:orange;
}
.head{
	color:black;
}
</style>

<div id="page-main">

{if $synonyms|@count==0}
{t}No synonyms have been defined.{/t}
{else}
<p class="instruction-text">
<span class="similar toggler" onclick="$('tr:not(\'[class*=similar]\')').toggle();">Orange synonyms</span> are identical to the one before, but have a different author.
<span class="identical toggler" onclick="$('tr:not(\'[class*=identical]\')').toggle();">Red ones</span> are identical in both synonym and author.<br />
Double-click a synonym or author (of the empty cell where the author should be) to edit. When editing, Enter saves the new entry, Escape cancels the edit.
<span class="message-error">Be aware: clicking the delete button immediately deletes the synonym, without confirmation.</span>
</p>
<table>
	<tr class='similar identical head'>
		<th style="width:10px" title="{t}corresponding taxon{/t}">taxon</th>
		<th style="width:400px">{t}synonym{/t}</th>
		<th style="width:300px">{t}author{/t}</th>
		<th title="{t}delete synonym{/t}">delete</th>
	</tr>
	{foreach from=$synonyms item=v key=k}
	<tr class="tr-highlight
		{if $v.taxon==$synonyms[$k+1].taxon && $v.synonym==$synonyms[$k+1].synonym && $v.author!=$synonyms[$k+1].author}
		prev-similar
		{/if}
		{if $prevz==$v.taxon_id && $v.synonym==$preva && $v.author==$prevb}
		identical
		{else if $prevz==$v.taxon_id && $v.synonym==$preva}
		similar
		{/if}" 
		style="vertical-align:top" id="syn-{$v.id}">

		<td style="white-space:nowrap"><a href="synonyms.php?id={$v.taxon_id}" style="color:#777">{$v.taxon}</a></td>
		<td ondblclick="taxonSynonymEditSyn(this,{$v.id});">{$v.synonym}</td>
		<td ondblclick="taxonSynonymEditAuth(this,{$v.id});">{$v.author}</td>
		<td style="text-align:center" class="a" onclick="taxonEasySynonymDelete({$v.id});">x</td>
	</tr>
	{assign var=prevz value=$v.taxon_id}
	{assign var=preva value=$v.synonym}
	{assign var=prevb value=$v.author}
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