{assign var=widthInCells value=5}
<div class="media-grid__container">
	<div id="media-grid">
		{assign var=mediaCat value=false}
		{foreach $content v k}

			{if $v.rs_id == ''}
				{capture name="fullImgUrl"}{$projectUrls.uploadedMedia}{$v.file_name}{/capture}
			{else}
				{capture name="fullImgUrl"}{$v.full_path}{/capture}
			{/if}

			{assign var=mediaCat value=$v.category}

			{if $requestData.disp==$v.id}
				{assign var=dispUrl value=$smarty.capture.fullImgUrl}
				{assign var=dispName value=$v.original_name}
			{/if}

			<div class="media-cell media-type-{$v.category}" id="media-cell-{$k}">
				{if $v.rs_id == ''}
					<a
					data-fancybox="gallery" 
					class="image-wrap "
					title="{$v.description}"
					href="{$smarty.capture.fullImgUrl}"
					alt="">
					{if $v.category=='image'}
						<div>
							<img
								id    = "media-{$k}"
								alt   = "{$v.description}"
								title = "{if $v.original_name!=''}{$v.original_name}{elseif $v.file_name!=''}{$v.file_name}{/if}"
								src   = "{$smarty.capture.fullImgUrl}"
								class = "image-full" />
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
						<a href="{$smarty.capture.fullImgUrl}" title="{$v.description}" data-fancybox="gallery" alt="">
						<img src="{$smarty.capture.fullImgUrl}" alt="{$v.description}" id="media-{$k}" class="image-full" />
						</a><br/>
						{$name}
					{else if $v.category == 'audio' or $v.category == 'video'}
                        <div style="display:none;" id="hidden-media-{$k}">
                            <audio controls>
                                <source src="{$smarty.capture.fullImgUrl}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio> 
                        </div>
                        
                        <a data-fancybox="gallery" data-src="#hidden-media-{$k}" href="javascript:;" class="{if $v.category=='audio'}ion-volume-medium{else}ion-videocamera{/if} larger-ion-icon">
                            {$name}
                        </a> 
					{else}
						<a href="{$smarty.capture.fullImgUrl}" title="{$v.description}" data-fancybox="gallery">
							<img src="{$v.rs_thumb_medium}" alt="{$v.description}" /><br>
							{$name}
						</a>
					{/if}
				{/if}
				<div id="caption-{$k}" class="media-caption">
					<p>{$v.description}</p>
				</div>
			</div>
		{/foreach}
	</div>
</div>
