{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2>{t}Orphaned taxa{/t} <span style="font-size:12px;font-style:normal">({$concepts|@count})</span></h2>

{t}Below are the taxa in your project that have no parent.{/t}

<p>
	<ul>
    {foreach from=$concepts item=v key=k}
    <li>
		<a href="taxon.php?id={$v.id}">{$v.taxon} ({$v.rank})</a>
    	{if $v.is_deleted==1}<span style="font-size:0.9em;color:red;">{t}marked as deleted{/t}</span>{/if}
    	{if $v.id==$treetop}<span style="font-size:0.9em;color:green;">{t}top of taxonomic tree{/t}</span>{/if}
	</li>
    {/foreach}
    </ul>
</p>
<p>
	<a href="index.php">{t}back{/t}</a>
</p>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}