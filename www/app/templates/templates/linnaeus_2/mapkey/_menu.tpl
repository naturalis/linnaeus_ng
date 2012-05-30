<!--
<div id="allNavigationPane">
{if $useJavascriptLinks}
<input type="button" value="{t}< previous{/t}" {if $adjacentItems.prev}onclick="goMap({$adjacentItems.prev.id})" {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $adjacentItems.next}onclick="goMap({$adjacentItems.next.id})" {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}{else}disabled="disabled"{/if} class="allLookupButton" />
{else}
{if $allLookupNavigateOverrideUrl}
{assign var=url value=$allLookupNavigateOverrideUrl}
{else}
{assign var=url value='../mapkey/examine_species.php?id='}
{/if}
{if $adjacentItems.prev}
<a href="{$url}{$adjacentItems.prev.id}" {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}>{t}< previous{/t}</a>
&nbsp;
{/if}
{if $adjacentItems.next}
<a href="{$url}{$adjacentItems.next.id}" {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}>{t}next >{/t}</a>
{/if}
{/if}

&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
</div>
-->


<div id="allNavigationPane">
<div class="navigation-icon-wrapper">

<span onclick="allLookupShowDialog()" id="contents-icon" title="{t}Contents{/t}" class="navigation-icon" />
{t}Contents{/t}</span>


{if $useJavascriptLinks}
    <span
    {if $adjacentItems.prev}
        onclick="goTaxon({$adjacentItems.prev.id})" id="previous-icon" 
        {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}
    {else}
        id="previous-icon-inactive"
    {/if} 
    class="navigation-icon" />{t}Previous{/t}</span>
    <span 
    {if $adjacentItems.next}
        onclick="goTaxon({$adjacentItems.next.id})" id="next-icon"
        {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}
    {else}
        id="next-icon-inactive"
    {/if} 
    class="navigation-icon" />{t}Next{/t}</span>
{else}
    {if $allLookupNavigateOverrideUrl}
        {assign var=url value=$allLookupNavigateOverrideUrl}
    {else}
        {assign var=url value='../mapkey/examine_species.php?id='}
    {/if}
    {if $adjacentItems.prev}
        <a class="navigation-icon" id="previous-icon" 
        href="{$url}{$adjacentItems.prev.id}"
        {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}>{t}Previous{/t}</a>
    {else}
        <span class="navigation-icon" id="previous-icon-inactive">{t}Previous{/t}</span>
    {/if}
    {if $adjacentItems.next}
        <a class="navigation-icon" id="next-icon" 
        href="{$url}{$adjacentItems.next.id}" 
        {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}>{t}Next{/t}</a>
    {else}
        <span class="navigation-icon" id="next-icon-inactive">{t}Next{/t}</span>
    {/if}
{/if}
</div>
</div>
