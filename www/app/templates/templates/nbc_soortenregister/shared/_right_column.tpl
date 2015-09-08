	<div id="right">

		<div id="quicksearch">
			<h2>{t}Zoek op naam{/t}</h2>
	
				<form id="inlineformsearch" name="inlineformsearch" action="../search/nsr_search.php" method="get">
				<fieldset>
					<label accesskey="t" for="searchString">Zoek op naam</label>
					<input id="inlineformsearchInput" type="text" name="search" class="searchString" title="{t}Zoek op naam{/t}" value="{$search.search}" />
					<input id="inlineformsearchButton" type="submit" value="{t}zoek{/t}" class="zoekknop" /><br>
					<div id="suggestList"></div>
				</fieldset>
				<ul>
					<li class="searchdb">
						<a href="../search/nsr_search_extended.php"><b>{t}Uitgebreid zoeken{/t}</b></a>
					</li>
					<li class="level2">
						<a href="../search/nsr_search_pictures.php"><b>{t}Foto's zoeken{/t}</b></a>
					</li>
					<li class="level2">
						<a href="../species/tree.php"><b>{t}Taxonomische boom{/t}</b></a>
					</li>
				</ul>
				</form>
				<script type="text/JavaScript">
				$(document).ready(function() {
					$( '#inlineformsearchInput' ).focus();
				});
				</script>				
		</div>
		
        {include file="../shared/_widget-block.tpl"}

	</div>
	
