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
								Linnaeus
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

        $_me
            .removeAttr('onclick')
				.attr('onClick', 'showVideo("' + arr_arguments[1] + '","' + arr_arguments[3] +'");');

		arr_arguments = $_me.attr("onclick").split("'");
	});


	if( jQuery().prettyDialog )
	{
		$("a[rel^='prettyPhoto']").prettyDialog();
	}

	{if $search}onSearchBoxSelect('');{/if}
	{foreach from=$requestData key=k item=v}
	{if !$v|@is_array && !$v|strstr:"javascript"}
	addRequestVar('{$k}',{$v|@addslashes})
	{/if}
	{/foreach}
	chkPIDInLinks({$session.app.project.id},'{$addedProjectIDParam}');
	{if $searchResultIndexActive}
	searchResultIndexActive = {$searchResultIndexActive};
	{/if}

})
</script>
</body>
</html>