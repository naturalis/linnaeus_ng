	<div id="left">

        <div id="quicksearch">
            <h2>{t}Zoek op naam{/t}</h2>
            
            <form id="inlineformsearch" name="inlineformsearch" action="../search/search.php" method="post">
                <input id="inlineformsearchInput" type="text" name="search" class="searchString" title="{t}Zoek op naam{/t}" value="" />
                <input id="inlineformsearchButton" type="submit" value="{t}zoek{/t}" class="zoekknop" />
				<p>
					<a href="../search/nsr_search_extended.php"><b>Uitgebreid zoeken ></b></a>
				</p>
				<p>
					<a href="../search/nsr_search_pictures.php"><b>Foto's zoeken ></b></a>
				</p>
            </form>

        </div>

        <!-- div id="quicksearch">
            <h2></h2>
            
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

        </div -->


	</div>
