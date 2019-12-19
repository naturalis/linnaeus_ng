{include file="../shared/header.tpl" title="{t}Full search{/t}"}
{* @todo: to be removed: onsubmit="return searchDoSearchForm()" *}
<form id="searchForm" class="advanced-search__form" method="get" action="">
	<input type="hidden" name="extended" value="1" />
	<div class="page-generic-div">
		<ul class="tabs">
			<li class="tab-active">
				<a href="#">{t}Full search{/t}</a>
			</li>
			{if $hasTraits}
			<li class="tab">
				<a href="../search/nsr_search_extended.php">{t}Filter species{/t}</a>
			</li>
			{/if}
			<li>
				<a href="../species/tree.php">{t}Taxonomic tree{/t}</a>
			</li>
		</ul>
		<div class="search-input__container">
			<input type="text" id="search" name="search" value="{$search.search|@escape}" />
			<input type="submit" id="searchButton" value="{t}Search{/t}" />
		</div>
	</div>
	<div class="search-modules__container">
		<i>{t}Enclose multiple words with double quotes (") to search for a literal string.{/t}</i>
		<ul class="search-module__list">
	        {foreach $modules.modules v}
		        {if $v.module!='Higher taxa' && $v.module!='Index' && $v.module!='Search' && $v.module!='' && $v.show_in_public_menu==1}
		        <li>
			        <label class="checkbox-input">
			            <input
			                type="checkbox"
			                name="modules[{$v.id}]"
			                value="{$v.controller}" 
			                {if $search.modules[$v.id]==$v.controller || $search.modules==null || $search.modules=='*'}checked="checked"{/if} />
		                <span class="mock-checkbox"></span>
			             {if $v.module=='Species module'} {t}Species module{/t} / {t}Higher taxa{/t}{elseif $v.module=='Additional texts'}{t}Navigator{/t}{else}{t}{$v.module}{/t}{/if}
			        </label>
		        </li>
		        {/if}
	        {/foreach}
	        {foreach $modules.freeModules v}
	        	<li>
			        <label class="checkbox-input">
			        	<input type="checkbox" 
			        			name="freeModules[{$v.id}]" 
			        			value="{$v.id}" 
			        			{if $search.freeModules[$v.id]==$v.id || $search.modules==null || $search.modules=='*'}checked="checked"{/if} />
	        			<span class="mock-checkbox"></span>
				        {t}{$v.module}{/t}
					</label>
				</li>
	        {/foreach}
        </ul>
	</div>
</form>
{if $results}
	<div id="page-main">
		<div class="search-result__container">
			<div id="search-header">
				<div id="results-string">
					{if $results.count==0}
						{t _s1=$search.search|replace:'"':''}Your search for "%s" produced no results.{/t}
					{elseif $results.count==1}
						{t _s1=$search.search|replace:'"':'' _s2=$results.count}Your search for "%s" produced %s result.{/t}
					{else}
						{t _s1=$search.search|replace:'"':'' _s2=$results.count}Your search for "%s" produced %s results.{/t}
					{/if}
				</div>
			</div>
			<div id="search-results">
				{if $results.count>0}
					{foreach $results.data v}
						{if $v.numOfResults>0}
							{foreach $v.results r}
								{if $r.numOfResults>0}
									<div class="module">
										<h3 class="searched-category-title">{if $r.label!=$v.label}{$v.label}: {/if}{$r.label} <span class="result-count">({$r.data|@count})</span></h3>
										{foreach from=$r.data item=d}
										<div class="result">
											<h4>
												<a href="{$r.url|sprintf:$d.id|replace:'#CAT#':$d.cat}">
													{$d.label}
												</a>
												<span class="result-count">({$d.matches|@count})</span>
											</h4>
											{foreach $d.matches match}
												<h5>{$match}</h5>
											{/foreach}
										</div>
										{/foreach}
									</div>
								{/if}
							{/foreach}
						{/if}
					{/foreach}
				{/if}
			</div>
		</div>
	</div>
{/if}
{include file="../shared/footer.tpl"}