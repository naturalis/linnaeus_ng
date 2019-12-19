<div id="main-body">

<!-- Ruud: removed condition if $controllerBaseName !== 'module' /-->
	{if $controllerBaseName !== 'search'}
		{if $categories or (!$controllerMenuOverride and $controllerMenuExists and $controllerBaseName)}
			<div class="sidebar__container">
				{if $categories}
					<div class="category__container">
				        <ul>
				            {foreach $categories v k}
				            <li id="ctb-{$v.id}">
				 				<a {if $v.is_empty==0}href="../{if $taxon.lower_taxon==1}species/nsr_taxon.php{else}highertaxa/taxon.php{/if}?id={$taxon.id}&cat={$v.tabname}"{/if}
				                {if $activeCategory.id==$v.id}
				                class="category-active"
				                {/if}
				                >{$v.label}</a>
				            </li>
				            {if $activeCategory.id==$v.id && $k==0}{assign var=isTaxonStartPage value=true}{/if}
				            {/foreach}
				        </ul>
				    </div>
			    {/if}
				{if !$controllerMenuOverride and $controllerMenuExists and $controllerBaseName}
					<div class="lookup__container">
						<div id="lookupDialog">
							<input type="text" id="lookupDialogInput" placeholder="{if $_page_start_tpl__filter_content_placeholder}{$_page_start_tpl__filter_content_placeholder}{else}Filter content...{/if}">
							<div id="lookup-DialogContent"></div>
						</div>
					</div>
				{/if}
                
				<div id="menu-bottom">{snippet}menu_bottom.html{/snippet}</div>
                
			</div>	
		{/if}
	{/if}
	<div id="page-container">
