{include file="../shared/admin-header.tpl"}

{include file="../shared/admin-messages.tpl"}

{if $id}
<div id="page-main">

<span style="float:right">
	<span id="message-container" style="margin-right:0px">&nbsp;</span>
</span>

<div id="taxon-language-div"></div>


<div>
	<input type="hidden" name="taxon_id" id="taxon_id" value="{$id}" />  
	<a href="media_upload.php?id={$id}">{t}Upload media for this taxon{/t}</a><br /><br />

	<a name="image"></a>
	<span class="taxon-media-classheader">{t}Images{/t}</span><br />
	<table class="taxon-media-table">
		<tr>
			<th class="taxon-media-colheader-file">{t}Media{/t}</th>
			<th class="taxon-media-colheader-name">{t}Description (click to change){/t}</th>
			<th class="taxon-media-colheader-type">{t}Name, type &amp; size{/t}</th>
			<th></th>
		</tr>
	{section name=i loop=$media.image}
		<tr id="media-row-{$media.image[i].id}" class="tr-highlight" style="vertical-align:top">
			<td
				onclick="taxonMediaShowMedia('{$session.project.urls.project_media}{$media.image[i].file_name}','{$media.image[i].original_name}');" 
				style="cursor:pointer">
				{if $media.image[i].thumb_name != ''}
					<img
						src="{$session.project.urls.project_thumbs}{$media.image[i].thumb_name}"
						style="width:150px;border:1px solid black;margin-bottom:5px" />
				{else}
					<img
						src="{$session.project.urls.project_media}{$media.image[i].file_name}"
						style="width:150px;border:1px solid black;margin-bottom:5px" />
				{/if}
			</td>
			<td id="media-{$media.image[i].id}" onclick="taxonMediaDescriptionEdit(this);">
				{$media.image[i].description}
			</td>
			<td>
				{$media.image[i].original_name}<br />
				<span class="taxon-media-secondary-info">({$media.image[i].mime_type}; {$media.image[i].file_size} {t}kb{/t})</span>
				<script type="text/javascript">
					taxonMediaAddId({$media.image[i].id});
				</script>
			</td>
			<td onclick="taxonMediaDelete({$media.image[i].id},'image','{$media.image[i].original_name}');" class="taxon-media-cell-delete">
				<img src="{$baseUrl}admin/media/system/icons/cross.png" />
			</td>
		</tr>
	{/section}
	</table>
</div>

<br />

<div>
	<a name="video"></a>
	<span class="taxon-media-classheader">{t}Videos{/t}</span><br />
	<table class="taxon-media-table">
		<tr>
			<th class="taxon-media-colheader-file">{t}Media{/t}</th>
			<th class="taxon-media-colheader-name">{t}Description (click to change){/t}</th>
			<th class="taxon-media-colheader-type">{t}Name, type &amp; size{/t}</th>
			<th></th>
		</tr>
		{section name=i loop=$media.video}
		<tr id="media-row-{$media.video[i].id}" class="tr-highlight" style="vertical-align:top">
			<td style="cursor:pointer" onclick="window.open('{$session.project.urls.project_media}{$media.video[i].file_name}','_video');">
				<img 
					src="{$baseUrl}admin/media/system/icons/video.jpg" 
				/>
			</td>
			<td id="media-{$media.video[i].id}" onclick="taxonMediaDescriptionEdit(this);">{$media.video[i].description}</td>
			<td>
				{$media.video[i].original_name}<br />
				<span class="taxon-media-secondary-info">({$media.video[i].mime_type}; {$media.video[i].file_size} {t}kb{/t})</span>
				<script type="text/javascript">
					taxonMediaAddId({$media.video[i].id});
				</script>
			</td>
			<td
				onclick="taxonMediaDelete({$media.video[i].id},'video','{$media.video[i].original_name}');"
				class="taxon-media-cell-delete">
				<img src="{$baseUrl}admin/media/system/icons/cross.png" />
			</td>
		</tr>
	{/section}
	</table>
</div>

<br />

<div>
	<a name="sound"></a>
	<span class="taxon-media-classheader">{t}Sound{/t}</span><br />
	<table class="taxon-media-table">
		<tr>
			<th class="taxon-media-colheader-file">{t}Media{/t}</th>
			<th class="taxon-media-colheader-name">{t}Description (click to change){/t}</th>
			<th class="taxon-media-colheader-type">{t}Name, type &amp; size{/t}</th>
			<th></th>
		</tr>
		{section name=i loop=$media.sound}
		<tr id="media-row-{$media.sound[i].id}" class="tr-highlight">
			<td>
				<object type="application/x-shockwave-flash" data="../../tools/player_mp3.swf" width="130" height="20">
					<param name="movie" value="player_mp3.swf" />
					<param name="FlashVars" value="mp3={$session.project.urls.project_media}{$media.sound[i].file_name}" />
				</object>
			</td>
			<td id="media-{$media.sound[i].id}" onclick="taxonMediaDescriptionEdit(this);">
				{$media.sound[i].description}
			</td>
			<td>
				{$media.sound[i].original_name}<br />
				<span class="taxon-media-secondary-info">({$media.sound[i].mime_type}; {$media.sound[i].file_size} {t}kb{/t})</span>
				<script type="text/javascript">
					taxonMediaAddId({$media.sound[i].id});
				</script>
			</td>
			<td onclick="taxonMediaDelete({$media.sound[i].id},'sound file','{$media.sound[i].original_name}');" class="taxon-media-cell-delete">
				<img src="{$baseUrl}admin/media/system/icons/cross.png" />
			</td>
		{/section}
		</tr>
	</table>
</div>

</div>
{/if}

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{section name=i loop=$languages}
	taxonAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}

	taxonActiveLanguage = {$defaultLanguage};
	taxonDrawTaxonLanguages('taxonMediaChangeLanguage');

	allSetHeartbeatFreq({$heartbeatFrequency});
	taxonSetHeartbeat('{$session.user.id}','{$session.system.active_page.appName}','{$session.system.active_page.controllerBaseName}','{$session.system.active_page.viewName}');

{literal}	
});
</script>
{/literal}

{include file="../shared/admin-footer.tpl"}