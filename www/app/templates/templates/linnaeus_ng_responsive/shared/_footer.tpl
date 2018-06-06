<div id="allLookupList" class="allLookupListInvisible"></div>
</div>

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
</div>

<script>
$(document).ready(function()
{
	//http://fancyapps.com/fancybox/3/docs/#options
	$('[data-fancybox]').fancybox({
		arrows : false,
		infobar : true,
		animationEffect : false
	});

	acquireInlineTemplates();
});
</script>

<script async src="https://www.googletagmanager.com/gtag/js?id=UA-21555206-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-21555206-1', { 'anonymize_ip': true });
</script>

</body>
</html>