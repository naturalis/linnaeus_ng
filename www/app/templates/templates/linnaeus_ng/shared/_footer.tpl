			<div id="allLookupList" class="allLookupListInvisible"></div>
			</div>
			</div>
			</div>
			{if $controllerMenuOverride}
			    {include file=$controllerMenuOverride}
			{else}
			    {if $controllerMenuExists}
			        {if $controllerBaseName}{include file="../"|cat:$controllerBaseName|cat:"/_menu.tpl"}{else}{include file="_menu.tpl"}{/if}
			    {/if}
			{/if}
			<div id="bottombar" class="navbar navbar-inverted">
				<div class="container">
					<ul class="footer-menu__list">
						<li>
							<a href="http://linnaeus.naturalis.nl/" target="_blank">
								Linnaeus NG 2.0
							</a>
						</li>
						<li>
							<a href="../../../admin/views/users/login.php">Login</a>
						</li>
						<li>
							<span class="decode">{$contact}</span>
						</li>
						<li>
							<a target="_blank" href="http://www.naturalis.nl">
								Naturalis Biodiversity Center
							</a>	
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/JavaScript">
$(document).ready(function()
{
	//http://fancyapps.com/fancybox/3/docs/#options
	$('[data-fancybox]').fancybox({
		arrows : false,
		infobar : true,
		animationEffect : false
	});

	$(".inline-video").each(function()
	{
		$_me = $(this);

		arr_arguments = $_me.attr("onclick").split("'");

		$_me
			.removeAttr('onclick')
			.attr('onClick', 'showVideo("' + arr_arguments[1] + '","' + arr_arguments[3] +'");');
	});


	if( jQuery().prettyDialog )
	{
		$("a[rel^='prettyPhoto']").prettyDialog();
	}

	/*
	if( jQuery().shrinkText )
	{
		$("#title a").shrinkText();
		$("#header-title").shrinkText();

		$( window ).resize(function()
		{
			$("#title a").shrinkText();
			$("#header-title").shrinkText();
		});
	}
	*/

	{if $search}onSearchBoxSelect('');{/if}
	{foreach from=$requestData key=k item=v}
	addRequestVar('{$k}','{$v|addslashes}')
	{/foreach}
	chkPIDInLinks({$session.app.project.id},'{$addedProjectIDParam}');
	{if $searchResultIndexActive}
	searchResultIndexActive = {$searchResultIndexActive};
	{/if}

})
</script>
</body>
</html>