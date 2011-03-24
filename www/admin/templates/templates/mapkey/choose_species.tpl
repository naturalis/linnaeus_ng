{include file="../shared/admin-header.tpl"}

<div id="page-main">

MUST PAGINATE<br /><br />

<table>
{foreach from=$taxa key=k item=v}
{if $v.lower_taxon==1}
	<tr class="tr-highlight">
		<td>{$v.taxon}</td>
		<td>[<a href="draw_species.php?id={$v.id}">add occurrences</a>]</td>
	</tr>
{/if}
{/foreach}
</table>
<p>
You can also define multiple occurrences at once by <a href="file.php">uploading a file</a>.
</p>
</div>
{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
