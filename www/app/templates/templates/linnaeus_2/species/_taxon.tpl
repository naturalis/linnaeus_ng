{include file="../shared/_header-titles.tpl"}

<div id="categories">
<ul>
	{foreach from=$categories key=k item=v}
		<li id="ctb-{$v.id}">
			{$v.title}
		</li>
		{if $activeCategory==$v.id && $k==0}{assign var=isTaxonStartPage value=true}{/if}
	{/foreach}
</ul>
</div>

{include file="../shared/_search-main.tpl"}

<div id="page-main">
{if $activeCategory=='classification'}
<div id="classification">
	<table>
	{foreach from=$content key=k item=v name=classification}
	{if $v.do_display}
		<tr>
			{if $useJavascriptLinks}			
			<td {if $smarty.foreach.classification.index==$content|@count-1}class="current-taxon"{else}class="a" onclick="{if $v.lower_taxon==1}goTaxon{else}goHigherTaxon{/if}({$v.id})"{/if}>{$v.taxon}</td>
			{else}
			<td {if $smarty.foreach.classification.index==$content|@count-1}class="current-taxon"{/if}>
				{if $v.lower_taxon==1}
				<a href="../species/taxon.php?id={$v.id}">{$v.taxon}</a>
				{else}
				<a href="../highertaxa/taxon.php?id={$v.id}">{$v.taxon}</a>
				{/if}
			</td>
			{/if}		
			<td>({$v.rank})</td>
		</tr>
	{/if}
	{/foreach}
	</table>
</div>
{elseif $activeCategory=='literature' && $contentCount.literature>0}
<div id="literature">
	{foreach from=$content key=k item=v}
	<div class="author">
		<span class="name">
			{$v.author_full}
		</span>
		<span class="year">{$v.year}</span>
	</div>
	<div class="text">{$v.text}</div>
	{/foreach}
</div>
{elseif $activeCategory=='names' && $contentCount.names>0}
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
	<div class="title">{t}Common Names{/t}</div>
	<table>
	<thead>
		<tr class="highlight">
			<th>{t}Common name{/t}</th>
			<th>{t}Transliteration{/t}</th>
			<th>{t}Language{/t}</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$content.common key=k item=v}
		<tr class="highlight">
			<td>{$v.commonname}</td>
			<td>{$v.transliteration}</td>
			<td>{$v.language_name}</td>
		</tr>
	{/foreach}
	</tbody>
	</table>
</div>
{/if}
{elseif $activeCategory=='media' && $contentCount.media>0}
<div id="media">
	<table>
	{assign var=mediaCat value=false}
	{foreach from=$content key=k item=v}
	{if $mediaCat!=$v.category}
	{if $k!=0}
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	{/if}
	<tr>
		<td colspan="2" class="media-cat-header">{$v.category_label}</td>
	</tr>
	{/if}
	<tr>
		<td class="media-image-cell">
	{if $v.category=='image'}
		{capture name="fullImgUrl"}{$session.app.project.urls.uploadedMedia}{$v.file_name}{/capture}
		<a class="group1" title="{$v.original_name}" href="{$session.app.project.urls.uploadedMedia}{$v.file_name}">
		{if $v.thumb_name != ''}
			<img
				alt="{$v.original_name}" 
				src="{$session.app.project.urls.uploadedMediaThumbs}{$v.thumb_name}"
				class="media-image" />
		{else}
			<img
				alt="{$v.original_name}" 
				src="{$session.app.project.urls.uploadedMedia}{$v.file_name}"
				class="media-image" />
		{/if}
		</a>
	{elseif $v.category=='video'}
			<img 
				alt="{$v.original_name}" 
				src=".{$session.app.project.urls.systemMedia}video.jpg" 
				onclick="showMedia('{$session.app.project.urls.uploadedMedia}{$v.file_name}','{$v.original_name}');" 
				class="media-video-icon" />
	{elseif $v.category=='audio'}
			<object type="application/x-shockwave-flash" data="{$soundPlayerPath}{$soundPlayerName}" width="130" height="20">
				<param name="movie" value="{$soundPlayerName}" />
				<param name="FlashVars" value="mp3={$session.app.project.urls.uploadedMedia}{$v.file_name}" />
			</object>
	{/if}
			</td>		
		<td class="media-description-cell">{$v.description}</td>
	</tr>
	{assign var=mediaCat value=$v.category}
	{if $requestData.disp==$v.id}
		{assign var=dispUrl value=$smarty.capture.fullImgUrl}
		{assign var=dispName value=$v.original_name}
	{/if}
	{/foreach}
	</table>
</div>
{else}
<div id="content">
{if $isTaxonStartPage && $overviewImage}
<img alt="{$overviewImage}" id="overview-image" src="{$session.app.project.urls.uploadedMedia}{$overviewImage}"/>
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

{foreach from=$categories key=k item=v}
{if $useJavascriptLinks}	
    $('#ctb-{$v.id}').html('<a href="javascript:goTaxon({$taxon.id},\'{$v.id}\');" class="{$v.className}">'+$('#ctb-{$v.id}').html()+'</a>');
{else}
	$('#ctb-{$v.id}').html('<a href="../species/taxon.php?id={$taxon.id}&cat={$v.id}" class="{$v.className}">'+$('#ctb-{$v.id}').html()+'</a>');
{/if}
{/foreach}

{literal}
	$(".group1").colorbox({rel:'group1'});	
});
</script>
{/literal}
