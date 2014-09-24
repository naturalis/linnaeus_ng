{include file="../shared/_header-titles.tpl"}

<div id="categories">
<ul>
	{foreach from=$categories key=k item=v}
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
	{foreach from=$content.classification key=k item=v name=classification}
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
	{foreach from=$content.taxonlist key=k item=v name=list}
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
	{foreach from=$content key=k item=v}
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
	{foreach from=$content.synonyms key=k item=v}
		<p>{if $session.app.user.species.type=='lower'}<i>{/if}{$v.synonym}{if $session.app.user.species.type=='lower'}</i>{/if} {$v.author}</p>
	{/foreach}
	</table>
</div>
{/if}
{if $content.common}
<div id="common">
	<div class="title">{t}Common names{/t}</div>
	{foreach from=$content.common key=k item=v}
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
		{foreach from=$content key=k item=v}

			{assign var=mediaCat value=$v.category}
			{if $requestData.disp==$v.id}
				{assign var=dispUrl value=$smarty.capture.fullImgUrl}
				{assign var=dispName value=$v.original_name}
			{/if}

			<div class="media-cell media-type-{$v.category}" id="media-cell-{$k}">
				<a 
					rel   = "prettyPhoto[gallery]"
					class = "image-wrap "
					title = "{$v.file_name}"
					href  = "{$projectUrls.uploadedMedia}{$v.file_name}"
					alt   = "{$v.description}"
					>

					{if $v.category=='image'}
						{capture name="fullImgUrl"}{$projectUrls.uploadedMedia}{$v.file_name}{/capture}
						<div>
							{if $v.thumb_name != ''}
								<img
									id    = "media-{$k}"
									alt   = "{$v.description}"
									title = "{if $v.original_name!=''}{$v.original_name}{elseif $v.file_name!=''}{$v.file_name}{/if}"
									src   = "{$projectUrls.uploadedMediaThumbs}{$v.thumb_name}"
									class = "image-thumb" />
							{else}
								<img
									id    = "media-{$k}"
									alt   = "{$v.description}"
									title = "{if $v.original_name!=''}{$v.original_name}{elseif $v.file_name!=''}{$v.file_name}{/if}"
									src   = "{$projectUrls.uploadedMedia}{$v.file_name}"
									class = "image-full" />
							{/if}
						</div>
					{elseif $v.category=='video'}
							<img
								id="media-{$k}"
								alt="{$v.description}"
								title="{if $v.original_name!=''}{$v.original_name}{elseif $v.file_name!=''}{$v.file_name}{/if}"
								src="{$projectUrls.systemMedia}video.png"
								onclick="showMedia('{$projectUrls.uploadedMedia}{$v.file_name}','{$v.original_name}');"
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

	{if $isTaxonStartPage && $overviewImage}
		<div id="overview-image" style="background: url('{$projectUrls.uploadedMedia}{$overviewImage.image}');"></div>
	{/if}

{$content}

</div>
{/if}

{if $taxon.id==null}{t}No or illegal taxon ID specified.{/t}{/if}


</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

{if $dispUrl && $dispName}
	showMedia('{$dispUrl}','{$dispName}');
{/if}

	allLookupSetListMax(0);
	allLookupAlwaysFetch=true;

{literal}
	/* $(".group1").colorbox({rel:'group1'}); */

	// $('[id^=media-]').each(function(e){
	// 	$('#caption-'+$(this).attr('id').replace(/media-/,'')).html($(this).attr('alt'));
	// });

});
</script>
{/literal}
