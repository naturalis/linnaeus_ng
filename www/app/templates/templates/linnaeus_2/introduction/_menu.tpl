<script type="text/javascript">
	allLookupSetSelectedId({$page.id});
</script>

<div id="allNavigationPane">
{include file="../shared/_back-to-search.tpl"}
<div class="navigation-icon-wrapper">

<span onclick="allLookupShowDialog()" id="contents-icon" title="{t}Contents{/t}" class="navigation-icon" />
{t}Contents{/t}</span>

{if $useJavascriptLinks}
    <span
    {if $adjacentItems.prev}
        onclick="goIntroductionTopic({$adjacentItems.prev.id})" id="previous-icon" 
        title="{t}Previous{/t} {t}topic{/t}">{t}Previous{/t}</a>
    {else}
        id="previous-icon-inactive"
    {/if} 
    class="navigation-icon" />{t}Previous{/t}</span>
    <span 
    {if $adjacentItems.next}
        onclick="goIntroductionTopic({$adjacentItems.next.id})" id="next-icon"
        title="{t}Next{/t} {t}topic{/t}">{t}Next{/t}</a>
    {else}
        id="next-icon-inactive"
    {/if} 
    class="navigation-icon" />{t}Next{/t}</span>
{else}
    {if $adjacentItems.prev}
        <a class="navigation-icon" id="previous-icon" 
        href="../introduction/topic.php?id={$adjacentItems.prev.id}"
        title="{t}Previous{/t} {t}topic{/t}">{t}Previous{/t}</a>
    {else}
        <span class="navigation-icon" id="previous-icon-inactive">{t}Previous{/t}</span>
    {/if}
    {if $adjacentItems.next}
        <a class="navigation-icon" id="next-icon" 
        href="../introduction/topic.php?id={$adjacentItems.next.id}" 
         title="{t}Next{/t} {t}topic{/t}">{t}Next{/t}</a>
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