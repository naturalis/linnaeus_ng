{include file="../shared/header.tpl"}
<div id="header-titles-small">
	<span id="header-title">
		{t}Glossary:{/t}<span class="alphabet-letter-title">{$letter}</span>
	</span>
</div>
{include file="_alphabet.tpl"}

<div id="page-main">
	{if $alpha|@count==0}
		{t}(no references have been defined){/t}
	{else}		
	<div id="content">
			<ul>
			{foreach $gloss v}
				<li><a href="term.php?id={$v.id}">{$v.term}</a></li>
			{/foreach}
			</ul>
		{/if}
	</div>
</div>

{include file="../shared/footer.tpl"}