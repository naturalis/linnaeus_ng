{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form id="theForm" method="post" action="">
<p>
<fieldset><legend>{t}Find{/t}</legend>
{t}Search for:{/t} <input type="text" id="search" name="search" value="{$search.search|@escape}" />&nbsp;*&nbsp;&nbsp;&nbsp;
<i>{t}Enclose multiple words with double quotes (") to search for the literal string.{/t}</i><br />
{t}In modules:{/t}<br />
{foreach from=$modules.modules item=v}
{if $v.module!='Higher taxa' && $v.module!='Index'}
<label>
	<input type="checkbox" name="modules[{$v.id}]" value="{$v.controller}" {if $search.modules[$v.id]==$v.controller}checked="checked"{/if} />
	{t}{$v.module}{/t}
	{if $v.module=='Species module'} / {t}Higher taxa{/t}{/if}
	{if $v.module=='Additional texts'} ({t}Welcome{/t}, {t}Contributors{/t}, {t}About ETI{/t}){/if}	
</label><br />
{/if}
{/foreach}
<br />
{foreach from=$modules.freeModules item=v}
<label><input type="checkbox" name="freeModules[{$v.id}]" value="{$v.id}" {if $search.freeModules[$v.id]==$v.id}checked="checked"{/if} />{t}{$v.module}{/t}</label><br />
{/foreach}
</fieldset>
</p>
<p>
<fieldset><legend><label><input name="doReplace" type="checkbox" id="replaceToggle" onchange="searchToggleReplace()" {if $search.doReplace=='on'}checked="checked"{/if} />{t}Replace{/t}</label></legend>
<div id="replaceParameters" class="replaceBlankedOut">
{t}Replace with:{/t} <input type="text" id="replacement" name="replacement" value="" disabled="disabled" />&nbsp;&nbsp;&nbsp;
<i>{t}Do not enclose multiple words with double quotes, unless you want them as part of the actual replacement string.{/t}</i><br />
{t}Replace options:{/t}<br />
<label><input type="radio" name="options" id="optionsAll" value="all" {if $search.options=='optionsAll'}checked="checked"{/if} disabled="disabled" />{t}Replace all without confirmation{/t}</label><br />
<label><input type="radio" name="options" id="optionsShow" value="perOccurrence" {if $search.options!='optionsAll'}checked="checked"{/if} disabled="disabled" />{t}Replace per occurrence{/t}</label><br />
</div>
</fieldset>
</p>
<input type="button" id="searchButton" onclick="searchDoSearchForm()" value="{t}search{/t}" />
</form>
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	searchReplaceValue = '{/literal}{$search.replacement|@escape}{literal}';
	searchToggleReplace();
});
</script>
{/literal}


{include file="../shared/admin-footer.tpl"}
