<div id="page-main">
	<div id="term">{$term.term}</div>
	<div id="defintion">{$term.definition}</div>
	{if $term.synonyms}
	
	<div id="synonyms">
		<div id="synonyms-title">{t}Synonyms{/t}</div>
		{foreach from=$term.synonyms key=k item=v}
			<div class="synonym">{$v.synonym} ({$v.language})</div>
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
				class="image-thumb"
				onclick="showMedia('{$session.app.project.urls.project_media}{$v.file_name|escape:'url'}','{$v.original_name}');" 
				src="{$session.app.project.urls.project_thumbs}{$v.thumb_name|escape:'url'}" />
		{else}
			<img
				class="image-full"
				onclick="showMedia('{$session.app.project.urls.project_media}{$v.file_name|escape:'url'}','{$v.original_name}');" 
				src="{$session.app.project.urls.project_media}{$v.file_name|escape:'url'}" />
		{/if}
		</div>
	{/foreach}
	</div>
	{/if}
</div>
