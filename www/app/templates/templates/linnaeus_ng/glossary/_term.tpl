<div id="page-main">
	<div class="glossary-filter">
		{include file="_alphabet.tpl"}
		<div id="definition">
			{$term.definition}
			{if $term.synonyms}
		 	<p id="synonyms">
		 		<span id="synonyms-title">
		 			{if $term.synonyms|@count > 1}
		 				{t}Alternative forms{/t}
	 				{else}
	 					{t}Alternative form{/t}
					{/if} 
					{t}for{/t} {$term.term}
				</span>:
				{foreach from=$term.synonyms key=k item=v name=synonyms}{$v.synonym}{if $v.language && $v.language_id!=$currentLanguageId} ({$v.language}){/if}{if !$smarty.foreach.synonyms.last}, {/if}{/foreach}.
		     </p>
		     {/if}
		</div>
		{if $term.media}
			<div class="media-grid__container">
			{assign var=widthInCells value=2}
			<div id="media-grid">
				{assign var=mediaCat value=false}
				{foreach $term.media v k}
				{if $v.rs_id == ''}
					{capture name="fullImgUrl"}{$projectUrls.uploadedMedia}{$v.file_name}{/capture}
				{else}
					{capture name="fullImgUrl"}{$v.full_path}{/capture}
				{/if}
					{assign var=mediaCat value=$v.category}
				<div class="media-cell media-type-{$v.category}" id="media-cell-{$k}"
				{if $v.rs_id != '' && $v.category == 'video' && $v.width != '' && $v.height != ''} style="width: {$v.width}px; height: {$v.height}px;"{/if}
				>
					{if $v.rs_id == ''}
						<a
						class="image-wrap fancy-box"
						title="{$v.file_name}"
						href="{$smarty.capture.fullImgUrl}"
						alt="{$v.description}"
						>
						{if $v.category=='image'}
							<div>
								<img
									id= "media-{$k}"
									alt="{$v.description}"
									title="{if $v.original_name!=''}{$v.original_name}{elseif $v.file_name!=''}{$v.file_name}{/if}"
									src="{$smarty.capture.fullImgUrl}"
									class="image-full" />
							</div>
						
						{elseif $v.category=='video'}
							<img
								id="media-{$k}"
								alt="{$v.description}"
								title="{if $v.original_name!=''}{$v.original_name}{elseif $v.file_name!=''}{$v.file_name}{/if}"
								src="{$projectUrls.systemMedia}video.png"
								onclick="showMedia('{$smarty.capture.fullImgUrl}','{$v.original_name}');"
								class="media-video-icon" />
						
						{elseif $v.category=='audio'}
							<object
								id="media-{$k}"
								alt="{$v.description}"
								title="{if $v.original_name!=''}{$v.original_name}{elseif $v.file_name!=''}{$v.file_name}{/if}"
								type="application/x-shockwave-flash"
								data="{$soundPlayerPath}{$soundPlayerName}"
								width="130"
								height="20">
								<param name="movie" value="{$soundPlayerName}" />
								<param name="FlashVars" value="mp3={$projectUrls.uploadedMedia}{$v.file_name}" />
							</object>
						{/if}
						</a>
					{else}
						{if $v.category == 'image'}
							<a href="{$smarty.capture.fullImgUrl}" class="fancy-box" title="{$v.file_name}">
								<img src="{$smarty.capture.fullImgUrl}" alt="{$v.original_name}" id="media-{$k}" class="image-full" />
							</a>
							{$name}
						{else if $v.category == 'audio' or $v.category == 'video'}
							<{$v.category} src="{$smarty.capture.fullImgUrl}" alt="{$name}" id="media-{$k}" controls />
								<a href="{$smarty.capture.fullImgUrl}">Play {$v.original_name}</a>
							</{$v.category}><br>
							{$name}
						{else}
							<a href="{$smarty.capture.fullImgUrl}">
							<img src="{$v.rs_thumb_medium}" alt="{$v.original_name}" /><br>
							{$name}
							</a>
						{/if}
					{/if}
					<div id="caption-{$k}" class="media-caption">
						<p >{$v.description}</p>
					</div>
				</div>
				{/foreach}
			</div>
		</div>
		{/if}
	</div>
</div>

<script type="text/JavaScript">
$(document).ready(function()
{
{if $dispUrl && $dispName}
	showMedia('{$dispUrl}','{$dispName}');
{/if}
	$('[id^=media-]').each(function(e)
	{
		$('#caption-'+$(this).attr('id').replace(/media-/,'')).html($(this).attr('alt'));
	});
});
</script>
