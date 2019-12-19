<div id="categories">
    <ul>
        <li>
            <a class="category{if $currentPage=='l2_examine_species'}-active{/if} category-first" 
            href="index.php{if $mapId}?mapId={$mapId}{/if}">
            {t}Examine{/t}</a>
        </li>
        <li>
            <a class="category{if $currentPage=='l2_compare'}-active{/if}" 
            href="compare.php{if $mapId}?mapId={$mapId}{/if}">
            {t}Compare{/t}</a>
        </li>
        <li>
            <a class="category{if $currentPage=='l2_search'}-active{/if}" 
            href="search.php{if $mapId}?mapId={$mapId}{/if}">
            {t}Search{/t}</a>
        </li>
        <li>
            <a class="category{if $currentPage=='l2_diversity'}-active{/if} category-last" 
            href="diversity.php{if $mapId}?mapId={$mapId}{/if}">
            {t}Diversity index{/t}</a>
        </li>
    </ul>
</div>