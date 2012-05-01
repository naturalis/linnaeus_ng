<div id="menu-container">
	<div id="main-menu">
	{assign var=first value=true}
	{foreach from=$menu key=k item=v}
	{if $v.type=='regular' && $v.show_in_public_menu==1}
	{if $v.controller == $controllerBaseName}
	<a class="menu-item-active" href="../{$v.controller}/">{t}{$v.module}{/t}</a>
	{assign var=first value=false}
	{else}
	<a class="menu-item" href="../{$v.controller}/">{t}{$v.module}{/t}</a>
	{assign var=first value=false}
	{/if}
	{elseif $v.show_in_public_menu==1}
	{if $useJavascriptLinks}
	<span class="menu-item{if $v.id == $module.id}-active{/if}" onclick="goMenuModule({$v.id});">{t}{$v.module}{/t}</span>
	{else}
	<a class="menu-item{if $v.id == $module.id}-active{/if}" href="../module/?modId={$v.id}">{t}{$v.module}{/t}</a>
	{/if}
	{assign var=first value=false}
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
			<input type="image" src="{$session.app.project.urls.system_media}search.gif" style="border:0" />
		{if $languages|@count>1}
		<select id="languageSelect" onchange="doLanguageChange()">
		{foreach from=$languages key=k item=v}
			<option value="{$v.language_id}"{if $v.language_id==$currentLanguageId} selected="selected"{/if}>{$v.language} {if $v.def_language==1}*{/if}</option>
		{/foreach}
		</select>
		{/if}
	</div>
</div>