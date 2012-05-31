{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{if $map.mapExists}
l2SetMap(
    '{$map.imageFullName|replace:' ':'%20'}',
    {$map.size[0]},
    {$map.size[1]},
    '{$map.coordinates.original}',
    {math equation="(floor( x / y ))-z" x=$map.size[0] y=$map.cols z=1},
    {math equation="(floor( x / y ))-z" x=$map.size[1] y=$map.rows z=1}
);
{/if}

{if $didSearch==true}
showDialog(
    _('Found {$taxa|@count} taxa'),
    '<p style="margin:0px;height:250px;overflow-y:scroll">'+
    {foreach from=$taxa item=v}'<a href="l2_examine_species.php?id={$v.id}&m={$mapId}&ref=search">{$v.taxon|escape:'htmlall'}</a><br />'+
    {/foreach}'</p>'
);
{/if}

{literal}
$("#mapTable").mousemove(function(event) {
    l2MapMouseOver(event.pageX,event.pageY);
}); 

});
</script>
{/literal}
