
<div id="main-menu">
<ul>
<!-- <li><a class="home{if $controllerBaseName=='linnaeus'}-selected{/if}" href="../linnaeus/"></a></li> -->
{assign var=first value=true}
{foreach from=$menu key=k item=v}
    {if $v.type=='regular' && $v.show_in_public_menu==1}
        {if $v.controller == $controllerBaseName}
            <li><a class="main-menu-selected" href="../{$v.controller}/">{t}{$v.module}{/t}</a></li>
            {assign var=first value=false}
        {else}
            <li><a href="../{$v.controller}/">{t}{$v.module}{/t}</a></li>
            {assign var=first value=false}
        {/if}
    {elseif $v.show_in_public_menu==1}
        {if $useJavascriptLinks}
            <li><span class="main-menu{if $v.id == $module.id}-selected{/if}" onclick="goMenuModule({$v.id});">{t}{$v.module}{/t}</span></li>
        {else}
            <li><a class="main-menu
            {if $v.id == $module.id}-selected{/if}" href="../module/?modId={$v.id}">{t}{$v.module}{/t}</a></li>
        {/if}
        {assign var=first value=false}
    {/if}
{/foreach}
</ul>
</div>
