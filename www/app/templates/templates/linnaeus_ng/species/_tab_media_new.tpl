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
				
					{if $v.category == 'image'}
						<a href="{$smarty.capture.fullImgUrl}" title="{$v.original_name}" data-fancybox="gallery">
						<img src="{$smarty.capture.fullImgUrl}" alt="{$v.original_name}" id="media-{$k}" class="image-full" />
						</a>
						
					{else if $v.category == 'audio'}
                        <div style="display:none;" id="hidden-media-{$k}">
                            <audio controls>
                                <source src="{$smarty.capture.fullImgUrl}" type="type="audio/mpeg">
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
								 <div class="video-overlay" style="background: url('{$projectUrls.systemMedia}video-overlay.png') center center no-repeat; background-size: 30% auto;"></div>
								 <video><source src="{$smarty.capture.fullImgUrl}" type="video/mp4"></video>
							</a>
						</div>


					{else}
						<a href="{$smarty.capture.fullImgUrl}" title="{$v.description}">
							<img src="{$v.rs_thumb_medium}" alt="{$v.description}" /><br>
						</a>
					{/if}

				<div id="caption-{$k}" class="media-caption">
					<p>{$v.description}</p>
				</div>
			</div>
		{/foreach}

	</div>
</div>
