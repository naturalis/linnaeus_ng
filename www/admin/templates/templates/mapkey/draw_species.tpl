{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<div id="map_canvas" style="width:650px; height:500px">{if !$isOnline}Unable to display map.{/if}</div>
	<div id="map_options">
		<b>Occurrences for "{$taxon.taxon}"</b><br/>
		Selection type: <span id="selection-type">(none)</span><br />
		Coordinates:<br /><span id="coordinates">(-1,-1)</span><br />
		
		<form action="" method="post" id="theForm">
		<input type="hidden" name="id" value="{$taxon.id}" />
		<input type="hidden" name="rnd" value="{$rnd}" />
		<p style="text-align:justify">
		To enable setting markers (points on the map), click the button below. Then click on the appropriate spot on the map to place a marker. To remove a marker, right-click on it.<br />
		<input type="button" value="set markers" onclick="setOccurrenceType('marker');" />
		</p>
		<p style="text-align:justify">
		To enable drawing polygons, click the button below. Then draw the polygon by clicking the appropriate spots on the map. When finished drawing, click the button again. To remove a polygon, right-click on it.<br />
		<input type="button" value="draw a polygon" id="polygon-button" onclick="createPolygon();" />
		</p>
		<p>
		When you are done, click 'save' to store the occurrences.<br />
		<input type="button" onclick="saveAll()" value="save" />
		</p>
		</form>
	</div>
</div>
<div id="x"></div>
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{if $isOnline}

	initMap({$mapInitString});	
	addMouseHandlers();

{else}
	alert('Your computer appears to be offline.\nUnable to display map.');
{/if}	
{literal}
});
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}