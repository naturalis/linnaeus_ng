{include file="_search-main-no-tabs.tpl"}

<div id="page-main">
	<div id="definition">{$term.definition}</div>
	
	{if $term.media}
	<div id="media">
		<div id="media-title">{t}Images{/t}</div>
	{foreach from=$term.media key=k item=v}
		<div class="image-cell">
		{if $v.thumb_name}
			<img
				alt="{$v.original_name}"
				class="image-thumb"
				onclick="showMedia('{$session.app.project.urls.uploadedMedia}{$v.file_name|escape:'url'}','{$v.original_name}');" 
				src="{$session.app.project.urls.uploadedMediaThumbs}{$v.thumb_name|escape:'url'}" />
		{else}
			<img
				alt="{$v.original_name}"
				class="image-full"
				onclick="showMedia('{$session.app.project.urls.uploadedMedia}{$v.file_name|escape:'url'}','{$v.original_name}');" 
				src="{$session.app.project.urls.uploadedMedia}{$v.file_name|escape:'url'}" />
		{/if}
		</div>
	{/foreach}
	</div>
	{/if}
</div>
