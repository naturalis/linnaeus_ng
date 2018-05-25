<!-- move this to css once accepted as video overlay! -->
<style>
.video-container {
    position: relative;
    width: 100%;
    height: 100%;
    border-radius: 5px;
    background-attachment: scroll;
    overflow: hidden;
}
.video-container video {
    min-width: 100%;
    min-height: 100%;
    position: relative;
    z-index: 1;
}
.video-container .video-overlay {
    height: 100%;
    width: 100%;
    position: absolute;
    top: 0px;
    left: 0px;
    z-index: 2;
    background: url('{$projectUrls.systemMedia}video-overlay.png') center center no-repeat;
    background-size: 30% auto;
    opacity: 0.5;
}
</style>

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
				{capture name="fullImgUrl"}{$v.full_path}{/capture}
				{assign var=mediaCat value=$v.category}
				<div class="media-cell media-type-{$v.category}" id="media-cell-{$k}">	
				
						{if $v.caption != ''}
							{capture name="caption"}{$v.caption}{/capture}
						{else if $v.metadata.title != ''}
							{capture name="caption"}{$v.metadata.title}{/capture}
						{else}
							{capture name="caption"}{$v.original_name}{/capture}
						{/if}
						
						{if $v.category == 'image'}
							<a href="{$smarty.capture.fullImgUrl}" data-fancybox="gallery"  title="{$v.file_name}">
								<img src="{$smarty.capture.fullImgUrl}" alt="{$v.original_name}" id="media-{$k}" class="image-full" />
							</a>
						
						{else if $v.category == 'audio'}
							<div style="display:none;" id="hidden-media-{$k}">
								<audio controls>
									<source src="{$smarty.capture.fullImgUrl}" type="audio/mpeg">
									Your browser does not support the html5 audio element.
								</audio>
							</div>		

							<a data-fancybox="gallery" data-src="#hidden-media-{$k}" href="javascript:;" data-caption="{$name}">
								<audio controls><source src="{$smarty.capture.fullImgUrl}" type="audio/mpeg"></audio>
							</a>
														
						{else if $v.category == 'video'}
							<div style="display:none;" id="hidden-media-{$k}">
								<video controls>
									<source src="{$smarty.capture.fullImgUrl}" type="video/mp4">
									Your browser does not support the html5 video element.
								</video>
							</div>		
							<div class="video-container"> 
							<a data-fancybox="gallery" data-src="#hidden-media-{$k}" href="javascript:;" data-caption="{$name}" class="media-video-icon">
								 <div class="video-overlay"></div>
								 <video><source src="{$smarty.capture.fullImgUrl}" type="video/mp4"></video>
							</a>
							</div>
						
						{else}
							<a href="{$smarty.capture.fullImgUrl}">
							<img src="{$v.rs_thumb_medium}" alt="{$v.original_name} title="{$v.original_name}" /><br>
							</a>
						{/if}
						
					
					<div id="caption-{$k}" class="media-caption">
						<p >{$smarty.capture.caption}</p>
					</div>
				</div>
				{/foreach}
			</div>
		</div>
		{/if}
	</div>
</div>
