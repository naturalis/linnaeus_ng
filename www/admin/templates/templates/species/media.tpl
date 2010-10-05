{include file="../shared/admin-header.tpl"}

{include file="../shared/admin-messages.tpl"}

{if $id}
<div id="page-main">
<div>
<span class="taxon-media-classheader">Images</span><br />
<table class="taxon-media-table">
<tr><th class="taxon-media-colheader-file">Media</th><th class="taxon-media-colheader-name">Description (click to change)</th><th class="taxon-media-colheader-type">Name, type &amp; size</th><th></th></tr>
{section name=i loop=$media.image}
<tr class="tr-highlight" style="vertical-align:top">
<td>
{if $media.image[i].thumb_name != ''}
	<img src="{$session.project.urls.project_thumbs}{$media.image[i].thumb_name}" style="width:150px;border:1px solid black;margin-bottom:5px" />
{else}
	<img src="{$session.project.urls.project_media}{$media.image[i].file_name}" style="width:150px;border:1px solid black;margin-bottom:5px" />
{/if}
</td>
<td id="media-{$media.image[i].id}" onclick="taxonMediaDescription(this);"></td>
<td>{$media.image[i].original_name}<br /><span class="taxon-media-secondary-info">({$media.image[i].mime_type}; {$media.image[i].file_size} kb)</span></td>
</tr>
{/section}
</table>
</div>
<br />
<div>
<span class="taxon-media-classheader">Videos</span><br />
<table class="taxon-media-table">
<tr><th class="taxon-media-colheader-file">Media</th><th class="taxon-media-colheader-name">Description</th><th class="taxon-media-colheader-type">Name, type &amp; size</th><th></th></tr>
{section name=i loop=$media.video}
<tr class="tr-highlight" style="vertical-align:top">
<td>VIDEO</td>
<td id="media-{$media.video[i].id}" onclick="taxonMediaDescription(this);"></td>
<td>{$media.video[i].original_name}<br />
<span class="taxon-media-secondary-info">({$media.video[i].mime_type}; {$media.video[i].file_size} kb)</span></td>
</tr>
{/section}
</table>
</div>
<br />
<div>
<span class="taxon-media-classheader">Sound</span><br />
<table class="taxon-media-table">
<tr><th class="taxon-media-colheader-file">Media</th><th class="taxon-media-colheader-name">Description</th><th class="taxon-media-colheader-type">Name, type &amp; size</th><th></th></tr>
{section name=i loop=$media.sound}
<tr class="tr-highlight">
<td>
<object type="application/x-shockwave-flash" data="../../tools/player_mp3.swf" width="130" height="20">
	<param name="movie" value="player_mp3.swf" />
	<param name="FlashVars" value="mp3={$session.project.urls.project_media}{$media.sound[i].file_name}" />
</object>
</td>
<td id="media-{$media.sound[i].id}" onclick="taxonMediaDescription(this);"></td>
<td>{$media.sound[i].original_name}<br />
<span class="taxon-media-secondary-info">({$media.sound[i].mime_type}; {$media.sound[i].file_size} kb)</span></td>
{/section}
</table>
</div>
<p>
<a href="media_upload.php?id={$id}">Upload new</a>
</p>
</div>
{/if}


{include file="../shared/admin-footer.tpl"}