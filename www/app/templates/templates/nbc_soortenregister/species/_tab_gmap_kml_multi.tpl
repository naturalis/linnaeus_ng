
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;" type="text/javascript"></script>
<script type="text/javascript">
var gmap = null;
var dynMapOv = null;
var geoXml = null;
var centerat = null;
var zoomlevel = null;

var kmlUrl;//="http://www.leidenuniv.nl/cml/mashup/kml_kmz/S_aristida_adscensionis_kleur.kmz";

function GMapInitialize()
{
  //Load Google Maps
  gmap = new GMap2(document.getElementById("gmap"));
  gmap.addMapType(G_PHYSICAL_MAP);

  var centerat = new GLatLng(11.0, 11.0);
  var topRight = new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(10,10));
  var topLeft = new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(10,10));
  var bottomRight = new GControlPosition(G_ANCHOR_BOTTOM_RIGHT, new GSize(10,10));
  gmap.addControl(new GLargeMapControl(),topRight);
  gmap.addControl(new GScaleControl());
  gmap.addControl(new GOverviewMapControl());

  // Create a hierarchical map type control
  var hierarchy = new GHierarchicalMapTypeControl();
  // Make Hybrid the Satellite default
  hierarchy.addRelationship(G_SATELLITE_MAP, G_HYBRID_MAP, "Labels", false);
  // Add the control to the map
  gmap.addControl(hierarchy);
  gmap.setMapType(G_HYBRID_MAP);
  gmap.addControl(new GNavLabelControl (), topLeft);
  gmap.setCenter(centerat, 4);

  geoXml = new GGeoXml( kmlUrl );
  // alert (geoXml.IconStyle)
  gmap.addOverlay(geoXml);
  gmap.enableScrollWheelZoom();
}

$(document).ready(function()
{
	$('body').on('unload',function() { GUnload(); } );
	kmlUrl = '{$external_content->full_url|@escape}';
	GMapInitialize();
});

</script>


<p>
    <h2 id="name-header">{$requested_category.title}</h2>

    {if $content}
    <p>
        {$content}
    </p>
    {/if}
    
    {$external_content->full_url|@escape}
            
	<div id="gmap" border=1 style="width:500px; height:600px;"></div>

</p>


