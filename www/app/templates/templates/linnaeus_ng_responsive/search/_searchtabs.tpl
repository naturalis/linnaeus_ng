<div class="searchBoxContainer {$responsiveTabs}">
	<ul class="tabs">
		<!-- li {if $activeTab eq 'quickSearch'}class="active"{/if}>
			<a href="../search/nsr_search.php?search=">
				<span>Snel</span> 
				<span>zoeken</span>
			</a>
		</li -->
		<li {if $activeTab eq 'extendedSearch'}class="active"{/if}>
			<a href="../search/nsr_search_extended.php">
				<span>Uitgebreid</span> 
				<span>zoeken</span>
			</a>
		</li>
		<li {if $activeTab eq 'searchPictures'}class="active"{/if}>
			<a href="../search/nsr_search_pictures.php">
				<span>Foto's</span> 
				<span>zoeken</span>
			</a>
		</li>
		<li {if $activeTab eq 'taxonTree'}class="active"{/if}>
			<a href="../species/tree.php">
				<span>Taxonomische</span> 
				<span>boom</span>
			</a>
		</li>
	</ul>
		
	{if $activeTab eq 'extendedSearch'}
	<div class="extendedSearch">
	    <input type="text" size="60" class="field" id="{$responsiveTabs}group" name="group" autocomplete="off" placeholder="Filter op soortgroep..." value="{$search.group}">
	    <div id="{$responsiveTabs}group_suggestion" match="like" class="auto_complete" style="display:none;"></div>
	</div>
  	{/if}
</div>