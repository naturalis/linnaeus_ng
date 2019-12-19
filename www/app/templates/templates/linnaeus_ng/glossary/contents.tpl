{include file="../shared/header.tpl" title="Glossary: "|cat:$letter}

<div id="page-main">
	<div class="glossary-filter">
		{include file="_alphabet.tpl"}
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
</div>

<script>
$(document).ready(function()
{
	allLookupShowDialog();
});
</script>

{include file="../shared/footer.tpl"}