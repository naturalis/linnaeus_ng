    <aside role="complementary" class="large-3 large-pull-6 sidebar-first columns sidebar">
        <section class="block block-block-nlsoort-search block-block-nlsoort-search-nlsoort-search clearfix">
        
	        <h2 class="block-title">&nbsp;</h2>
        
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
					{foreach $categories v k}
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
        
        
        <section id="treebranchContainer">
            <h2>{t}Indeling{/t}</h2>
			<table id="name-tree">
			{if $children|@count >0}
			{math equation="(x-2)" x=$classification|@count assign=start}
			{else}
			{math equation="(x-3)" x=$classification|@count assign=start}
			{/if}

			{section name=taxon loop=$classification start=$start}
				{math equation="(x-y)*3" x=$smarty.section.taxon.index y=$start assign=buffercount}
				{if $classification[taxon].parent_id!=null}
				<tr><td>
					{if $buffercount>0}
					{'&nbsp;'|str_repeat:$buffercount}
					<span class="classification-connector"></span>
					{/if}
					<span class="classification-name{if $smarty.section.taxon.index+1<$classification|@count} smaller{else} current{/if}">
					<a href="nsr_taxon.php?id={$classification[taxon].id}">
						{if $classification[taxon].lower_taxon==1}
							{if $classification[taxon].infra_specific_epithet}
								{$classification[taxon].infra_specific_epithet}
							{else}
								{$classification[taxon].specific_epithet}
							{/if}
							{assign var=lastname value="`$classification[taxon].uninomial` `$classification[taxon].specific_epithet`"}
						{else}
							{$classification[taxon].name}
							{assign var=lastname value=$classification[taxon].name}
						{/if}
					</a>
					</span>
					{assign var=rank_id value=$classification[taxon].rank_id}
					<span class="classification-rank">[{$classification[taxon].rank_label}]</span>
				</td></tr>
				{/if}
			{/section}

			{foreach $children v x}
			<tr><td>
				{'&nbsp;'|str_repeat:($buffercount+4)}
				<span class="classification-connector"></span>
				<span class="classification-name smaller"><a href="?id={$v.id}">{$v.name|replace:$lastname:''}</a></span>
				<span class="classification-rank">[{$v.rank_label}]</span>
				{if $v.species_count.total>0}
				<span class="classification-count">({$v.species_count.total}/{$v.species_count.established})</span>
				{/if}
			</td></tr>
			{/foreach}			
			</table>
		</section>  


        
        
    </aside>