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

<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-21555206-1', 'nederlandsesoorten.nl');
ga('send', 'pageview');

</script>

</body>
</html>