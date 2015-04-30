	<div id="left">

        <div>
            <h2>&nbsp;</h2>
        </div>

		<div id="facets">

			<div id="categories">
				<ul>
                	{* remove 1==2 on r16 once the beelduitwisselaar comes back online, so its linkl might show on empty media-pages *}
					{foreach from=$categories key=k item=v}
					{if
						($v.id!=$smarty.const.CTAB_CLASSIFICATION &&
						($v.is_empty==0 || $v.id==$smarty.const.CTAB_NAMES)) ||
                        (1==2 && $v.id==$smarty.const.CTAB_MEDIA && $taxon['base_rank_id'] >= $smarty.const.SPECIES_RANK_ID)
					}
					<li id="ctb-{$v.id}" tabname="{$v.tabname}">
						{* $v.tabname *}
						{if $activeCategory==$v.id}
						{$v.title}
						{else if  $activeCategory=='external' && $ext_tab==$k}
						{$v.title}
                        {else}
						<a href="{if $v.redirect_to}{$v.redirect_to}&ext_tab={$k}{else}../species/nsr_taxon.php?id={$taxon.id}&cat={$v.id}{/if}" class="{$v.className}">
                        	{$v.title}
						</a>	
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
							{assign var=lastname value="`$classification[taxon].specific_epithet`"}
						{else}
							{$classification[taxon].name}
							{assign var=lastname value=$classification[taxon].name}
						{/if}
					</a>
					</span>
					{assign var=rank_id value=$classification[taxon].rank_id}
					<span class="classification-rank">[{$classification[taxon].rank_label}]</span>
					{if $classification[taxon].species_count.total>0}
					
					{if $smarty.section.taxon.index==$start}
						<br /><span class="classification-count">({$classification[taxon].species_count.total} {t}soorten in totaal{/t} / {$classification[taxon].species_count.established} {t}gevestigd{/t})</span>
					{else}
						<span class="classification-count">({$classification[taxon].species_count.total}/{$classification[taxon].species_count.established})</span>
					{/if}
					{/if}
				</td></tr>
				{/if}
			{/section}

			{foreach from=$children item=v key=x}
			<tr><td>
				{'&nbsp;'|str_repeat:($buffercount+4)}
				<span class="classification-connector"></span>
                <span class="classification-name smaller"><a href="?id={$v.id}">

{if $v.rank_id >= $smarty.const.SPECIES_RANK_ID}
{if $v.infra_specific_epithet}
{$v.infra_specific_epithet}
{else}
{$v.specific_epithet}
{/if}
{else}
{$v.name}
{/if}
                </a></span>
				<span class="classification-rank">[{$v.rank_label}]</span>
				{if $v.species_count.total>0}
				<span class="classification-count">({$v.species_count.total}/{$v.species_count.established})</span>
				{/if}
			</td></tr>
			{/foreach}			
			</table>
		</div>  

		<div id="sideBarLogos">
			{foreach from=$sideBarLogos item=v}
			{if $v.url}<a href="{$v.url}" target="_blank">{/if}{if $v.logo}<img src="{$v.logo}" {if $v.organisation}title="{$v.organisation}"{/if}/>{/if}{if $v.url}</a>{/if}
			{/foreach}
		</div>

	</div>
