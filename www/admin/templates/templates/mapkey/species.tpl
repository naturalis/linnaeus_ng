{include file="../shared/admin-header.tpl"}
<div id="page-main">

<b>Occurrences of {$taxon.taxon}</b>

<p>
To view a single occurrence, click the 'view on map' link of that occurrence.<br />
To view multiple occurrences, check the checkboxes of all the occurrences you want to see and click 'show selected'.
</p>
<form action="species_show.php" method="post">
<input type="hidden" name="id" value="{$taxon.id}" />
<input type="hidden" name="s" value="{$taxon.id}" />
<table>
	<thead>
	<tr class="tr-highlight">
		<th style="width:250px">Data type</th>
		<th style="width:75px">Marker type</th>
		<th style="width:300px">Coordinates</th>
		<th colspan="2"></th>
	</tr>
	</thead>
	{foreach from=$taxon.occurrences key=l item=o}
	<tr class="tr-highlight">
		<td>{$o.type_title}</td>
		<td>{$o.type}</td>
		<td>{if $o.type==marker}({$o.latitude},{$o.longitude}){else}{$o.boundary_nodes|@substr:1:50}...{/if}</td>
		<td>[<a href="species_show.php?s={$taxon.id}&id={$o.id}">view on map</a>]</td>
		<td><input type="checkbox" id="species-{$o.id}" name="id[]" value="{$o.id}"></td>
	</tr>
	{/foreach}
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="submit" value="show selected" />
			<input type="button" id="select-all" value="select all" onclick="toggleAllSpecies()" />
		</td>
	</tr>
</table>
</form>

</div>
{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
