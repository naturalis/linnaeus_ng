<div id="allNavigationPane">
{include file="../shared/_back-to-search.tpl"}
<div class="navigation-icon-wrapper">
{if $backlink}
    <a class="navigation-icon icon-nav-back" id="back-icon" href="{$backlink.url}" title="{t}Back to {/t} {$backlink.name}">{t}Back{/t}</a>
{else}
    <span class="navigation-icon icon-nav-back icon-inactive" id="back-icon-inactive">{t}Back{/t}</span>
{/if}
</div>
</div>
