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
