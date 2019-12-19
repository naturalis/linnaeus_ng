{include file="../shared/admin-header.tpl"}
<div id="page-main">

<b>{t _s1=$taxon.taxon}Occurrences of "%s"{/t}</b>

<p>
{t}To view a single occurrence, click the 'view on map' link of that occurrence.{/t}<br />
{t}To view multiple occurrences, check the checkboxes of all the occurrences you want to see and click 'show selected'.{/t}
</p>
<form action="species_show.php" method="post">
<input type="hidden" name="id" value="{$taxon.id}" />
<input type="hidden" name="s" value="{$taxon.id}" />
<table>
	<thead>
	<tr class="tr-highlight">
		<th style="width:250px">{t}Data type{/t}</th>
		<th style="width:75px">{t}Marker type{/t}</th>
		<th style="width:300px">{t}Coordinates{/t}</th>
		<th colspan="2"></th>
	</tr>
	</thead>
	{foreach from=$taxon.occurrences key=l item=o}
	<tr class="tr-highlight">
		<td>{$o.type_title}</td>
		<td>{$o.type}</td>
		<td>{if $o.type==marker}({$o.latitude},{$o.longitude}){else}{$o.boundary_nodes|@substr:1:50}...{/if}</td>
		<td>[<a href="species_show.php?s={$taxon.id}&id={$o.id}">{t}view on map{/t}</a>]</td>
		<td><input type="checkbox" id="species-{$o.id}" name="id[]" value="{$o.id}"></td>
	</tr>
	{/foreach}
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="{t}submit{/t}" value="show selected" />
			<input type="button" id="select-all" value="{t}select all{/t}" onclick="mapToggleAllSpecies()" />
		</td>
	</tr>
</table>
</form>

</div>
{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
