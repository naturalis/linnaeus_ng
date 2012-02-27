{include file="../shared/admin-header.tpl"}
{assign var=map value=$maps[$mapId]}
<div id="page-main">
<p>
{if $maps|@count>1}{t}Switch to another map:{/t} {/if}
{if $maps|@count>1}
{foreach item=v from=$maps}{if $v.id!=$mapId}<a href="?id={$taxon.id}&mapId={$v.id}">{$v.name}</a>, {/if}{/foreach}
{else}{t}Switch to{/t}{/if}
<a href="species_edit.php?id={$taxon.id}">{t}editable map{/t}</a>
</p>

{if $map.mapExists}
<style>
{literal}
.mapCell {
	width:{/literal}{math equation="(floor( x / y ))-z" x=$map.size[0] y=$map.cols z=1}{literal}px;
	height:{/literal}{math equation="(floor( x / y ))-z" x=$map.size[1] y=$map.rows z=1}{literal}px;
	padding:0px;
	margin:0px;
	border-right:1px solid #777;
	border-bottom:1px solid #777;
	filter: alpha(opacity=60);
	-moz-opacity: .60;
	-khtml-opacity: 0.60;
	opacity: .60;
}
{/literal}
</style>
<table 
	style="
		background:url({$session.admin.project.urls.project_media_ln2_maps}{$map.image|replace:' ':'%20'});
		width:{$map.size[0]}px;
		height:{$map.size[1]}px;
		padding:0px;
		margin:0px;
		border-collapse:collapse;
		border:1px solid #777;
		"
>
{assign var=cellNo value=1}
{section name=rows start=1 loop=$map.rows step=1}
	<tr>
	{section name=cols start=1 loop=$map.cols step=1}
		<td 
			lat="{math equation="((((a-b)/c) * d) + b)" a=$map.coordinates.bottomRight.lat b=$map.coordinates.topLeft.lat c=$map.rows d=$smarty.section.rows.index}"
			long="{math equation="((((a-b)/c) * d) + b)" a=$map.coordinates.bottomRight.long b=$map.coordinates.topLeft.long c=$map.cols d=$smarty.section.cols.index}"
			id="cell-{$cellNo}" 
			class="mapCell" {if $occurrences[$cellNo].square_number==$cellNo}style="background-color:#{$occurrences[$cellNo].colour}"{/if}></td>
		{assign var=cellNo value=$cellNo+1}
	{/section}
	</tr>
{/section}
</table>
<div>{$map.name} / <span id=coordinates></span></div>
{else}
<div>
{t _s1=$map.name}The image file for the map "%s" is missing.{/t}
</div>
{/if}
{* if $maps|@count>1}
<div>
{t}Switch to another map:{/t}<br />
{foreach item=v from=$maps}
{if $v.id!=$mapId}
<a href="?id={$taxon.id}&mapId={$v.id}"><img src="{$session.admin.project.urls.project_media_ln2_maps}{$v.image|replace:' ':'%20'}" style="width:50px;"/><br />{$v.name}</a></a><br />
{/if}
{/foreach}
</div>
{/if *}
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){

	allLookupNavigateOverrideUrl('ln2_species_show.php?id=%s&mapId={/literal}{$mapId}{literal}');
	$("td").hover(function(){
		$('#coordinates').html($(this).attr('lat')+'&deg;N, '+$(this).attr('long')+'&deg;E');
	});


{/literal}

var cellData = Array();
{foreach item=v from=$occurrences}
	cellData[{$v.square_number}] = ['{$v.coordinates}','{$v.legend}'];
{/foreach}

{literal}
});
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}