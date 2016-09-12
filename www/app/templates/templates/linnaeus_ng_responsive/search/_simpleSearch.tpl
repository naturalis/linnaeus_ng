<style>
.suggestList {
	position: absolute;	
	top: 6em;
	left: 0px;
	font-size: 90%;
	border: solid 2px #ccc;  
	padding: 2px;
	background-color: #fff;
	display: none;
	max-height: 350px;
	overflow: auto;
	width:255px;
}
.suggestList li {
	display: block;
	padding: 3px 0 3px 0;
	order-bottom:1px solid #ddd;
	cursor:pointer;
}
.suggestList li:last-child {
	border-bottom:none;
}

.suggestList li:nth-child(even) {
	background-color:#eee;
}

.suggestList li:hover {
	background-color:#e9ffff;
}
.suggestList li .scientific {
	font-size:0.82em;
	color:#777;
	font-style:italic;
	padding-bottom:2px;
}

.suggestList li .common {
	font-size:1.05em;
	color:#444;
	padding-bottom:4px;
}

</style>
<div class="treeSimpleSearch">
  <form id="inlineformsearch" name="inlineformsearch" action="../search/nsr_search.php" method="get">
		<input id="name" name="search" type="text" placeholder="Snel zoeken..." name="search" class="searchString" title="{t}Zoek op naam{/t}" value="{$search.search}"  autocomplete="off" />
		<div id="name_suggestion" class="suggestList"></div>
	</form>
	<a href="../search/nsr_search_extended.php" class="moreSearchOptions">
		<img src="{$baseUrl}app/style/img/arrow-right.svg" alt="">
		{t}Meer zoekopties{/t}
	</a>
</div>

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