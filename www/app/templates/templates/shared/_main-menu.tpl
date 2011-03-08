<div id="menu-container">
	<div id="main-menu">
{foreach from=$menu key=k item=v}
{if $v.type=='regular' && $v.module!='Introduction'}
{if $v.controller == $controllerBaseName}
<a class="menu-item-active" href="../{$v.controller}/">{t}{$v.module}{/t}</a>
{else}
<a class="menu-item" href="../{$v.controller}/">{t}{$v.module}{/t}</a>
{/if}
{elseif $v.module!='Introduction'}
{if $v.id == $module.id}
<span class="menu-item-active" onclick="goMenuModule({$v.id});">{t}{$v.module}{/t}</span>
{else}
<span class="menu-item" onclick="goMenuModule({$v.id});">{t}{$v.module}{/t}</span>
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