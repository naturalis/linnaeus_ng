{include file="../shared/header.tpl"}
{include file="_header-titles.tpl"}
{include file="../shared/_search-main.tpl"}

<div id="page-main">
    <div id="alphabet">

    <input type="hidden" id="letter" name="letter" value="{$letter}" />

    {if $alpha|@count!=0}
    {t}Click to browse:{/t}&nbsp;
    {foreach name=loop from=$alpha key=k item=v}
    {if $v==$letter}
    <span class="alphabet-active-letter">{$v}</span>
    {else}
    <span class="alphabet-letter" onclick="$('#letter').val('{$v}');$('#theForm').submit();">{$v}</span>
    {/if}
    {/foreach}
    {/if}
    </div>

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
