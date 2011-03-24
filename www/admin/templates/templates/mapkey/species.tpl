{include file="../shared/admin-header.tpl"}
{literal}
<script>
var allChecked = false;
function toggleAllSpecies() {

	if (allChecked)
		$('input[id*=species-]').attr('checked',false)
	else
		$('input[id*=species-]').attr('checked',true)

	allChecked = !allChecked;
	
	$('#select-all').val(allChecked ? 'deselect all' : 'select all');

}
</script>
{/literal}
<div id="page-main">
<p>
Click the 'view on map' link of the occurrence you want to see, or check the checkboxes of all the occurrences you want to see and click 'show selected'.
<p>
<form action="species_show.php" method="post">
<table>
{foreach from=$taxa key=k item=v}
	<tr class="tr-highlight">
		<td>{$v.taxon}</td>
	{foreach from=$v.occurrences key=l item=o}
	{if $l!=0}
	<tr class="tr-highlight">
		<td>&nbsp;</td>
	{/if}
		<td>{$o.type}</td>
		<td>{if $o.type==marker}{$o.coordinate}{else}{$o.coordinate}{/if}</td>
		<td>[<a href="species_show.php?id={$o.id}">view on map</a>]</td>
		<td><input type="checkbox" id="species-{$o.id}" name="id[]" value="{$o.id}"></td>
	</tr>
	{/foreach}
{/foreach}
</table>
<p>
<input type="submit" value="show selected" />
<input type="button" id="select-all" value="select all" onclick="toggleAllSpecies()" />
</form>
</p>
</div>
{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
