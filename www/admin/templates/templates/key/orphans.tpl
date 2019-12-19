{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
{t}Below is a list of taxa that are not yet part of your key:{/t}
</p>
<p>
{foreach from=$taxa key=k item=v}
{if $v.id!=$v.res_taxon_id && $v.keypath_endpoint==1}
	<a href="../species/taxon.php?id={$v.id}">{$v.taxon}</a> ({$v.rank})<br />
{/if}
{/foreach}
</p>
</div>

{include file="../shared/admin-footer.tpl"}
