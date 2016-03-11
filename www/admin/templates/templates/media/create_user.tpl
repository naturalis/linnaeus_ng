{include file="../shared/admin-header.tpl"}

{include file="../shared/admin-messages.tpl"}

<div id="page-main">

    {if $result == ''}
    	<p><a href="?action=create">create ResourceSpace user</a></p>
    {else}
    	<p>{$result|@print_r}</p>
    {/if}

</div>

{include file="../shared/admin-footer.tpl"}
