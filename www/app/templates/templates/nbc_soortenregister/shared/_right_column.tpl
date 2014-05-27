	<div id="right">

		<div id="quicksearch">
			<h2>Zoek op naam</h2>
	
				<form id="inlineformsearch" name="inlineformsearch" action="../search/nsr_search.php" method="get">
				<fieldset>
					<label accesskey="t" for="searchString">Zoek op naam</label>
					<input id="inlineformsearchInput" type="text" name="search" class="searchString" title="{t}Zoek op naam{/t}" value="{$search.search}" />
					<input id="inlineformsearchButton" type="submit" value="{t}zoek{/t}" class="zoekknop" /><br>
					<div id="suggestList"></div>
				</fieldset>
				<ul>
					<li class="searchdb">
						<a href="../search/nsr_search_extended.php"><b>Uitgebreid zoeken</b></a>
					</li>
					<li class="level2">
						<a href="../search/nsr_search_pictures.php"><b>Foto's zoeken</b></a>
					</li>
					<li class="level2">
						<a href="../species/tree.php"><b>Taxonomische boom</b></a>
					</li>
				</ul>
				</form>
		</div>

	</div>
<script type="text/JavaScript">
$(document).ready(function() {
	$( '#inlineformsearchInput' ).focus();
});
</script>