<div class="searchBoxContainer {$responsiveTabs}">
	<ul class="tabs">
		<li class="tab">
			<a class="active " href="../search/nsr_search_extended.php">{t}Filter species{/t}</a>
		</li>
		<li class="tab">
			<a href="../search/search.php">{t}Full search{/t}</a>
		</li>
		<li class="tab">
			<a href="../species/tree.php">{t}Taxonomic tree{/t}</a>
		</li>
	</ul>
		
	<div class="extendedSearch">
	    <input type="text" size="60" class="field focusfirst" id="{$responsiveTabs}group" name="group" autocomplete="off" placeholder="{t}Filter by species group...{/t}" value="{$search.group}">
	    <div id="{$responsiveTabs}group_suggestion" match="like" class="auto_complete" style="display:none;"></div>
	</div>
</div>