<div id="main-body">
	<div class="sidebar__container">
		{if $categories}
			<div class="category__container">
				<div class="category-title responsive-hidden">
					Passport menu
				</div>
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
					<input type="text" id="lookupDialogInput" placeholder="Filter content...">
					<div id="lookup-DialogContent"></div>
				</div>
				
			</div>
		{/if}
	</div>
	<div id="page-container">