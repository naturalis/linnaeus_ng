{include file="../shared/admin-header.tpl"}

<div id="page-main">
<ul>
{section name=i loop=$taxa}
<li>{$taxa[i].list_level} {$taxa[i].taxon}{if $taxa[i].is_hybrid==1} x{/if}</li>
{/section}
</ul>
</div>

{include file="../shared/admin-footer.tpl"}
