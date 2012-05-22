{include file="../shared/header.tpl"}

<div id="header-titles"></div>
<div id="categories">
<ul>
<li>
    <a class="category{if $taxonType=='lower'}-active{/if} category-first" 
    href="javascript:window.open('index.php','_self')">
    {t}Species and lower taxa{/t}</a>
</li>
<li>
    <a class="category{if $taxonType=='higher'}-active{/if}" 
    href="javascript:window.open('higher.php','_self')">
    {t}Higher Taxa{/t}</a>
</li>
<li>
    <a class="category{if $taxonType=='common'}-active{/if} category-last" 
    href="javascript:window.open('common.php','_self')">
    {t}Common names{/t}</a>
</li>
</ul>
</div>

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
</div>

{include file="../shared/footer.tpl"}
