{include file="../shared/header.tpl"}

{if $alpha}
<div id="alphabet">
	{foreach from=$alpha key=k item=v}
	{if $letter==$v}
	<span class="letter-active">{$v}</span>
	{else}
	<span class="letter" onclick="goAlpha('{$v}','index.php')">{$v}</span>
	{/if}
	{/foreach}
</div>
{/if}

<div id="page-main">
	<div id="term">{$term.term}</div>
	<div id="defintion">{$term.definition}</div>
	{if $term.synonyms}
	
	<div id="synonyms">
		<div id="synonyms-title">{t}Synonyms{/t}</div>
		{foreach from=$term.synonyms key=k item=v}
			<div class="synonym">{$v.synonym} ({$v.language})</div>
		{/foreach}
	</div>
	{/if}
	
	{if $term.media}
	<div id="media">
		<div id="media-title">{t}Images{/t}</div>
	{foreach from=$term.media key=k item=v}
		<div class="image-cell">
		{if $v.thumb_name}
			<img
				class="image-thumb"
				onclick="showMedia('{$session.project.urls.project_media}{$v.file_name|escape:'url'}','{$v.original_name}');" 
				src="{$session.project.urls.project_thumbs}{$v.thumb_name|escape:'url'}" />
		{else}
			<img
				class="image-full"
				onclick="showMedia('{$session.project.urls.project_media}{$v.file_name|escape:'url'}','{$v.original_name}');" 
				src="{$session.project.urls.project_media}{$v.file_name|escape:'url'}" />
		{/if}
		</div>
	{/foreach}
	</div>
	{/if}

	<div id="navigation">
		{if $adjacentTerms.prev}
		<span onclick="goGlossaryTerm({$adjacentTerms.prev.id})" id="prev">{t}< previous{/t}</span>
		{/if}
		<span id="back" onclick="goAlpha('{$term.term|@substr:0:1}','index.php')">{t}back to index{/t}</span>
		{if $adjacentTerms.next}
		<span onclick="goGlossaryTerm({$adjacentTerms.next.id})" id="next">{t}next >{/t}</span>
		{/if}
	</div>
</div>

{include file="../shared/footer.tpl"}
