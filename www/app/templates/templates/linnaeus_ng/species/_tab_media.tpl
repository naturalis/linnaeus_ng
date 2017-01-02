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
					IF1
					<a
					rel="prettyPhoto[gallery]"
					class="image-wrap "
					title="{$v.file_name}"
					href="{$smarty.capture.fullImgUrl}"
					alt="{$v.description}">
					{if $v.category=='image'}
						IF2
						<div>
							<img
								id    = "media-{$k}"
								alt   = "{$v.description}"
								title = "{if $v.original_name!=''}{$v.original_name}{elseif $v.file_name!=''}{$v.file_name}{/if}"
								src   = "{$smarty.capture.fullImgUrl}"
								class = "image-full" />
						</div>
					{elseif $v.category=='video'}
						ELSEIF2
						<img
							id="media-{$k}"
							alt="{$v.description}"
							title="{if $v.original_name!=''}{$v.original_name}{elseif $v.file_name!=''}{$v.file_name}{/if}"
							src="{$projectUrls.systemMedia}video.png"
							onclick="showMedia('{$smarty.capture.fullImgUrl}','{$v.original_name}');"
							class="media-video-icon" />
					{elseif $v.category=='audio'}
						ELSEIF2.1
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
					ELSE1
					{if $v.category == 'image'}
						<a href="{$smarty.capture.fullImgUrl}" title="{$v.file_name}" class="fancy-box" alt="{$v.description}">
						<img src="{$smarty.capture.fullImgUrl}" alt="{$v.description}" id="media-{$k}" class="image-full" />
						</a><br/>
						{$name}
					{else if $v.category == 'audio' or $v.category == 'video'}
						ELSEIF3
						<a href="#inline-media-{$k}" class="fancy-box fancy-box-video"><i class="ion-ios-videocam"></i></a>
						<div id="inline-media-{$k}" style="display: none;">
							<{$v.category} src="{$smarty.capture.fullImgUrl}" alt="{$v.description}" id="media-{$k}" controls
								{if $v.width != '' && $v.height != ''}style="width: {$v.width}px; height: {$v.height}px;"{/if}/>
								<a href="{$smarty.capture.fullImgUrl}">Play {$v.original_name}</a>
							</{$v.category}><br>
							{$name}
						</div>
					{else}
						ELSEIF4
						<a href="{$smarty.capture.fullImgUrl}">
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
