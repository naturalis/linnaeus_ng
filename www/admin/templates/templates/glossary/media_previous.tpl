{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

{if $id}
<div id="page-main">
<p>
<form id="theForm" method="post">
	<input type="hidden" name="gloss_id" id="gloss_id" value="{$id}" />
	<input type="hidden" name="action" id="action" value="" />
	<input type="button" value="{t}upload media{/t}" onclick="window.open('media_upload.php?id={$id}','_top')" />&nbsp;
	<input type="button" value="{t}back{/t}" onclick="window.open('edit.php?id={$id}','_top')" />
</form>
</p>
<div>
<a name="image"></a>
<fieldset style="width:735px;">
<legend id="key-step-choices">{t}Images{/t}</legend>
	<table class="taxon-media-table">
		<tr>
			<td></td>
			<td><div id="taxon-language-other-language-1"></div></td>
			<!-- td>{t}Overview image{/t}</td -->
		</tr>
		{foreach $media.image v k}
		<tr id="media-row-{$v.id}" class="tr-highlight" style="vertical-align:top">
			<td style="width:150px;padding-right:10px">

				{$v.sort_order}

				{if $v.thumb_name != ''}
					{capture name="src"}{$session.admin.project.urls.project_thumbs}{$v.thumb_name}{/capture}
				{else}
					{capture name="src"}{$session.admin.project.urls.project_media}{$v.file_name}{/capture}
				{/if}
				<a rel="prettyPhoto[gallery]" title="{$v.description}" href="{$session.admin.project.urls.project_media}{$v.file_name}">
					<img src="{$smarty.capture.src}" style="width:150px;cursor:pointer"/>
				</a>
				<p>
				{$v.original_name}<br />
				<span class="taxon-media-secondary-info">({$v.mime_type}; {$v.hr_file_size} {t}kb{/t})</span>
				</p>
				<p>
				<input type="button" value="{t}delete this image{/t}" onclick="glossMediaDelete({$v.id},'image','{$v.original_name}');" />
				</p>
				<script type="text/javascript">
					glossMediaFileStore([
						'image',
						'{$v.id}',
						'{$session.admin.project.urls.project_media}{$v.file_name}',
						'{$v.original_name}',
						{$v.dimensions[1]}
					]);
				</script>
			</td>
			<td style="padding-right:10px">
				<textarea id="media-{$v.id}" style="width:400px;height:100px">{$v.description}</textarea><br />
				<input type="button" value="{t}save description{/t}" onclick="glossMediaSaveDesc('media-{$v.id}','{$v.id}')" />
			</td>
		</tr>
		<tr id="media-row-{$v.id}-space">
			<td colspan="2" style="height:20px;"></td>
		</tr>
	{/foreach}
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
		{foreach $media.video v k}
		<tr id="media-row-{$v.id}" class="tr-highlight" style="vertical-align:top">
			<td>
				<a rel="prettyPhoto[gallery]" title="{$v.description}" href="{$session.admin.project.urls.project_media}{$v.file_name}">
				<img
					src="{$baseUrl}admin/media/system/icons/video.jpg"
				/>
				</a>
				<p>
				{$v.original_name}<br />
				<span class="taxon-media-secondary-info">({$v.mime_type}; {$v.file_size} {t}kb{/t})</span>
				</p>
				<p>
				<input type="button" value="{t}delete this video{/t}" onclick="glossMediaDelete({$v.id},'video','{$v.original_name}');" />
				</p>
				<!-- script type="text/javascript">
					glossMediaFileStore([
						'video',
						'{$v.id}',
						'{$session.admin.project.urls.project_media}{$v.file_name}',
						'{$v.original_name}'
					]);
				</script -->
			</td>
			<td>
				<textarea id="media-{$v.id}" style="width:450px;height:100px">{$v.description}</textarea><br />
				<input type="button" value="{t}save description{/t}" onclick="glossMediaSaveDesc('media-{$v.id}','{$v.id}')" />
			</td>
		</tr>
		<tr id="media-row-{$v.id}-space">
			<td colspan="2" style="height:20px;"></td>
		</tr>
	{/foreach}
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
		{foreach $media.sound v k}
		<tr id="media-row-{$v.id}" class="tr-highlight" style="vertical-align:top">
			<td style="width:260px;" >
				<object type="application/x-shockwave-flash" data="{$soundPlayerPath}{$soundPlayerName}" width="130" height="20">
					<param name="movie" value="{$soundPlayerName}" />
					<param name="FlashVars" value="mp3={$session.admin.project.urls.project_media}{$v.file_name}" />
				</object>
				<p>
				{$v.original_name}<br />
				<span class="taxon-media-secondary-info">({$v.mime_type}; {$v.file_size} {t}kb{/t})</span>
				</p>
				<p>
				<input type="button" value="{t}delete this sound file{/t}" onclick="glossMediaDelete({$v.id},'sound file','{$v.original_name}');" />
				</p>
			</td>
			<td>
				<textarea id="media-{$v.id}" style="width:450px;height:100px">{$v.description}</textarea><br />
				<input type="button" value="{t}save description{/t}" onclick="glossMediaSaveDesc('media-{$v.id}','{$v.id}')" />
			</td>
		</tr>
		<tr id="media-row-{$v.id}-space" >
			<td colspan="2" style="height:20px;"></td>
		</tr>
	{/foreach}
	</table>
</fieldset>
</div>
{/if}

<script type="text/JavaScript">
$(document).ready(function()
{
	allShowLoadingDiv();
	{foreach $languages v k}
	allAddLanguage([{$v.language_id},'{$v.language}',{if $v.def_language=='1'}1{else}0{/if}]);
	{/foreach}
	allActiveLanguage = {$defaultLanguage};
	glossDrawLanguages('glossMediaChangeLanguage',true);
	allHideLoadingDiv();
	allLookupNavigateOverrideUrl('media.php?id=%s');
});
</script>

{include file="../shared/admin-footer.tpl"}