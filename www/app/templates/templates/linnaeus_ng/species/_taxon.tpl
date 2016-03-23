{include file="../shared/_header-titles.tpl"}

<div id="categories">
<ul>
	{foreach $categories v k}
		<li id="ctb-{$v.id}">
			<a {if $v.is_empty==0}href="../species/taxon.php?id={$taxon.id}&cat={$v.id}"{/if} class="{$v.className}">{$v.title}</a>
		</li>
		{if $activeCategory==$v.id && $k==0}{assign var=isTaxonStartPage value=true}{/if}
	{/foreach}
</ul>
</div>

{include file="../shared/_search-main.tpl"}

<div id="page-main">
{if $activeCategory=='classification'}

<div id="classification">
	<p>
	<b>{t}Classification{/t}</b><br />
	{foreach $content.classification v k classification}
	{if $v.do_display}

		<a href="../{if $v.lower_taxon==1}species{else}highertaxa{/if}/taxon.php?id={$v.id}">{$v.label}</a>

		{* if $smarty.foreach.classification.last || $v.is_empty==1}
			{$v.label}
		{else}
			<a href="../{if $v.lower_taxon==1}species{else}highertaxa{/if}/taxon.php?id={$v.id}">{$v.label}</a>
		{/if *}
		<br />
	{/if}
	{/foreach}
	</p>

	{if $content.taxonlist|@count>0}
	<p>
	<b>{$taxon.label} {t}contains the following taxa{/t}:</b><br/>
	{foreach $content.taxonlist v k list}
		<a href="../{if $v.lower_taxon==1}species{else}highertaxa{/if}/taxon.php?id={$v.id}">{$v.label}</a>
		{if $v.commonname} ({$v.commonname}){/if}
		<br />
	{/foreach}
	</p>
	{/if}


</div>
{elseif $activeCategory=='literature'}
{if $contentCount.literature>0}
<div id="literature">
	{foreach $content v k}
	<b>{$v.author_full}, {$v.year_full}:</b> {$v.text}
	{/foreach}
</div>
{/if}
{elseif $activeCategory=='names'}
{if $contentCount.names>0}
{if $content.synonyms}
<div id="synonyms">
	<div class="title">{t}Synonyms{/t}</div>
	<table>
	{foreach $content.synonyms v k}
		<p>{if $session.app.user.species.type=='lower'}<i>{/if}{$v.synonym}{if $session.app.user.species.type=='lower'}</i>{/if} {$v.author}</p>
	{/foreach}
	</table>
</div>
{/if}
{if $content.common}
<div id="common">
	<div class="title">{t}Common names{/t}</div>
	{foreach $content.common v k}
		<p>{$v.commonname}{if $v.transliteration} ({$v.transliteration}){/if} [{$v.language_name}]</p>
	{/foreach}
</div>
{/if}
{/if}
{elseif $activeCategory=='media'}
{if $contentCount.media>0}
<div id="media">
{assign var=widthInCells value=5}
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
					rel   = "prettyPhoto[gallery]"
					class = "image-wrap "
					title = "{$v.file_name}"
					href  = "{$smarty.capture.fullImgUrl}"
					alt   = "{$v.description}"
					>

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
						<a href="{$smarty.capture.fullImgUrl}" title="{$v.file_name}" rel="prettyPhoto">
						<img src="{$smarty.capture.fullImgUrl}" alt="{$v.original_name}" id="media-{$k}" class="image-full" />
						</a><br/>
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

			</div><!-- /.media-cell -->
		{/foreach}

	</div><!-- /#media-grid -->
</div><!-- /#media -->
{/if}
{else}

<div id="content" class="proze" >

	{if $isTaxonStartPage && $overviewImage.image}
		<div id="overview-image" style="background: url('{$projectUrls.uploadedMedia}{$overviewImage.image}');"></div>
	{else if $isTaxonStartPage && $overview}
		<div id="overview-image" style="background: url('{$overview}');"></div>
	{/if}

{$content}

</div>
{/if}

{if $taxon.id==null}{t}No or illegal taxon ID specified.{/t}{/if}


</div>

<script type="text/JavaScript">
$(document).ready(function()
{
{if $dispUrl && $dispName}
	showMedia('{$dispUrl}','{$dispName}');
{/if}
	allLookupSetListMax(0);
	allLookupAlwaysFetch=true;
});
</script>
