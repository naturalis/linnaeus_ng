{include file="../shared/admin-header.tpl"}

<div id="page-main">
<table>
{foreach from=$taxa key=k item=v}
	<tr>
		<td>{$v.taxon}</td>
	{foreach from=$v.occurrences key=l item=o}
	{if $l!=0}
	<tr>
		<td>&nbsp;</td>
	{/if}
		<td>{$o.type}</td>
		<td>{if $o.type==marker}{$o.coordinate}{else}{$o.coordinate}{/if}</td>
		<td>[<a href="species_show.php?id={$o.id}">view on map</a>]</td>
	</tr>
	{/foreach}
{/foreach}
</table>

<a href="file.php">Upload a file</a>
</div>
{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
