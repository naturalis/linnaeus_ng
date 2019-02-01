	<div id="left">
		{include file="../search/_simpleSearch.tpl"}		

		<div id="facets">

			<div id="categories">
				<ul>
	                {foreach $categories v k}
					<li id="ctb-{$v.id}" tabname="{$v.tabname}">
	                    {if $activeCategory.id==$v.id}
							<a href="#" class="active">{$v.label}</a>
	                    {else}
                    <a href="../species/nsr_taxon.php?id={$taxon.id}&cat={if $v.type=='auto'}{$v.tabname}{else}{$v.id}{/if}"{if $v.type=='external' && $v.external_reference->link_embed=='link_new'} target="_blank"{/if}>{$v.label}</a>
                    {/if}
					</li>
					{/foreach}
				</ul>
			</div>

		</div>


		{include file="../shared/_block_toolbox.tpl"}

		<div class="treebranchContainer desktop">
            <h2>{t}Indeling{/t}</h2>
			<table id="name-tree">
			{if $children|@count >0}
			{math equation="(x-2)" x=$classification|@count assign=start}
			{else}
			{math equation="(x-3)" x=$classification|@count assign=start}
			{/if}

			{section name=taxon loop=$classification start=$start}
				{math equation="(x-y)*3" x=$smarty.section.taxon.index y=$start assign=buffercount}
                
                {assign v $classification[taxon]}
                
				{if $v.parent_id!=null}
				<tr>
					<td class="buffer-{$buffercount}">
					{if $buffercount>0}
					<!-- {'&nbsp;'|str_repeat:$buffercount} -->
					<span class="classification-connector"></span>
					{/if}
					<div class="classContainer">
						<span class="classification-name{if $smarty.section.taxon.index+1<$classification|@count} smaller{else} current{/if}">
						<a href="nsr_taxon.php?id={$v.id}">
							{if $v.lower_taxon==1}
								{if $v.infra_specific_epithet}
									{$v.infra_specific_epithet}
								{else}
	                                {if $v.previous_is_notho}
										{$v.specific_epithet_no_markers}
                                    {else}
										{$v.specific_epithet}
                                    {/if}
								{/if}
								{assign var=lastname value="`$v.uninomial` `$v.specific_epithet`"}
							{else}
								{$v.name}
								{assign var=lastname value=$v.name}
							{/if}
						</a>
						</span>

						<span class="classification-rank">[{$v.rank_label}]</span>

						{if $v.species_count.total>0}
						
                            {if $tree_taxon_count_style!='none'}
                           
                                {if $smarty.section.taxon.index==$start}
                                    <br /><span class="classification-count">
                                    {if $tree_taxon_count_style=='species_only'}
                                        ({$v.species_count.total} {t}soorten{/t})
                                    {else}
                                        ({$v.species_count.total} {t}soorten in totaal{/t} / {$v.species_count.established} {t}gevestigd{/t})
                                    {/if}
                                    </span>
                                {else}
                                    <span class="classification-count">
                                    {if $tree_taxon_count_style=='species_only'}
                                        ({$v.species_count.total})
                                    {else}
                                        ({$v.species_count.total}/{$v.species_count.established})
                                    {/if}
                                    </span>
                                {/if}
        
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
								{if $v.base_rank_id >= $smarty.const.SPECIES_RANK_ID}
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
                        {if $v.species_count.total>0 && $tree_taxon_count_style!='none'}
                        <span class="classification-count">
                            {if $tree_taxon_count_style=='species_only'}
                                ({$v.species_count.total})
                            {else}
                                ({$v.species_count.total}/{$v.species_count.established})
                            {/if}
                        </span>
                        {/if}
					</div>
				</td>
			</tr>
			{/foreach}			
			</table>
		</div>

		<div class="sideBarLogos desktop">
			{foreach from=$sideBarLogos item=v}
				{if $v.url}
				<a href="{$v.url}" target="_blank">
				{/if}
					{if $v.logo}
						<img src="{$v.logo}" {if $v.organisation}title="{$v.organisation}"{/if} />
					{else}
						<span style="padding-left:30px;">{$v.organisation}</span>
					{/if}
				{if $v.url}
				</a>
				{/if}
			{/foreach}
		</div>

		<div class="sideBarLogos desktop">
			{foreach from=$sideBarLogosNew item=v}
				{if $v.url}
					<a href="{$v.url}" target="_blank">
				{/if}
				{if $v.logo}
					<img src="{$v.logo}" {if $v.organisation}title="{$v.organisation}"{/if} />
				{else}
					<span style="padding-left:30px;">{$v.organisation}</span>
				{/if}
				{if $v.url}
					</a>
				{/if}
			{/foreach}
		</div>
	</div>

	<div class="sideBarLogos responsive">
		{foreach from=$sideBarLogos item=v}
			{if $v.url}
				<a href="{$v.url}" target="_blank">
			{/if}
			{if $v.logo}
				<img src="{$v.logo}" {if $v.organisation}title="{$v.organisation}"{/if} />
			{else}
				<span style="padding-left:30px;">{$v.organisation}</span>
			{/if}
			{if $v.url}
				</a>
			{/if}
		{/foreach}
	</div>
	<div class="treebranchContainer responsive">
            <h2>{t}Indeling{/t}</h2>
			<table id="name-tree">
			{if $children|@count >0}
			{math equation="(x-2)" x=$classification|@count assign=start}
			{else}
			{math equation="(x-3)" x=$classification|@count assign=start}
			{/if}

			{section name=taxon loop=$classification start=$start}
				{math equation="(x-y)*3" x=$smarty.section.taxon.index y=$start assign=buffercount}
                
                {assign v $classification[taxon]}
                
				{if $v.parent_id!=null}
				<tr>
					<td class="buffer-{$buffercount}">
					{if $buffercount>0}
					<!-- {'&nbsp;'|str_repeat:$buffercount} -->
					<span class="classification-connector"></span>
					{/if}
					<div class="classContainer">
						<span class="classification-name{if $smarty.section.taxon.index+1<$classification|@count} smaller{else} current{/if}">
						<a href="nsr_taxon.php?id={$v.id}">
							{if $v.lower_taxon==1}
								{if $v.infra_specific_epithet}
									{$v.infra_specific_epithet}
								{else}
	                                {if $v.previous_is_notho}
										{$v.specific_epithet_no_markers}
                                    {else}
										{$v.specific_epithet}
                                    {/if}
								{/if}
								{assign var=lastname value="`$v.uninomial` `$v.specific_epithet`"}
							{else}
								{$v.name}
								{assign var=lastname value=$v.name}
							{/if}
						</a>
						</span>

						<span class="classification-rank">[{$v.rank_label}]</span>
						{if $v.species_count.total>0}
						
                        {if $tree_taxon_count_style!='none'}

                            {if $smarty.section.taxon.index==$start}
                                <br /><span class="classification-count">
                                {if $tree_taxon_count_style=='species_only'}
                                    ({$v.species_count.total} {t}soorten{/t})
                                {else}
                                    ({$v.species_count.total} {t}soorten in totaal{/t} / {$v.species_count.established} {t}gevestigd{/t})
                                {/if}
                                </span>
                            {else}
                                <span class="classification-count">
                                {if $tree_taxon_count_style=='species_only'}
                                    ({$v.species_count.total})
                                {else}
                                    ({$v.species_count.total}/{$v.species_count.established})
                                {/if}
                                </span>
                            {/if}
    
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
								{if $v.base_rank_id >= $smarty.const.SPECIES_RANK_ID}
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
                        {if $v.species_count.total>0 && $tree_taxon_count_style!='none'}
                        <span class="classification-count">
                            {if $tree_taxon_count_style=='species_only'}
                                ({$v.species_count.total})</span>
                            {else}
                                ({$v.species_count.total}/{$v.species_count.established})</span>
                            {/if}
                        {/if}

					</div>
				</td>
			</tr>
			{/foreach}			
			</table>
		</div>  
