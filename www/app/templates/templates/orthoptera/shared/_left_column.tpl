    <aside role="complementary" class="large-3 large-pull-6 sidebar-first columns sidebar">
        <section class="block block-block-nlsoort-search block-block-nlsoort-search-nlsoort-search clearfix">
        
	        <h2 class="block-title">Search</h2>
        
            <form id="inlineformsearch" name="inlineformsearch" action="../search/nsr_search.php" method="get">
            <fieldset>
                <input id="searchString" type="text" name="search" class="searchString" value="{$search.search}" />
                <input id="search-submit" type="submit"  value="{t}zoek{/t}">
            </fieldset>
            
            <ul class="no-bullet">
                <!-- li>
                	<a href="../search/nsr_search_extended.php">Advanced search</a>
                </li -->
                <li>
                	<a href="../species/tree.php">Show taxonomic tree</a>
                </li>
                <li>
                	<a href="../search/nsr_search_pictures.php">Photo search</a>
                </li>
            </ul>

            </form>
        
        </section>

        <section class="block block-menu-block block-menu-block-2 clearfix">
        
        	<h2 class="block-title">About</h2>

            <div class="menu-block-wrapper menu-block-2 menu-name-main-menu parent-mlid-0 menu-level-2">

				<ul>
					{foreach from=$categories key=k item=v}
					{if (($v.is_empty==0 || $v.id==$smarty.const.CTAB_NAMES) && $v.id!=$smarty.const.CTAB_CLASSIFICATION)||$v.id==$smarty.const.CTAB_MEDIA}
					<li id="ctb-{$v.id}" tabname="{$v.tabname}">
						{* $v.tabname *}
						{if $activeCategory==$v.id}
						{$v.title}
						{else}
						<a href="../species/nsr_taxon.php?id={$taxon.id}&cat={$v.id}" class="{$v.className}">{$v.title}</a>	
						{/if}
					</li>
					{/if}
					{/foreach}
				</ul>

            </div>
        
        </section>      
    </aside>