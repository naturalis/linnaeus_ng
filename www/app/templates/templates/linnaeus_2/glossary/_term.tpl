{include file="_search-main-no-tabs.tpl"}

<div id="page-main">
	<div id="definition">
		{$term.definition}
		{if $term.synonyms}
	 	<p id="synonyms"><span id="synonyms-title">{if $term.synonyms|@count > 1}{t}Synonyms{/t}{else}{t}Synonym{/t}{/if} {t}for{/t} {$term.term}</span>: 
			{foreach from=$term.synonyms key=k item=v name=synonyms}{$v.synonym}{if $v.language} ({$v.language}){/if}{if !$smarty.foreach.synonyms.last}, {/if}{/foreach}.
	     </p>  
	     {/if}
	</div>

     
	{if $term.media}
	<div id="media">
{assign var=widthInCells value=2}
<table id="media-grid">
	{assign var=mediaCat value=false}
	{foreach from=$term.media key=k item=v}
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
			{if $v.thumb_name != ''}
				<img
					id="media-{$k}"
					alt="{$v.alt}" 
					src="{$session.app.project.urls.uploadedMediaThumbs}{$v.thumb_name}"
					class="image-thumb" />
			{else}
				<img
					id="media-{$k}"
					alt="{$v.alt}" 
					src="{$session.app.project.urls.uploadedMedia}{$v.file_name}"
					class="image-full" />
			{/if}
		{elseif $v.category=='video'}
				<img 
					id="media-{$k}"
					alt="{$v.description}" 
					src="{$session.app.project.urls.systemMedia}video.png" 
					onclick="showMedia('{$session.app.project.urls.uploadedMedia}{$v.file_name}','{$v.original_name}');" 
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
	{section name=bar start=$rest loop=$widthInCells}
	  <td></td>
	{/section}
	</tr>

				
</table>	
	
	
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
