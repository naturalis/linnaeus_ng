<ul class="side-menu">    
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
    {if $languages|@count>1}
        <li class="language__li">
            {foreach $languages v k}
                {if $v.iso2 != ''}
                    <a href="#" class="{if $v.language_id==$currentLanguageId}main-menu-selected{/if}" onclick="doLanguageChange({$v.language_id})">
                        {$v.iso2}
                    </a>
                {/if}
             {/foreach}
        </li>
    {/if}
</ul>