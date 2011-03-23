{include file="../shared/admin-header.tpl"}

<div id="page-main">
<div id="map_canvas" style="width:650px; height:500px">{if !$isOnline}Unable to display map.{/if}</div>
<div id="map_options" style="width:250px; height:500px;position:relative;margin-top:-500px;left:660px;">
Name: {$mapView.name}<br />
Start - lat {$mapView.coordinate1_lat},{$mapView.coordinate1_lng}<br />
End - lat: {$mapView.coordinate2_lat},{$mapView.coordinate2_lng}<br />
Zoom level: {$mapView.zoom}<br />
<a href="snapshot.php?id={$mapView.id}">edit</a>
</div>
<div id="coordinates"><span id="coordinates-start"></span><span id="coordinates-end"></span></div>
</div>
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{if $isOnline}
	initMap({$middelLat}, {$middelLng}, {$mapView.zoom}, [{$mapView.coordinate1_lat},{$mapView.coordinate1_lng},{$mapView.coordinate2_lat},{$mapView.coordinate2_lng}]);
{else}
alert('Your computer appears to be offline.\nUnable to display map.');
{/if}
{literal}
});
</script>
{/literal}

{include file="../shared/admin-footer.tpl"}
