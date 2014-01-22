{include file="../shared/header.tpl"}
{include file="_header-titles.tpl"}
{include file="../shared/_search-main.tpl"}


 	<div id="alphabet" class="test">
	<input type="hidden" id="letter" name="letter" value="{$letter}" />
	{if $hasNonAlpha}
		{assign var=l value=$letter|ord}
		{if $l < 97 || $l > 122}
		<span class="alphabet-active-letter">#</span>
		{else}
		<a href="index.php?letter=#">#</a>
		{/if}
	{/if}

	{section name=foo start=97 loop=123 step=1}
	  {assign var=l value=$smarty.section.foo.index|chr}
		{if $l==$letter}
		<span class="alphabet-active-letter">{$l|upper}</span>
		{elseif $alpha[$l]}
		<a class="alphabet-letter" href="index.php?letter={$l}">{$l|upper}</a>
		{else}
		<span class="alphabet-letter-ghosted">{$l|upper}</span>
		{/if}
	{/section}
	</div>

<div id="page-main">
	
    {if $taxonType=='higher'}
        {include file="../index/_higher.tpl"}
    {else}
        {include file="../index/_lower.tpl"}
    {/if}
</div>

{include file="../shared/footer.tpl"}
