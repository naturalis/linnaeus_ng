<div id="main-menu">
<ul>
<li>

<input
    type="search"
    name="search"
    id="search"
    class="search-box"
	placeholder="{t}Search...{/t}"
    value="{if $search.search}{$search.search}{/if}"
	onkeyup="if (event.keyCode==13) { doSearch(); }"
	required
/>
<img onclick="doSearch()" src="{$projectUrls.systemMedia}search.gif" class="search-icon" />

<a class="home{if $controllerBaseName=='linnaeus'}-selected{/if}" href="../linnaeus/home.php">{t}Home{/t}</a></li>
{assign var=first value=true}
{foreach $menu v k}
    {if $v.type=='regular' && $v.show_in_public_menu==1}
        {if $v.controller == $controllerBaseName}
            <li><a class="main-menu-selected" href="../{$v.controller}/">{t}{$v.module}{/t}</a></li>
            {assign var=first value=false}
        {else}
            <li><a href="../{$v.controller}/">{t}{$v.module}{/t}</a></li>
            {assign var=first value=false}
        {/if}
    {elseif $v.show_in_public_menu==1}
        <li><a class="main-menu{if $v.id == $module.id}-selected{/if}" href="../module/?modId={$v.id}">{t}{$v.module}{/t}</a></li>
        {assign var=first value=false}
    {/if}
{/foreach}
</ul>


</div>
