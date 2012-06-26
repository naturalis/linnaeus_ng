<div id="allNavigationPane">
<div class="navigation-icon-wrapper">

{if $useJavascriptLinks}
    <span
    {if $alphaNav.prev}
        onclick="$('#letter').val('{$alphaNav.prev}');$('#theForm').submit();" id="previous-icon" 
        {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}
    {else}
        id="previous-icon-inactive"
    {/if} 
    class="navigation-icon" />{t}Previous{/t}</span>
    <span 
    {if $nextStart!=-1}
        onclick="$('#letter').val('{$alphaNav.next}');$('#theForm').submit();" id="next-icon"
        {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}
    {else}
        id="next-icon-inactive"
    {/if} 
    class="navigation-icon" />{t}Next{/t}</span>
{else}
    {if $alphaNav.prev}
        <a class="navigation-icon" id="previous-icon" 
        href="javascript:$('#letter').val('{$alphaNav.prev}');$('#theForm').submit();"
        {if $adjacentItems.prev.label} title="{$adjacentItems.prev.label}"{/if}>{t}Previous{/t}</a>
    {else}
        <span class="navigation-icon" id="previous-icon-inactive">{t}Previous{/t}</span>
    {/if}
    {if $alphaNav.next}
        <a class="navigation-icon" id="next-icon" 
        href="javascript:$('#letter').val('{$alphaNav.next}');$('#theForm').submit();" 
        {if $adjacentItems.next.label} title="{$adjacentItems.next.label}"{/if}>{t}Next{/t}</a>
    {else}
        <span class="navigation-icon" id="next-icon-inactive">{t}Next{/t}</span>
    {/if}
{/if}
{if $backlink}
    <a class="navigation-icon" id="back-icon" href="{$backlink.url}" title="{t}Back to {/t}{$backlink.name}">{t}Back{/t}</a>
{else}
    <span class="navigation-icon" id="back-icon-inactive">{t}Back{/t}</span>
{/if}
</div>
</div>
