{include file="_search-main-no-tabs.tpl"}

<div id="page-main">
	{include file="_alphabet.tpl"}
	<div id="definition">
		{$term.definition}
		{if $term.synonyms}
	 	<p id="synonyms"><span id="synonyms-title">{if $term.synonyms|@count > 1}{t}Synonyms{/t}{else}{t}Synonym{/t}{/if} {t}for{/t} {$term.term}</span>: 
			{foreach from=$term.synonyms key=k item=v name=synonyms}{$v.synonym}{if $v.language && $v.language_id!=$currentLanguageId} ({$v.language}){/if}{if !$smarty.foreach.synonyms.last}, {/if}{/foreach}.
	     </p>  
	     {/if}
	</div>

     
	{if $term.media}
		<div id="media">
		{assign var=widthInCells value=2}
			<div id="media-grid">
				{assign var=mediaCat value=false}
				{foreach $term.media v k}

					{assign var=mediaCat value=$v.category}
					{if $requestData.disp==$v.id}
						{assign var=dispUrl value=$smarty.capture.fullImgUrl}
						{assign var=dispName value=$v.original_name}
					{/if}
					
					<div class="media-cell media-type-{$v.category}" id="media-cell-{$k}">
						<a 
							rel          = "prettyPhoto[gallery]"
							class        = "image-wrap"
							title        = "{$v.file_name}"
							alt          = "{$v.alt}"
							href         = "{$projectUrls.uploadedMedia}{$v.file_name}"

							>

						{if $v.category=='image'}
							{capture name="fullImgUrl"}{$projectUrls.uploadedMedia}{$v.file_name}{/capture}
                            <img
                                id="media-{$k}"
                                alt="{$v.alt}" 
                                src="{$v.full_path}"
                                class="image-full" />
						{elseif $v.category=='video'}
								<img 
									id="media-{$k}"
									alt="{$v.description}" 
									src="{$projectUrls.systemMedia}video.png" 
									onclick="showMedia('{$v.full_path}','{$v.original_name}');" 
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
									<param name="FlashVars" value="mp3={$v.full_path}" />
								</object>
						{/if}
						</a>

						<div id="caption-{$k}" class="media-caption">
							<p >{$v.description}</p>
						</div>
					</div><!-- /.media-cell -->

				{/foreach}
							
			</div> <!-- /#media-grid -->	
	
	
	</div><!-- /#media -->
	{/if}
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
