{include file="../shared/admin-header.tpl"}

<style>
table {
	border-collapse:collapse;
}
td {
	border:1px solid #999;
	padding:0.5px;
}
</style>


<div id="page-main">
<a href="?action=clear">try again</a>

<table>
{foreach from=$lines item=line}
	<tr>
    {foreach from=$line item=v}
	<td>{$v}</td>
    {/foreach}
    </tr>
{/foreach}
</table>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}