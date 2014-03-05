	<div id="right">


		<div id="quicksearch">
			<h2>Zoek op naam</h2>
	
				<form id="inlineformsearch" name="inlineformsearch" action="../search/nsr_search.php" method="get">
				<fieldset>
					<label accesskey="t" for="searchString">Zoek op naam</label>
					<input id="inlineformsearchInput" type="text" name="search" class="searchString" title="{t}Zoek op naam{/t}" value="" />
					<input id="inlineformsearchButton" type="submit" value="{t}zoek{/t}" class="zoekknop" /><br>
					<div id="suggestList"></div>
				</fieldset>
				<ul>
					<li class="searchdb">
						<a href="../search/nsr_search_extended.php"><b>Uitgebreid zoeken</a>
					</li>
					<li class="level2">
						<a href="../search/nsr_search_pictures.php"><b>Foto's zoeken</a>
					</li>
				</ul>
				</form>
		</div>

		{*
			{if $adjacentItems.prev}
				<a class="navigation-icon" id="previous-icon" 
				href="../species/taxon.php?id={$adjacentItems.prev.id}&cat={$activeCategory}"
				{if $adjacentItems.prev.label} title="{t}Previous to{/t} {$adjacentItems.prev.label}"{/if}>{t}Previous{/t}</a>
			{else}
				<span class="navigation-icon" id="previous-icon-inactive">{t}Previous{/t}</span>
			{/if}
			{if $adjacentItems.next}
				<a class="navigation-icon" id="next-icon" 
				href="../species/taxon.php?id={$adjacentItems.next.id}&cat={$activeCategory}" 
				{if $adjacentItems.next.label} title="{t}Next to{/t} {$adjacentItems.next.label}"{/if}>{t}Next{/t}</a>
			{else}
				<span class="navigation-icon" id="next-icon-inactive">{t}Next{/t}</span>
			{/if}
		*}

	</div>
