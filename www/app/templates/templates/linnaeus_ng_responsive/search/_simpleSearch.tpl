<div class="treeSimpleSearch">
  <form id="inlineformsearch" name="inlineformsearch" action="../search/nsr_search.php" method="get">
		<input id="inlineformsearchInput" type="text" placeholder="Snel zoeken..." name="search" class="searchString" title="{t}Zoek op naam{/t}" value="{$search.search}" />
		<div id="suggestList"></div>
	</form>
	<a href="../search/nsr_search_extended.php" class="moreSearchOptions">
		<img src="{$baseUrl}app/style/img/arrow-right.svg" alt="">
		Meer zoekopties
	</a>
</div>