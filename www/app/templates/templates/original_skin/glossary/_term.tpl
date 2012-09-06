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
		
		
	<!--	
		
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
	
	-->
	
	{foreach from=$term.media key=k item=v}
		{if $k==0}
			<tr>
		{elseif $k%$widthInCells==0}
			</tr>
			<tr>
			{section name=foo start=0 loop=$widthInCells}
			{math equation="(x + y) - z" x=$k y=$smarty.section.foo.index z=$widthInCells assign=id}
			  <td id="caption-{$id}" class="caption"></td>
			{/section}
			</tr>
			<tr>
		{/if}
		<td class="media-cell">
		<a rel="prettyPhoto[gallery]" class="image-wrap" title="{$v.description}" href="{$session.app.project.urls.uploadedMedia}{$v.file_name}">
		{if $v.category=='image'}
			{capture name="fullImgUrl"}{$session.app.project.urls.uploadedMedia}{$v.file_name}{/capture}
			{if $v.thumb_name != ''}
				<img
					id="media-{$k}"
					alt="{$v.caption}" 
					src="{$session.app.project.urls.uploadedMediaThumbs}{$v.thumb_name}"
					class="image-thumb" />
			{else}
				<img
					id="media-{$k}"
					alt="{$v.caption}" 
					src="{$session.app.project.urls.uploadedMedia}{$v.file_name}"
					class="image-full" />
			{/if}
		{elseif $v.category=='video'}
				<img 
					id="media-{$k}"
					alt="{$v.description}" 
					src="{$session.app.project.urls.systemMedia}video.jpg" 
					onclick="showMedia('{$session.app.project.urls.uploadedMedia}{$v.file_name}','{$v.original_name}');" 
					class="media-video-icon" />
		{elseif $v.category=='audio'}
				<object 
					id="media-{$k}"
					alt="{$v.description}" 
					type="application/x-shockwave-flash" 
					data="{$soundPlayerPath}{$soundPlayerName}" 
					width="130" 
					height="20">
					<param name="movie" value="{$soundPlayerName}" />
					<param name="FlashVars" value="mp3={$session.app.project.urls.uploadedMedia}{$v.file_name}" />
				</object>
		{/if}
		</a>
		</td>
	{assign var=mediaCat value=$v.category}
	{if $requestData.disp==$v.id}
		{assign var=dispUrl value=$smarty.capture.fullImgUrl}
		{assign var=dispName value=$v.original_name}
	{/if}
	{/foreach}	
	
	</div>
	{/if}
</div>
