<script type="text/javascript">
	allLookupSetSelectedId({$taxon.id});
</script>

<!-- <div id="allNavigationPane">
{include file="../shared/_back-to-search.tpl"}
<div class="navigation-icon-wrapper">

<span onclick="allLookupShowDialog()" id="contents-icon" title="{t}Contents{/t}" class="navigation-icon icon-book" />
{t}Contents{/t}</span>

{if $adjacentItems.prev}
    <a class="navigation-icon icon-nav-prev" id="previous-icon" 
    href="../species/taxon.php?id={$adjacentItems.prev.id}&cat={$activeCategory}"
    {if $adjacentItems.prev.label} title="{t}Previous to{/t} {$adjacentItems.prev.label}"{/if}>{t}Previous{/t}</a>
{else}
    <span class="navigation-icon icon-nav-prev icon-inactive" id="previous-icon-inactive">{t}Previous{/t}</span>
{/if}
{if $adjacentItems.next}
    <a class="navigation-icon icon-nav-next" id="next-icon" 
    href="../species/taxon.php?id={$adjacentItems.next.id}&cat={$activeCategory}" 
    {if $adjacentItems.next.label} title="{t}Next to{/t} {$adjacentItems.next.label}"{/if}>{t}Next{/t}</a>
{else}
    <span class="navigation-icon icon-nav-next  icon-inactive" id="next-icon-inactive">{t}Next{/t}</span>
{/if}

{if $backlink}
    <a class="navigation-icon icon-nav-back" id="back-icon" href="javascript:history.back()" back-url="{$backlink.url}" title="{t}Back to {/t} {$backlink.name}">{t}Back{/t}</a>
{else}
    <span class="navigation-icon icon-nav-back icon-inactive" id="back-icon-inactive">{t}Back{/t}</span>
{/if}

</div>
</div>
 -->