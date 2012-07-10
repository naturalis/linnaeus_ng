{include file="_search-main-no-tabs.tpl"}

<div id="page-main">
	<div id="definition">
		{$term.definition}
		{if $term.synonyms}
	 	<p id="synonyms"><span id="synonyms-title">{if $term.synonyms|@count > 1}{t}Synonyms{/t}{else}{t}Synonym{/t}{/if} {t}for{/t} {$term.term}</span>: 
			{foreach from=$term.synonyms key=k item=v name=synonyms}{$v.synonym}{if $v.language} ({$v.language}){/if}{if !$smarty.foreach.synonyms.last}, {/if}{/foreach}.
	     </p>  
	     {/if}
	</div>

     
	{if $term.media}
	<div id="media">
	
	{foreach from=$term.media key=k item=v}
		{if $k>0 && $k%2==0}<div class="clear"></div>{/if}
		<div class="media-cell">
		
		
		
	{if $v.category=='image'}
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
	{elseif $v.category=='video'}
			<img 
				alt="{$v.original_name}" 
				src="{$session.app.project.urls.systemMedia}video.jpg" 
				onclick="showVideo('{$session.app.project.urls.uploadedMedia}{$v.file_name}','{$v.original_name}');" 
				class="media-video-icon" />
	{elseif $v.category=='audio'}
			<object type="application/x-shockwave-flash" data="{$soundPlayerPath}{$soundPlayerName}" width="130" height="20">
				<param name="movie" value="{$soundPlayerName}" />
				<param name="FlashVars" value="mp3={$session.app.project.urls.uploadedMedia}{$v.file_name}" />
			</object>
	{/if}
		
		
		
		
		<div class="caption">{if $v.caption}{$v.caption}{elseif $v.fullname}{$v.fullname}{else}{$v.file_name}{/if}</div>
		</div>
	{/foreach}
	
	
	
	</div>
	{/if}
</div>
