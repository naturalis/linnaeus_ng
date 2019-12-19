<script type="text/JavaScript">
noMessageFade=true;
</script>

{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">

{if !$results}

<form id="theForm" method="get" action="" onsubmit="return searchDoSearchForm()" >
	
    <h2>Search</h2>

    <p>
        {t}Search for:{/t} <input type="text" id="search" name="search" value="{$search.search|@escape}" /> {t}(enclose multiple words with double quotes (") to search for the literal string){/t}
    </p>
    <p>
        {t}in modules:{/t}<br />
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


{else}


<p>

	<input type="button" onclick="window.open('index.php','_self');" value="modify search"> <input type="button" onclick="window.open('search_reset.php','_self');" value="new search">

	<h2>Results ({$results.count})</h2>

{if $results.count>0}

	{foreach from=$results.data item=v key=k}
		{if $v.numOfResults>0}
	    	<h3>{$v.label}</h3>
			{foreach from=$v.results item=r}
				{if $r.numOfResults>0}
					<h4>in {$r.label} ({$r.data|@count})</h4>
					{foreach from=$r.data item=d}
                    	<span class="result-block">
						<h5>
						{if $d.page_id}<a href="{$r.url|sprintf:$d.id:$d.page_id}">{else}<a href="{$r.url|sprintf:$d.id}">{/if}{$d.label}</a>
                        
						{if $d.page_id}<a class="alt-link" href="{$r.url|sprintf:$d.id:$d.page_id}" target="_new">{else}<a class="alt-link" href="{$r.url|sprintf:$d.id}" target="_new">{/if}(new tab)</a>

                        ({$d.matches|@count})</h4>
						{foreach from=$d.matches item=match}
						<h5>{$match}</h5>
						{/foreach}
                        </span>
					{/foreach}
				{/if}
			{/foreach}
		{/if}
	{/foreach}

{else}

	nothing found.

{/if}

</p>

{/if}   

</div>

<script type="text/JavaScript">
$(document).ready(function()
{
	searchSetMinSearchLength({$minSearchLength});
	$('#search').focus();
});
</script>

{include file="../shared/admin-footer.tpl"}