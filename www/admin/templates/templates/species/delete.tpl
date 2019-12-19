{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if $taxon}
<p>
{assign var=x value=$parent.rank_id}
{t _s1=$taxon.taxon}You are about to delete the taxon "%s", which has child taxa connected to it. Please specify what should happen to the connected child taxa. There are three possibilities:{/t}
<ol>
	<li>
		<span class="taxon-delete-option">{t}Orphans:{/t}</span>
		{t}turn them into "orphans". Orphans are taxa that are unconnected to the main taxon tree. You will need to individually reattach them later.{/t}
	</li>
	<li>
		<span class="taxon-delete-option">{t}Delete:{/t}</span>
		{t _s1=$taxon.taxon}delete them as well. Effectively this will delete the entire branch from taxon "%s" and down.{/t}
	</li>
	<li>
		<span class="taxon-delete-option">{t}Attach:{/t}</span>
		{t _s1=$taxon.taxon _s2=$ranks[$x].rank _s3=$parent.taxon}attach them as child to the parent of "%s", which is the %s "%s". There will be no change in the rank of the reattached taxa.{/t}
	</li>
</ol>
{t}Select the desired option for each of the taxa listed below and press 'save'.{/t}
</p>
<form method="post" action="" name="theForm">
<input type="hidden" name="id" value="{$taxon.id}" />
<input type="hidden" name="action" value="process" />
{assign var=y value=$taxa[0].level}
<table>
	<tr>
		<th>{t}Rank{/t}</th>
		<th>{t}Taxon{/t}</th>
		<th>{t}Delete{/t}</th>
		<th>{t}Orphan{/t}</th>
		<th>{t}Attach to{/t} {$ranks[$x].rank} "{$parent.taxon}"</th>
	</tr>
	{foreach from=$taxa item=v}
	{assign var=x value=$v.rank_id}
	<tr class="tr-highlight">
		<td style="padding-right:20px;">
			{section name=loop start=0 loop=$v.level}.{/section}{$ranks[$x].rank}
		</td>
		<td style="padding-right:20px;">
			{$v.taxon}
		</td>
		{if $taxa[i].level==$y}
		<td style="text-align:center">
			<input type="radio" checked="checked" name="child[{$v.id}]" value="delete" />
		</td>
		<td style="text-align:center">
			<input type="radio" name="child[{$v.id}]" value="orphan" />
		</td>
		<td style="text-align:center">
			<input type="radio" name="child[{$v.id}]" value="attach" />
		</td>
		{else}
		<td colspan="3">
		</td>
		{/if}
	</tr>
	{/foreach}
</table>
<p>
<input type="submit" value="{t}save{/t}" />&nbsp;
<input type="button" value="{t}back{/t}" onclick="window.open('index.php','_self');"
</form>
</p>
{/if}
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}