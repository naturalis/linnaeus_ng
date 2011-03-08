{include file="../shared/header.tpl"}
{include file="../shared/messages.tpl"}

<div id="page-main">
	<div id="content">
		{if $page.image.thumb_name}
		<img id="image-thumb" onclick="showMedia('{$session.project.urls.project_media}{$page.image.file_name|escape:'url'}','{$page.topic}')" src="{$session.project.urls.project_thumbs}{$page.image.thumb_name|escape:'url'}" />
		{elseif $page.image.file_name}
		<img id="image-full" onclick="showMedia('{$session.project.urls.project_media}{$page.image.file_name|escape:'url'}','{$page.topic}')" src="{$session.project.urls.project_media}{$page.image.file_name|escape:'url'}" />
		{/if}
		{$page.content}
	</div>
	<div id="navigation">
	{if $adjacentPages.prev}
	<span onclick="goModuleTopic({$adjacentPages.prev.page_id})" id="prev">{t}< previous{/t}</span>
	{/if}
	<span id="back" onclick="goAlpha('{$page.topic|@substr:0:1}','index.php')">{t}back to index{/t}</span>
	{if $adjacentPages.next}
	<span onclick="goModuleTopic({$adjacentPages.next.page_id})" id="next">{t}next >{/t}</span>
	{/if}
	</div>
</div>

{include file="../shared/footer.tpl"}
