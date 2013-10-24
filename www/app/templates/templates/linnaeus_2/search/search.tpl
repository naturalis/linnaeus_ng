{include file="../shared/header.tpl"}
{include file="_search-main-no-tabs.tpl"}

<div id="page-main">

{if $results}

	<div id="results">
		<div id="header">
		{if $results.count==0}
			{t _s1=$search.search|replace:'"':''}Your search for "%s" produced no results.{/t}
		{elseif $results.count==1}
			{t _s1=$search.search|replace:'"':'' _s2=$results.count}Your search for "%s" produced %s result.{/t}
		{else}
			{t _s1=$search.search|replace:'"':'' _s2=$results.count}Your search for "%s" produced %s results.{/t}
		{/if}
		</div>


<p>

	<input type="button" onclick="window.open('search.php','_self');" value="modify search">
	<input type="button" onclick="window.open('search_reset.php','_self');" value="new search">

	<h2>Results ({$results.count})</h2>

{if $results.count>0}

	{foreach from=$results.data item=v}
		{if $v.numOfResults>0}
			{foreach from=$v.results item=r}
				{if $r.numOfResults>0}
					<h3>{$r.label} ({$r.data|@count})</h3>
					{foreach from=$r.data item=d}
						<h4><a href="{$r.url|sprintf:$d.id}">{$d.label}</a> ({$d.matches|@count})</h4>
						{foreach from=$d.matches item=match}
						<h5>{$match}</h5>
						{/foreach}
					{/foreach}
				{/if}
			{/foreach}
		{/if}
	{/foreach}

{else}

	nothing found.

{/if}

</p>
</div>

{else}


<form id="theForm" method="post" action="" onsubmit="return searchDoSearchForm()" >

	<div class="page-generic-div">
		<p>
			{t}Search for:{/t} <input type="text" id="search" name="search" value="{$search.search|@escape}" />
			<i>{t}Enclose multiple words with double quotes (") to search for the literal string.{/t}</i>
		</p>
	</div>

    <p>
        {t}In modules:{/t}<br />
        {foreach from=$modules.modules item=v}
        {if $v.module!='Higher taxa' && $v.module!='Index' && $v.module!='Search' && $v.module!=''}
        <label>
            <input
                type="checkbox" 
                name="modules[{$v.id}]" 
                value="{$v.controller}" {if $search.modules[$v.id]==$v.controller || $search.modules==null}checked="checked"{/if}
             />
             {if $v.module=='Species module'} {t}Species module{/t} / {t}Higher taxa{/t}{elseif $v.module=='Additional texts'}{t}Navigator{/t}{else}{t}{$v.module}{/t}{/if}
        </label><br />
        {/if}
        {/foreach}
        {foreach from=$modules.freeModules item=v}
        <label>
        	<input type="checkbox" name="freeModules[{$v.id}]" value="{$v.id}" {if $search.freeModules[$v.id]==$v.id || $search.modules==null}checked="checked"{/if} />
	        {t}{$v.module}{/t}
		</label><br />
        {/foreach}

	</p>
	<input type="submit" id="searchButton" value="{t}search{/t}" />
	</form>

{/if}   

</div>



</div>

{include file="../shared/messages.tpl"}
{include file="../shared/footer.tpl"}