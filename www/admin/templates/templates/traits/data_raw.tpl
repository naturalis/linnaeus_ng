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
	<p>
	    <a href="?action=clear">upload another file</a>
	</p>

   <p>
        <table>
        {foreach from=$lines item=line}
            <tr>

                <td>
                	{if !$line.has_data}x{/if}
                
                
                	{$line.trait.sysname}
				</td>

                {foreach from=$line.cells item=v key=k}
                <td>{$v}</td>
                {/foreach}
            </tr>
        {/foreach}
        </table>
	</p>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}