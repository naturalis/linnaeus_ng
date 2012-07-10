<div id="page-main">
	<div id="term">{$term.term}</div>
	<div id="defintion">{$term.definition}</div>
	{if $term.synonyms}
	
	<div id="synonyms">
		<div id="synonyms-title">{t}Synonyms{/t}</div>
		{foreach from=$term.synonyms key=k item=v}
			<div class="synonym">{$v.synonym}{if $v.language} ({$v.language}){/if}</div>
		{/foreach}
	</div>
	{/if}
	
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
		{if $v.caption}{$v.caption}{elseif $v.fullname}{$v.fullname}{else}{$v.file_name}{/if}
	{/foreach}
	</div>
	{/if}
</div>
