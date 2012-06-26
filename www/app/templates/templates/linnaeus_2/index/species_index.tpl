{include file="../shared/header.tpl"}
{include file="_header-titles.tpl"}
{include file="../shared/_search-main.tpl"}


 	<div id="alphabet">
	<input type="hidden" id="letter" name="letter" value="{$letter}" />
	{if $hasNonAlpha}
		{assign var=l value=$letter|ord}
		{if $l < 97 || $l > 122}
		<span class="alphabet-active-letter">#</span>
		{else}
		<span class="alphabet-letter" onclick="$('#letter').val('#');$('#theForm').submit();">#</span>
		{/if}
	{/if}

	{section name=foo start=97 loop=123 step=1}
	  {assign var=l value=$smarty.section.foo.index|chr}
		{if $l==$letter}
		<span class="alphabet-active-letter">{$l|upper}</span>
		{elseif $alpha[$l]}
		<span class="alphabet-letter" onclick="$('#letter').val('{$l}');$('#theForm').submit();">{$l|upper}</span>
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

<!--

	{if $useJavascriptLinks}
		{if $prevStart!=-1}
		<span class="a" onclick="goNavigate({$prevStart});">< {t}previous{/t}</span>
		{/if}
		{if $nextStart!=-1}
		<span class="a" onclick="goNavigate({$nextStart});">{t}next{/t} ></span>
		{/if}
	{else}
		{if $prevStart!=-1}
		<a href="?start={$prevStart}&letter={$letter}">< {t}previous{/t}</span>
		{/if}
		{if $nextStart!=-1}
		<a href="?start={$nextStart}&letter={$letter}">{t}next{/t} ></span>
		{/if}
	{/if}
	
-->
</div>

{include file="../shared/footer.tpl"}
