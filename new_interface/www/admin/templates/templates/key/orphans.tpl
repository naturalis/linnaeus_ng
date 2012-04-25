{include file="../shared/admin-header.tpl"}

<div id="page-main">
{t}Below is a list of taxa that are not yet part of your key:{/t}
<ul>
{section name=i loop=$taxa}
<li>
{section name=j loop=$taxa[i].list_level-$taxa[0].list_level}.{/section}
 {$taxa[i].taxon}{if $taxa[i].is_hybrid==1} x{/if}</li>
{/section}
</ul>
</div>

{include file="../shared/admin-footer.tpl"}
