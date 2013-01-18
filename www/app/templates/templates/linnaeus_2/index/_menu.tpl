<div id="allNavigationPane">
{include file="../shared/_back-to-search.tpl"}
<div class="navigation-icon-wrapper">


{if $useJavascriptLinks}
    <span
    {if $alphaNav.prev}
        onclick="$('#letter').val('{$alphaNav.prev}');$('#theForm').submit();" id="previous-icon" 
        title="Previous to {$alphaNav.prev|@strtoupper}"
    {else}
        id="previous-icon-inactive"
    {/if} 
    class="navigation-icon" />{t}Previous{/t}</span>
    <span 
    {if $alphaNav.next}
        onclick="$('#letter').val('{$alphaNav.next}');$('#theForm').submit();" id="next-icon"
        title="Next to {$alphaNav.next|@strtoupper}"
    {else}
        id="next-icon-inactive"
    {/if} 
    class="navigation-icon" />{t}Next{/t}</span>
{else}
    {if $alphaNav.prev}
        <a class="navigation-icon" id="previous-icon" 
        href="javascript:$('#letter').val('{$alphaNav.prev}');$('#theForm').submit();"
        title="{t}Previous to{/t} {$alphaNav.prev|@strtoupper}">{t}Previous{/t}</a>
    {else}
        <span class="navigation-icon" id="previous-icon-inactive">{t}Previous{/t}</span>
    {/if}
    {if $alphaNav.next}
        <a class="navigation-icon" id="next-icon" 
        href="javascript:$('#letter').val('{$alphaNav.next}');$('#theForm').submit();" 
        title="Next to {$alphaNav.next|@strtoupper}">{t}Next{/t}</a>
    {else}
        <span class="navigation-icon" id="next-icon-inactive">{t}Next{/t}</span>
    {/if}
{/if}
{if $backlink}
    <a class="navigation-icon" id="back-icon" href="{$backlink.url}" title="{t}Back to {/t} {$backlink.name}">{t}Back{/t}</a>
{else}
    <span class="navigation-icon" id="back-icon-inactive">{t}Back{/t}</span>
{/if}
</div>
</div>
