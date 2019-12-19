{include file="../shared/admin-header.tpl"}

<form id="theForm" method="get" action="" onsubmit="return searchDoSearchForm()" >

<div class="page-generic-div">
    <p>
        {t}Search for:{/t} <input type="text" id="search" name="search" value="{$search.search|@escape}" />
        <i>{t}Enclose multiple words with double quotes (") to search for the literal string.{/t}</i>
    </p>
</div>

{include file="../shared/admin-messages.tpl"}



<div id="page-main">
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
{if $results}
<p>
{foreach from=$results item=v}
	{foreach from=$v.results item=r}
		<h3>{$r.label}</h3>
		{foreach from=$r.data item=d}
			<h4>{$d.label}</h4>
		{/foreach}
	{/foreach}
{/foreach}
</p>
{/if}    
    
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	searchReplaceValue = '{/literal}{$search.replacement|@escape}{literal}';
	searchToggleReplace();
	searchSetMinSearchLength({/literal}{$minSearchLength}{literal});
});
</script>
{/literal}


{include file="../shared/admin-footer.tpl"}