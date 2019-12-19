{if $taxon.id}
<script type="text/javascript">
	allLookupSetSelectedId({$taxon.id});
</script>
{/if}

<!-- <div id="allNavigationPane">
{include file="../shared/_back-to-search.tpl"}
<div class="navigation-icon-wrapper">

<span onclick="allLookupShowDialog()" id="contents-icon" title="{t}Contents{/t}" class="navigation-icon icon-book" />
{t}Contents{/t}</span>

{if $allLookupNavigateOverrideUrl}
    {assign var=url value=$allLookupNavigateOverrideUrl}
{else}
    {assign var=url value='../mapkey/examine_species.php?id='}
{/if}
{*
    {if $adjacentItems.prev}
        <a class="navigation-icon icon-nav-prev" id="previous-icon" 
        href="{$url}{$adjacentItems.prev.id}"
        {if $adjacentItems.prev.label} title="{t}Previous to{/t} {$adjacentItems.prev.label}"{/if}>{t}Previous{/t}</a>
    {else}
        <span class="navigation-icon icon-nav-prev icon-inactive" id="previous-icon-inactive">{t}Previous{/t}</span>
    {/if}
    {if $adjacentItems.next}
        <a class="navigation-icon icon-nav-next" id="next-icon" 
        href="{$url}{$adjacentItems.next.id}" 
        {if $adjacentItems.next.label} title="{t}Next to{/t} {$adjacentItems.next.label}"{/if}>{t}Next{/t}</a>
    {else}
        <span class="navigation-icon icon-nav-next icon-inactive" id="next-icon-inactive">{t}Next{/t}</span>
    {/if}
*}
{if $backlink}
    {if $session.app.user.map.search.taxa}
        {assign var=backUrl value='l2_search.php?action=research'}
    {elseif $session.app.user.map.index}
        {assign var=backUrl value='l2_diversity.php?action=reindex'}
    {else}
        {assign var=backUrl value=$backlink.url}
    {/if}
    <a class="navigation-icon icon-nav-back" id="back-icon" href="javascript:history.back()" back-url="{$backlink.url}" 
    title="{t}Back to {/t}{if $session.app.user.map.search.taxa}{t}Search results{/t}{elseif $session.app.user.map.index}{t}Diversity index{/t}{else}{$backlink.name}{/if}">
    {t}Back{/t}</a>
{else}
    <span class="navigation-icon icon-nav-back icon-inactive" id="back-icon-inactive">{t}Back{/t}</span>
{/if}

</div>
</div>
 -->