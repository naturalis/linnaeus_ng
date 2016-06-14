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
    </li>

    {foreach $menu v k}
		{if $v.show_in_public_menu==1 && $v.active=='y'}
        {if $v.type=='regular'}
            {if $v.controller == $controllerBaseName}
                <li><a class="main-menu-selected" href="../{$v.controller}/">{t}{$v.module}{/t}</a></li>
            {else}
                <li><a href="../{$v.controller}/">{t}{$v.module}{/t}</a></li>
            {/if}
        {elseif $v.type=='custom'}
            <li><a class="main-menu{if $v.id == $module.id}-selected{/if}" href="../module/?modId={$v.id}">{t}{$v.module}{/t}</a></li>
        {elseif $v.type=='automatic'}
            {if $v.controller == $controllerBaseName}
                <li><a class="main-menu-selected" href="{if $v.url}{$v.url}{else}../{$v.controller}/{/if}">{t}{$v.module}{/t}</a></li>
            {else}
                <li><a href="{if $v.url}{$v.url}{else}../{$v.controller}/{/if}">{t}{$v.module}{/t}</a></li>
            {/if}
        {/if}
        {/if}
    {/foreach}
</ul>


</div>
