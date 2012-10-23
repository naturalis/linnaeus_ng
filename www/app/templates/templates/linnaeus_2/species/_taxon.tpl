{include file="../shared/_header-titles.tpl"}

<div id="categories">
<ul>
	{foreach from=$categories key=k item=v}
		<li id="ctb-{$v.id}">
		{if $useJavascriptLinks}	
		    <a href="javascript:goTaxon({$taxon.id},\'{$v.id}\');" class="{$v.className}">{$v.title}</a>
		{else}
			<a {if $v.is_empty==0}href="../species/taxon.php?id={$taxon.id}&cat={$v.id}"{/if} class="{$v.className}">{$v.title}</a>	
		{/if}
		</li>
		{if $activeCategory==$v.id && $k==0}{assign var=isTaxonStartPage value=true}{/if}
	{/foreach}
</ul>
</div>

{include file="../shared/_search-main.tpl"}

<div id="page-main">
{if $activeCategory=='classification'}
<div id="classification">
	{foreach from=$content key=k item=v name=classification}
	{if $v.do_display}	
		{if $useJavascriptLinks}			
		<p {if $smarty.foreach.classification.last || $v.is_empty==1}class="current-taxon"{else}class="a" onclick="{if $v.lower_taxon==1}goTaxon{else}goHigherTaxon{/if}({$v.id})"{/if}>{$v.label}</p>
		{else}
		<p>
			{if $smarty.foreach.classification.last || $v.is_empty==1}
				{$v.label}
			{else}
				<a href="../{if $v.lower_taxon==1}species{else}highertaxa{/if}/taxon.php?id={$v.id}">{$v.label}</a>
			{/if}
		</p>
		{/if}		
		
	
	{/if}
	{/foreach}
	
</div>
{elseif $activeCategory=='literature'}
{if $contentCount.literature>0}
<div id="literature">
	{foreach from=$content key=k item=v}
	<div class="text">{$v.text}</div>
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
		<tr class="highlight">
			<td>{$v.synonym}</td>
			<td>{if $v.reference}
			{if $useJavascriptLinks}
			<span onclick="goLiterature({$v.reference.id});" class="a">{$v.reference.author_full}</span>
			{else}
			<a href="../literature/reference.php?id={$v.reference.id}">{$v.reference.author_full}</a>
			{/if}
			{/if}</td>
		</tr>
		{* $v.remark *}
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
<table id="media-grid">
	{assign var=mediaCat value=false}
	{foreach from=$content key=k item=v}
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
			<div>
			{if $v.thumb_name != ''}
				<img
					id="media-{$k}"
					alt="{$v.description}" 
					title="{if $v.original_name!=''}{$v.original_name}{elseif $v.file_name!=''}{$v.file_name}{/if}" 
					src="{$session.app.project.urls.uploadedMediaThumbs}{$v.thumb_name}"
					class="image-thumb" />
			{else}
				<img
					id="media-{$k}"
					alt="{$v.description}" 
					title="{if $v.original_name!=''}{$v.original_name}{elseif $v.file_name!=''}{$v.file_name}{/if}" 
					src="{$session.app.project.urls.uploadedMedia}{$v.file_name}"
					class="image-full" />
			{/if}
			</div>
		{elseif $v.category=='video'}
				<img 
					id="media-{$k}"
					alt="{$v.description}"
					title="{if $v.original_name!=''}{$v.original_name}{elseif $v.file_name!=''}{$v.file_name}{/if}" 
					src="{$session.app.project.urls.systemMedia}video.png" 
					onclick="showMedia('{$session.app.project.urls.uploadedMedia}{$v.file_name}','{$v.original_name}');" 
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

	{math assign=rest equation="(x%$widthInCells)+1" x=$k}
	{section name=bar start=$rest loop=$widthInCells}
	  <td></td>
	{/section}

	</tr>
	<tr>
	{math equation="x-(x%y)" x=$k y=$widthInCells assign=z}
	
	{section name=foo start=$z loop=$k+1}
	  <td id="caption-{$smarty.section.foo.index}" class="caption"></td>
	{/section}
	{math assign=rest equation="(x%$widthInCells)" x=$smarty.section.foo.index}
	{if $rest > 0}
	{section name=bar start=$rest loop=$widthInCells}
	  <td class=></td>
	{/section}
	{/if}
	</tr>

				
</table>
</div>
{/if}
{else}
<div id="content">
{if $isTaxonStartPage && $overviewImage}
<div id="overview-image" style="background: url('{$session.app.project.urls.uploadedMedia}{$overviewImage}');"></div>
{/if}
{$content}
</div>
{/if}

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

{if $dispUrl && $dispName}
	showMedia('{$dispUrl}','{$dispName}'); 
{/if}

{literal}
	/* $(".group1").colorbox({rel:'group1'}); */

	$('[id^=media-]').each(function(e){
		$('#caption-'+$(this).attr('id').replace(/media-/,'')).html($(this).attr('alt'));
	});
	
});
</script>
{/literal}
