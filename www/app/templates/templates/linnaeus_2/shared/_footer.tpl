<div id="allLookupList" class="allLookupListInvisible"></div>
</div ends="page-container">

{if $controllerMenuOverride}
    {include file=$controllerMenuOverride}
{else}
    {if $controllerMenuExists}
        {if $controllerBaseName}{include file="../"|cat:$controllerBaseName|cat:"/_menu.tpl"}{else}{include file="_menu.tpl"}{/if}
    {/if}
{/if}


<div id="footer-container">
</div ends="footer-container">
</div ends="body-container">
</form>
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	$('#body-container').height($(document).height());
	{if $search}onSearchBoxSelect('{$search|@addslashes}');{/if}

{foreach from=$requestData key=k item=v}
addRequestVar('{$k}','{$v|addslashes}')
{/foreach}

})
{literal}
</script>
{/literal}
</body>
</html>