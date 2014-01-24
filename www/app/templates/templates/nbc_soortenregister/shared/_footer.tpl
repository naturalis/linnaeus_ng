<div id="allLookupList" class="allLookupListInvisible"></div>
</div ends="page-container">

{if $controllerMenuOverride}
    {include file=$controllerMenuOverride}
{else}
    {if $controllerMenuExists}
        {if $controllerBaseName}{include file="../"|cat:$controllerBaseName|cat:"/_menu.tpl"}{else}{include file="_menu.tpl"}{/if}
    {/if}
{/if}


<div id="footer-container"></div>

</div>
</form>
</div>
</body>
</html>