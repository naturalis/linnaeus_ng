{include file="../shared/header.tpl"}

<div id="page-main">

{if $results}

	<div id="search-header">

	 	<p id="header-titles-small">{t}Search results{/t}</p>

		<div id="results-string">
		{if $results.count==0}
			{t _s1=$search.search|replace:'"':''}Your search for "%s" produced no results.{/t}
		{elseif $results.count==1}
			{t _s1=$search.search|replace:'"':'' _s2=$results.count}Your search for "%s" produced %s result.{/t}
		{else}
			{t _s1=$search.search|replace:'"':'' _s2=$results.count}Your search for "%s" produced %s results.{/t}
		{/if}
		</div>
		<div id="buttons">
			<input type="button" onclick="window.open('search.php','_self');" value="modify search">
			<input type="button" onclick="window.open('search_reset.php','_self');" value="new search">
		</div>
	</div>

	<div id="search-results">

{if $results.count>0}

	{foreach $results.data v}
		{if $v.numOfResults>0}
			{foreach $v.results r}
				{if $r.numOfResults>0}
					<div class="module">
						<h3>{if $r.label!=$v.label}{$v.label}: {/if}{$r.label} <span class="result-count">({$r.data|@count})</span></h3>
						{foreach from=$r.data item=d}
						<div class="result">
							<h4><a href="{$r.url|sprintf:$d.id|replace:'#CAT#':$d.cat}">{$d.label}</a>{* <span class="result-count">({$d.matches|@count})*}</span></h4>
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

{else}


    <form id="theForm" method="get" action="" onsubmit="return searchDoSearchForm()" >
    <input type="hidden" name="extended" value="1" />

	<div class="page-generic-div" style="margin-top:25px;">
		<p>
			{t}Search for:{/t} <input type="text" id="search" name="search" value="{$search.search|@escape}" /><br />
			<i>{t}Enclose multiple words with double quotes (") to search for a literal string.{/t}</i>
		</p>
	</div>

    <p>
        {t}In modules:{/t}
	</p>
	<p>
        {foreach $modules.modules v}
        {if $v.module!='Higher taxa' && $v.module!='Index' && $v.module!='Search' && $v.module!='' && $v.show_in_public_menu==1}
        <label>
            <input
                type="checkbox" 
                name="modules[{$v.id}]" 
                value="{$v.controller}" {if $search.modules[$v.id]==$v.controller || $search.modules==null || $search.modules=='*'}checked="checked"{/if}
             />
             {if $v.module=='Species module'} {t}Species module{/t} / {t}Higher taxa{/t}{elseif $v.module=='Additional texts'}{t}Navigator{/t}{else}{t}{$v.module}{/t}{/if}
        </label><br />
        {/if}
        {/foreach}
        {foreach $modules.freeModules v}
        <label>
        	<input type="checkbox" name="freeModules[{$v.id}]" value="{$v.id}" {if $search.freeModules[$v.id]==$v.id || $search.modules==null || $search.modules=='*'}checked="checked"{/if} />
	        {t}{$v.module}{/t}
		</label><br />
        {/foreach}

	</p>
	<input type="submit" id="searchButton" value="{t}search{/t}" />
	</form>

{/if}   

</div>

{include file="../shared/footer.tpl"}