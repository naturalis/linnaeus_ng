<div id="menu-container">
	<div id="main-menu">
{assign var=first value=true}
{foreach from=$menu key=k item=v}
{if $v.type=='regular' && $v.module!='Introduction'}
{if $v.controller == $controllerBaseName}
<div class="menu-item-container-active">
<span class="menu-active-indicator"><a class="menu-item-active" href="../{$v.controller}/">{t}{$v.module}{/t}</a></span><br />
</div>
{assign var=first value=false}
{else}
<div class="menu-item-container">
<a class="menu-item" href="../{$v.controller}/">{t}{$v.module}{/t}</a><br />
</div>
{assign var=first value=false}
{/if}
{elseif $v.module!='Introduction'}
{if !$first}<span class="menu-separator">|</span>{/if}
{if $v.id == $module.id}
<div class="menu-item-container-active">
<span class="menu-active-indicator"><span class="menu-item-active" onclick="goMenuModule({$v.id});">{t}{$v.module}{/t}</span></span><br />
</div>
{assign var=first value=false}
{else}
<div class="menu-item-container">
<span class="menu-item" onclick="goMenuModule({$v.id});">{t}{$v.module}{/t}</span><br />
</div>
{assign var=first value=false}
{/if}
{/if}
{/foreach}
	</div>
	<div id="language-change">
		<input
			type="text"
			name="search"
			id="search"
			class="search-input-shaded"
			value="{if $search}{$search}{else}{t}enter search term{/t}{/if}"
			onkeydown="setSearchKeyed(true);"
			onblur="setSearchKeyed(false);"
			onfocus="onSearchBoxSelect()" />
			<img src="../../media/system/search.gif" onclick="doSearch();" />
		<select id="languageSelect" onchange="doLanguageChange()">
	{foreach from=$languages key=k item=v}
			<option value="{$v.language_id}"{if $v.language_id==$currentLanguageId} selected="selected"{/if}>{$v.language} {if $v.def_language==1}*{/if}</option>
	{/foreach}
		</select>
	</div>
</div>