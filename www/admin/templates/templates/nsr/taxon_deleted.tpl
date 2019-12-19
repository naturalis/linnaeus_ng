{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2>{t}Taxa marked as deleted{/t} <span style="font-size:12px;font-style:normal">({$concepts|@count})</span></h2>

<p>
	<ul>
    {foreach from=$concepts item=v key=k}
    <li><a href="taxon.php?id={$v.id}">{$v.taxon} ({$v.rank})</a> <span style="font-size:0.9em">[{$v.deleted_by}, {$v.deleted_when}]</span></li>
    {/foreach}
    </ul>
</p>
<p>
	<a href="index.php">{t}back{/t}</a>
</p>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}