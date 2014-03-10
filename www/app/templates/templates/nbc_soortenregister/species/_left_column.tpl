	<div id="left">

        <div>
            <h2>&nbsp;</h2>
        </div>

		<div id="facets">

			<div id="categories">
				<ul>
					{foreach from=$categories key=k item=v}
					{if ($v.is_empty==0 || $v.id==$smarty.const.CTAB_NAMES) && $v.id!=$smarty.const.CTAB_CLASSIFICATION}
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

		</div>

        <div class="left-divider"></div>

		{include file="../shared/_block_toolbox.tpl"}

		<div id="treebranchContainer">
            <h2>Indeling</h2>

			<table id="name-tree">
			{math equation="(x-2)" x=$classification|@count assign=start}
			{section name=taxon loop=$classification start=$start}
				{math equation="(x-y)*2" x=$smarty.section.taxon.index y=$start assign=buffercount}
				{if $classification[taxon].parent_id!=null}
				<tr><td>
					{if $buffercount>0}
					{'&nbsp;'|str_repeat:$buffercount}
					<span class="classification-connector">&lfloor;</span>
					{/if}
					<span class="classification-name{if $smarty.section.taxon.index+1<$classification|@count} smaller{/if}">
					<a href="nsr_taxon.php?id={$classification[taxon].id}">
						{if $classification[taxon].lower_taxon==1}
							{$classification[taxon].specific_epithet} {$classification[taxon].infra_specific_epithet}
							{assign var=lastname value="`$classification[taxon].specific_epithet` `$classification[taxon].infra_specific_epithet`"}
						{else}
							{$classification[taxon].name}
							{assign var=lastname value=$classification[taxon].name}
						{/if}
					</a>
					</span>
					{assign var=rank_id value=$classification[taxon].rank_id}
					<span class="classification-rank">[{$classification[taxon].rank}]</span>
					{if $classification[taxon].species_count.total>0}
					<span class="classification-count">({$classification[taxon].species_count.total}/{$classification[taxon].species_count.indigenous})</span>
					{/if}
				</td></tr>
				{/if}
			{/section}

					{foreach from=$children item=v key=x}
					<tr><td>
						{'&nbsp;'|str_repeat:($buffercount+2)}
						<span class="classification-connector">&lfloor;</span>
						<span class="classification-name smaller"><a href="?id={$v.id}">{$v.name|@replace:$lastname:''}</a></span>
						<span class="classification-rank">[{$v.rank}]</span>
						{if $v.species_count.total>0}
						<span class="classification-count">({$v.species_count.total}/{$v.species_count.indigenous})</span>
						{/if}
					</td></tr>
					{/foreach}			

			
			
			
			</table>
		</div>  

	</div>
