{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

allLookupSuppressKeyNavigation = true;

{if $map.mapExists}
l2SetMap(
    '{$map.imageFullName|replace:' ':'%20'}',
    {$map.width},
    {$map.height},
    '{$map.coordinates.original}',
    {$map.cellWidth},
    {$map.cellHeight},
    {$map.resized}
);
{/if}
