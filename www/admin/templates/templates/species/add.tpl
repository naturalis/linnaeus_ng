{include file="../shared/admin-header.tpl"}

<div id="page-main">

{section name=i loop=$languages}
[{$languages[i].language}{if $languages[i].def_language=='1'}*{/if}]
{/section}





</div>

{include file="../shared/admin-footer.tpl"}
