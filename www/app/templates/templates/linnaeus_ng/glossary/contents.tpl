{include file="../shared/header.tpl"}
<div id="header-titles">
	<span id="header-title">
		{t}Glossary:{/t}<span class="alphabet-letter-title">{$letter}</span>
	</span>
</div>
<div id="page-main">
		{include file="_alphabet.tpl"}
		{if $alpha|@count==0}
			{t}(no references have been defined){/t}
		{else}
	<div id="content">
			<ul>
			{foreach from=$gloss item=v}
				<li><a href="term.php?id={$v.id}">{$v.term}</a></li>
			{/foreach}
			</ul>
		{/if}
	</div>
</div>

{include file="../shared/footer.tpl"}