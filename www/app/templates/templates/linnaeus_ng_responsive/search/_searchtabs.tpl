<div class="searchBoxContainer {$responsiveTabs}">
	<ul class="tabs">
		<!-- li {if $activeTab eq 'quickSearch'}class="active"{/if}>
			<a href="../search/nsr_search.php?search=">
				<span>{t}Snel{/t}</span> 
				<span>{t}zoeken{/t}</span>
			</a>
		</li -->
		<li {if $activeTab eq 'extendedSearch'}class="active"{/if}>
			<a href="../search/nsr_search_extended.php">
				<span>{t}Uitgebreid{/t}</span> 
				<span>{t}zoeken{/t}</span>
			</a>
		</li>
		<li {if $activeTab eq 'searchPictures'}class="active"{/if}>
			<a href="../search/nsr_search_pictures.php">
				<span>{t}Foto's{/t}</span> 
				<span>{t}zoeken{/t}</span>
			</a>
		</li>
		<li {if $activeTab eq 'taxonTree'}class="active"{/if}>
			<a href="../species/tree.php">
				<span>{t}Taxonomische{/t}</span> 
				<span>{t}boom{/t}</span>
			</a>
		</li>
	</ul>
		
	{if $activeTab eq 'extendedSearch'}
	<div class="extendedSearch">
	    <input type="text" size="60" class="field focusfirst" id="{$responsiveTabs}group" name="group" autocomplete="off" placeholder="{t}Filter op soortgroep...{/t}" value="{$search.group}">
	    <div id="{$responsiveTabs}group_suggestion" match="like" class="auto_complete" style="display:none;"></div>
	</div>
  	{/if}
</div>