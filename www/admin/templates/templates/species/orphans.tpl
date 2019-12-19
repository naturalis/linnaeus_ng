{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if $taxa}
<p>
{t}Select the desired option for each of the taxa listed below and press 'save'.{/t}
</p>
<form method="post" action="" name="theForm">
{assign var=y value=$taxa[0].level}
<table>
	<tr>
		<th>{t}Rank{/t}</th>
		<th>{t}Taxon{/t}</th>
		{if $session.admin.project.includes_hybrids==1}
		<th>{t}Hybrid{/t}</th>
		{/if}
		<th>{t}Delete{/t}</th>
		<th colspan="2">{t}Attach to parent:{/t}</th>
		<th>{t}Do nothing{/t}</th>
	</tr>
    
	{foreach from=$taxa key=k item=v}
	{assign var=x value=$v.rank_id}
	<tr class="tr-highlight">
		<td style="padding-right:20px;">
			{$ranks[$v.rank_id].rank}
		</td>
		<td style="padding-right:20px;">
			<a href="edit.php?id={$v.id}">{$v.taxon}</a>
		</td>
		{if $session.admin.project.includes_hybrids==1}
		<td style="padding-right:20px;">
			{if $v.is_hybrid==1}<span class="taxon-hybrid-x">x</span>{/if}
		</td>
		{/if}
		{if $v.level==$y}
		<td style="text-align:center">
			<input type="radio" name="child[{$v.id}]" value="delete" />
		</td>
		<td style="text-align:center">
			<input type="radio" name="child[{$v.id}]" id="attach-{$v.id}" value="attach" />
		</td>
		<td style="text-align:center">
		<select name="parent[{$v.id}]" id="parent-{$v.id}" onchange="taxonOrphanChangeSelect(this)">
			{foreach from=$tree key=pk item=pv}
			<option value="{$pv.id}" {if $data.parent_id==$v.id}selected="selected"{/if}>
            {'&nbsp;&nbsp;'|str_repeat:$pv.level}
			{$pv.taxon_formatted}</option>
			{/foreach}
		</select>
        {*<input type="button" value="&larr;" onclick="bla('text-{$v.id}','parent-{$v.id}');"/><input id="text-{$v.id}" type="text" value="" />*}
		</td>
		<td style="text-align:center">
			<input type="radio" checked="checked" name="child[{$v.id}]" value="ignore" />
		</td>
		{else}
		<td colspan="4">
		</td>
		{/if}
	</tr>
	{if $v.tree}
		{foreach from=$v.tree key=pk item=pv}
		{assign var=z value=$pv.rank_id}
		<tr class="tr-highlight">
			<td style="padding-right:20px;">
				{section name=loop start=0 loop=$pv.level+1}.{/section}{$ranks[$z].rank}
			</td>
			<td style="padding-right:20px;">
				{$pv.taxon}
			</td>
			{if $session.admin.project.includes_hybrids==1}
			<td style="padding-right:20px;">
				{if $pv.is_hybrid==1}<span class="taxon-hybrid-x">x</span>{/if}
			</td>
			{/if}
			<td colspan="3">
			</td>
		</tr>
		{/foreach}
	{/if}
	{/foreach}
</table>
<p>
<input type="submit" value="{t}save{/t}" />&nbsp;
<input type="button" value="{t}back{/t}" onclick="window.open('branches.php','_self');"
</form>
</p>
{else}
{t}There are currently no orphaned taxa in your database.{/t}<br />
<a href="branches.php">{t}Back{/t}</a>
{/if}
</div>

{* literal}
<script>
function bla(src,tgt) {

	var b = $('#'+src).val();
	b = b.trim();
	
	$('#'+tgt+' > option').each(function(i){
		var s = $(this).text();
		if (s.trim()==b) $(this).text(s+' <-----------------------------');
	});
	
}
</script>
{/literal *}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}