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
		{if $session.project.includes_hybrids==1}
		<th>{t}Hybrid{/t}</th>
		{/if}
		<th>{t}Delete{/t}</th>
		<th colspan="2">{t}Attach to parent:{/t}</th>
		<th>{t}Do nothing{/t}</th>
	</tr>
	{section name=i loop=$taxa}
	{assign var=x value=$taxa[i].rank_id}
	<tr class="tr-highlight">
		<td style="padding-right:20px;">
			{$ranks[$x].rank}
		</td>
		<td style="padding-right:20px;">
			{$taxa[i].taxon}
		</td>
		{if $session.project.includes_hybrids==1}
		<td style="padding-right:20px;">
			{if $taxa[i].is_hybrid==1}<span class="taxon-hybrid-x">x</span>{/if}
		</td>
		{/if}
		{if $taxa[i].level==$y}
		<td style="text-align:center">
			<input type="radio" name="child[{$taxa[i].id}]" value="delete" />
		</td>
		<td style="text-align:center">
			<input type="radio" name="child[{$taxa[i].id}]" id="attach-{$taxa[i].id}" value="attach" />
		</td>
		<td style="text-align:center">
		
	<select name="parent[{$taxa[i].id}]" id="parent-{$taxa[i].id}" onchange="taxonOrphanChangeSelect(this)">
	{section name=k loop=$taxa[i].parents}
	<option value="{$taxa[i].parents[k].id}" {if $data.parent_id==$taxa[i].id}selected="selected"{/if}>
	{section name=foo loop=$taxa[i].parents[k].level-$taxa[i].parents[0].level}
	&nbsp;
	{/section}		
	{$tree[k].taxon}</option>
	{/section}
	</select>
		</td>
		<td style="text-align:center">
			<input type="radio" checked="checked" name="child[{$taxa[i].id}]" value="ignore" />
		</td>
		{else}
		<td colspan="4">
		</td>
		{/if}
	</tr>
	{if $taxa[i].tree}
		{section name=j loop=$taxa[i].tree}
		{assign var=z value=$taxa[i].tree[j].rank_id}
		<tr class="tr-highlight">
			<td style="padding-right:20px;">
				{section name=loop start=0 loop=$taxa[i].tree[j].level+1}.{/section}{$ranks[$z].rank}
			</td>
			<td style="padding-right:20px;">
				{$taxa[i].tree[j].taxon}
			</td>
			{if $session.project.includes_hybrids==1}
			<td style="padding-right:20px;">
				{if $taxa[i].tree[j].is_hybrid==1}<span class="taxon-hybrid-x">x</span>{/if}
			</td>
			{/if}
			<td colspan="3">
			</td>
		</tr>
		{/section}
	{/if}
	{/section}
</table>
<p>
<input type="submit" value="{t}save{/t}" />&nbsp;
<input type="button" value="{t}back{/t}" onclick="window.open('list.php','_self');"
</form>
</p>
{else}
{t}There are currently no orphaned taxa in your database.{/t}<br />
<a href="list.php">{t}Back{/t}</a>
{/if}
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}