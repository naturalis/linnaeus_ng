{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

{if $id}
<div id="page-main">

<span style="float:right">
	<span id="message-container" style="margin-right:0px">&nbsp;</span>
</span>

<p>
	<input type="hidden" name="taxon_id" id="taxon_id" value="{$id}" />
	<input type="hidden" name="action" id="action" value="" />
	<input type="button" value="{t}upload media{/t}" onclick="window.open('media_upload.php?id={$id}','_top')" />&nbsp;
	<input type="button" value="{t}main page{/t}" onclick="window.open('taxon.php?id={$taxon.id}','_top')" />
</p>
<div>
<a name="image"></a>
<fieldset style="width:735px;">
<legend id="key-step-choices">{t}Images{/t}</legend>
	<table class="taxon-media-table">
		<tr>
			<td></td>
			<td><div id="taxon-language-other-language-1"></div></td>
			<td>{t}Overview image{/t}</td>
		</tr>
		{section name=i loop=$media.image}
		<tr id="media-row-{$media.image[i].id}" class="tr-highlight" style="vertical-align:top">
			<td style="width:150px;padding-right:10px">
				{if $media.image[i].thumb_name != ''}
					{capture name="src"}{$session.admin.project.urls.project_thumbs}{$media.image[i].thumb_name}{/capture} 
				{else}
					{capture name="src"}{$session.admin.project.urls.project_media}{$media.image[i].file_name}{/capture} 
				{/if}
				<a class="group1" title="{$media.image[i].original_name}" href="{$session.admin.project.urls.project_media}{$media.image[i].file_name}">
					<img src="{$smarty.capture.src}" style="width:150px;cursor:pointer"/>
				</a>
				<p>
				{$media.image[i].original_name}<br />
				<span class="taxon-media-secondary-info">({$media.image[i].mime_type}; {$media.image[i].hr_file_size} {t}kb{/t})</span>
				</p>
				<p>
				<input type="button" value="{t}delete this image{/t}" onclick="taxonMediaDelete({$media.image[i].id},'image','{$media.image[i].original_name}');" />
				</p>
				<script type="text/javascript">
					taxonMediaFileStore([
						'image',
						'{$media.image[i].id}',
						'{$session.admin.project.urls.project_media}{$media.image[i].file_name}',
						'{$media.image[i].original_name}',
						{$media.image[i].dimensions[1]}
					]);
				</script>
			</td>
			<td style="padding-right:10px">
				<textarea id="media-{$media.image[i].id}" style="width:400px;height:100px">{$media.image[i].description}</textarea><br />
				<input type="button" value="{t}save description{/t}" onclick="taxonMediaSaveDesc('media-{$media.image[i].id}','{$media.image[i].id}')" />
			</td>
			<td>
				<input type="checkbox" id="overview-{$media.image[i].id}"{if $media.image[i].overview_image=='1'} checked="checked"{/if} onclick="taxonChangeOverviewPicture(this)" />
			</td>
			<td>
			{if $smarty.section.i.index>0}
				<span
					class="a updownarrows"
					title="{t}move image upward{/t}"
					onclick="taxonChangeMediaOrder('{$id}','{$media.image[i].id}','up')">
					&uarr;
				</span>
			{/if}
			</td>
			<td>
			{if $smarty.section.i.index<$media.image|@count-1}
				<span
					class="a updownarrows"
					title="{t}move image downward{/t}"
					onclick="taxonChangeMediaOrder('{$id}','{$media.image[i].id}','down')">
					&darr;
				</span>
			{/if}
			</td>
		</tr>
		<tr>
			<td colspan="2" style="height:20px;"></td>
		</tr>
	{/section}
	</table>
	</fieldset>
</div>

<br />

<div>
	<a name="video"></a>
<fieldset style="width:735px;">
<legend id="key-step-choices">{t}Videos{/t}</legend>
	<table class="taxon-media-table">
		<tr>
			<td></td>
			<td><div id="taxon-language-other-language-2"></div></td>
		</tr>
		{section name=i loop=$media.video}
		<tr id="media-row-{$media.video[i].id}" class="tr-highlight" style="vertical-align:top">
			<td style="cursor:pointer;width:260px;" onclick="window.open('{$session.admin.project.urls.project_media}{$media.video[i].file_name}','_video');">
				<img 
					src="{$baseUrl}admin/media/system/icons/video.jpg" 
				/>
				<p>
				{$media.video[i].original_name}<br />
				<span class="taxon-media-secondary-info">({$media.video[i].mime_type}; {$media.video[i].file_size} {t}kb{/t})</span>
				</p>
				<p>
				<input type="button" value="{t}delete this video{/t}" onclick="taxonMediaDelete({$media.video[i].id},'video','{$media.video[i].original_name}');" />
				</p>
				<!-- script type="text/javascript">
					taxonMediaFileStore([
						'video',
						'{$media.video[i].id}',
						'{$session.admin.project.urls.project_media}{$media.video[i].file_name}',
						'{$media.video[i].original_name}'
					]);
				</script -->
			</td>
			<td>
				<textarea id="media-{$media.video[i].id}" style="width:450px;height:100px">{$media.video[i].description}</textarea><br />
				<input type="button" value="{t}save description{/t}" onclick="taxonMediaSaveDesc('media-{$media.video[i].id}','{$media.video[i].id}')" />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="height:20px;"></td>
		</tr>
	{/section}
	</table>
</fieldset>
</div>

<br />


<div>
	<a name="sound"></a>
<fieldset style="width:735px;">
<legend id="key-step-choices">{t}Sound{/t}</legend>
	<table class="taxon-media-table">
		<tr>
			<td></td>
			<td><div id="taxon-language-other-language-2"></div></td>
		</tr>
		{section name=i loop=$media.sound}
		<tr id="media-row-{$media.video[i].id}" class="tr-highlight" style="vertical-align:top">
			<td style="width:260px;" >
				<object type="application/x-shockwave-flash" data="{$soundPlayerPath}{$soundPlayerName}" width="130" height="20">
					<param name="movie" value="{$soundPlayerName}" />
					<param name="FlashVars" value="mp3={$session.admin.project.urls.project_media}{$media.sound[i].file_name}" />
				</object>
				<p>
				{$media.sound[i].original_name}<br />
				<span class="taxon-media-secondary-info">({$media.sound[i].mime_type}; {$media.sound[i].file_size} {t}kb{/t})</span>
				</p>
				<p>
				<input type="button" value="{t}delete this sound file{/t}" onclick="taxonMediaDelete({$media.sound[i].id},'sound file','{$media.sound[i].original_name}');" />
				</p>
				<!-- script type="text/javascript">
					taxonMediaFileStore([
						'sound',
						'{$media.sound[i].id}',
						'{$session.admin.project.urls.project_media}{$media.sound[i].file_name}',
						'{$media.sound[i].original_name}'
					]);
				</script -->
			</td>
			<td>
				<textarea id="media-{$media.sound[i].id}" style="width:450px;height:100px">{$media.sound[i].description}</textarea><br />
				<input type="button" value="{t}save description{/t}" onclick="taxonMediaSaveDesc('media-{$media.sound[i].id}','{$media.sound[i].id}')" />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="height:20px;"></td>
		</tr>
	{/section}
	</table>
</fieldset>
</div>
{/if}

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	allShowLoadingDiv();
{section name=i loop=$languages}
	allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}

	allActiveLanguage = {$defaultLanguage};
	taxonDrawTaxonLanguages('taxonMediaChangeLanguage',true);

	allSetHeartbeatFreq({$heartbeatFrequency});
	taxonSetHeartbeat(
		'{$session.admin.user.id}',
		'{$session.admin.system.active_page.appName}',
		'{$session.admin.system.active_page.controllerBaseName}',
		'{$session.admin.system.active_page.viewName}',
		'{$taxon.id}'
	);
	allHideLoadingDiv();
	allLookupNavigateOverrideUrl('media.php?id=%s');
{literal}	
	$(".group1").colorbox({rel:'group1'});	
});
</script>
{/literal}

{include file="../shared/admin-footer.tpl"}