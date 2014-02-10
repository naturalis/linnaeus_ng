<div id="allNavigationPane">
{include file="../shared/_back-to-search.tpl"}
<div class="navigation-icon-wrapper">

{if $alphaNav.prev}
	<a class="navigation-icon" id="previous-icon" 
	href="index.php?letter={$alphaNav.prev}"
	title="{t}Previous to{/t} {$alphaNav.prev|@strtoupper}">{t}Previous{/t}</a>
{else}
	<span class="navigation-icon" id="previous-icon-inactive">{t}Previous{/t}</span>
{/if}
{if $alphaNav.next}
	<a class="navigation-icon" id="next-icon" 
	href="index.php?letter={$alphaNav.next}" 
	title="Next to {$alphaNav.next|@strtoupper}">{t}Next{/t}</a>
{else}
	<span class="navigation-icon" id="next-icon-inactive">{t}Next{/t}</span>
{/if}

{if $backlink}
    <a class="navigation-icon" id="back-icon" href="{$backlink.url}" title="{t}Back to {/t} {$backlink.name}">{t}Back{/t}</a>
{else}
    <span class="navigation-icon" id="back-icon-inactive">{t}Back{/t}</span>
{/if}
</div>
</div>
