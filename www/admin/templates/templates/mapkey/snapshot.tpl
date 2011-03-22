{include file="../shared/admin-header.tpl"}

<div id="page-main">

<div id="map_canvas" style="width:650px; height:500px"></div>
<div id="map_options">
<form action="" method="post">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" value="{$snapshot.id}" />
<table>
	<tr>
		<td colspan="3"><input type="button" value="enable selection" id="btn-enable" onclick="toggleSelecting();" /></td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">Snapshot parameters:</td>
	</tr>
	<tr><td>Name:</td><td colspan="2"><input type="text" id="name-default" name="name" value="{$snapshot.name}" /></td></tr>
	<tr>
		<td>Start:</td>
		<td>latitude:</td>
		<td><input type="text" id="coordinate1_lat" name="coordinate1_lat" style="width:50px" value="{$snapshot.coordinate1_lat}" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>longitude:</td>
		<td><input type="text" id="coordinate1_lng" name="coordinate1_lng" style="width:50px" value="{$snapshot.coordinate1_lng}" /></td>
	</tr>
	<tr>
		<td>End:</td>
		<td>latitude:</td>
		<td><input type="text" id="coordinate2_lat" name="coordinate2_lat" style="width:50px" value="{$snapshot.coordinate2_lat}" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>longitude:</td>
		<td><input type="text" id="coordinate2_lng" name="coordinate2_lng" style="width:50px" value="{$snapshot.coordinate2_lng}" /></td>
	</tr>
	<tr>
		<td>Zoom:</td>
		<td colspan="2"><input type="text" id="zoom" name="zoom" value="{$snapshot.zoom}" /></td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="button" value="clear selection" onclick="removeRectangle();setFormParameters(true)" />&nbsp;
			<input type="submit" value="save" />
		</td>
	</tr>
</table>
</form>
</div>
<div id="coordinates"><span id="coordinates-start"></span><span id="coordinates-end"></span></div>
</div>
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	initMap({$middelLat}, {$middelLng}, {$initZoom}{if $snapshot}, [{$snapshot.coordinate1_lat},{$snapshot.coordinate1_lng},{$snapshot.coordinate2_lat},{$snapshot.coordinate2_lng}]{/if});
	addMouseClickHandlers();
{literal}
});
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}