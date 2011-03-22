{include file="../shared/admin-header.tpl"}

<div id="page-main">
<div id="map_canvas" style="width:650px; height:500px"></div>
<div id="map_options" style="width:250px; height:500px;position:relative;margin-top:-500px;left:660px;">
Name: {$snapshot.name}<br />
Start - lat {$snapshot.coordinate1_lat},{$snapshot.coordinate1_lng}<br />
End - lat: {$snapshot.coordinate2_lat},{$snapshot.coordinate2_lng}<br />
Zoom level: {$snapshot.zoom}<br />
<a href="snapshot.php?id={$snapshot.id}">edit</a>
</div>
<div id="coordinates"><span id="coordinates-start"></span><span id="coordinates-end"></span></div>
</div>
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

	initMap({$middelLat}, {$middelLng}, {$snapshot.zoom}, [{$snapshot.coordinate1_lat},{$snapshot.coordinate1_lng},{$snapshot.coordinate2_lat},{$snapshot.coordinate2_lng}]);

{literal}
});
</script>
{/literal}

{include file="../shared/admin-footer.tpl"}
