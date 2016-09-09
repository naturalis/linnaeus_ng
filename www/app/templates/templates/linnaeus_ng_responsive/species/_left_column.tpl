	<div id="left">
		{include file="../search/_simpleSearch.tpl"}		

		<div id="facets">

			<div id="categories">
				<h1 class="main-display-name mobile">{$names.preffered_name} <span class="cursive">{$names.nomen}</span></h1>
				<ul>
                	{* remove 1==2 on r16 once the beelduitwisselaar comes back online, so its link might show on empty media-pages *}
					{foreach from=$categories key=k item=v}
					{if
						($v.id!=$smarty.const.CTAB_CLASSIFICATION &&
						($v.is_empty==0 || $v.id==$smarty.const.CTAB_NAMES)) ||
                        (1==2 && $v.id==$smarty.const.CTAB_MEDIA && $taxon['base_rank_id'] >= $smarty.const.SPECIES_RANK_ID)
					}
					<li id="ctb-{$v.id}" tabname="{$v.tabname}">
						{* $v.tabname *}
						{if $activeCategory==$v.id}
							<a href="#" class="active">{$v.title}</a>
						{else if  $activeCategory=='external' && $ext_tab==$v.id}
							<a href="">{$v.title}</a>
            {else}
							<a href="{if $v.redirect_to}{$v.redirect_to}&ext_tab={$v.id}{else}../species/nsr_taxon.php?id={$taxon.id}&cat={$v.id}{/if}" class="{$v.className}">
  		        	{$v.title}
							</a>	
						{/if}
					</li>
					{/if}
					{/foreach}
				</ul>
			</div>

		</div>


		{include file="../shared/_block_toolbox.tpl"}

		<div id="treebranchContainer">
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
				<tr>
					<td class="buffer-{$buffercount}">
					{if $buffercount>0}
					<!-- {'&nbsp;'|str_repeat:$buffercount} -->
					<span class="classification-connector"></span>
					{/if}
					<div class="classContainer">
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
						{if $classification[taxon].species_count.total>0}
						
						{if $smarty.section.taxon.index==$start}
							<br /><span class="classification-count">({$classification[taxon].species_count.total} {t}soorten in totaal{/t} / {$classification[taxon].species_count.established} {t}gevestigd{/t})</span>
						{else}
							<span class="classification-count">({$classification[taxon].species_count.total}/{$classification[taxon].species_count.established})</span>
						{/if}
						{/if}
					</div>
				</td></tr>
				{/if}
			{/section}

			{foreach from=$children item=v key=x}
			<tr>
				<td class="buffer-{$buffercount+3}">
					<div class="classContainer">
						<span class="classification-connector"></span>
            <span class="classification-name smaller">
            	<a href="?id={$v.id}">
								{if $v.rank_id >= $smarty.const.SPECIES_RANK_ID}
									{if $v.infra_specific_epithet}
										{$v.infra_specific_epithet}
									{elseif $v.specific_epithet}
										{$v.specific_epithet}
									{else}
										{assign var=label value="`$v.specific_epithet` `$v.infra_specific_epithet`"}
										{$label|replace:$lastname:''|replace:'()':''}
									{/if}
								{else}
									{$v.name}
								{/if}
              </a>
            </span>
						<span class="classification-rank">[{$v.rank_label}]</span>
						{if $v.species_count.total>0}
							<span class="classification-count">({$v.species_count.total}/{$v.species_count.established})</span>
						{/if}
					</div>
				</td>
			</tr>
			{/foreach}			
			</table>
		</div>  

		<div id="sideBarLogos">
			{foreach from=$sideBarLogos item=v}
				{if $v.url}
				<a href="{$v.url}" target="_blank">
				{/if}
					<!-- <img src="http://www.nederlandsesoorten.nl/linnaeus_ng/shared/media/project/0001/ravon-logo.png" alt=""> -->

					{if $v.logo}
					<img src="{$v.logo}" {if $v.organisation}title="{$v.organisation}"{/if} />
					{/if}
				{if $v.url}
				</a>
				{/if}
			{/foreach}
		</div>

	</div>
