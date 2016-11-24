<!-- <div class="treeSimpleSearch">
  <form id="inlineformsearch" name="inlineformsearch" action="../search/nsr_search.php" method="get">
		<input id="name" name="search" type="text" placeholder="Snel zoeken..." name="search" class="searchString" title="{t}Zoek op naam{/t}" value="{$search.search}"  autocomplete="off" />
		<div id="name_suggestion" class="suggestList"></div>
	</form>
	<a href="../search/nsr_search_extended.php" class="moreSearchOptions">
		<img src="{$baseUrl}app/style/img/arrow-right.svg" alt="">
		{t}Meer zoekopties{/t}
	</a>
</div> -->

<div class="inline-templates" id="lineTpl">
<!--
	<li id="item-%IDX%" ident="%IDENT%" onclick="window.open('../species/nsr_taxon.php?id=%IDENT%','_self');" onmouseover="activesuggestion=-1">
    <div class="common">%COMMON_NAME%</div>
    <div class="scientific">%SCIENTIFIC_NAME%</div>
	</li>
-->
</div>


<script>
$(document).ready(function()
{
	bindKeys();
});
</script>