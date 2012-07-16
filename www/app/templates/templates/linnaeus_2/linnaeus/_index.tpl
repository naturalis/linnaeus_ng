<div id="header-titles"></div>
{include file="_navigator-menu.tpl"}
{include file="../shared/_search-main.tpl"}
<div id="page-main">
    <table id="module-grid">
        <tr>
        {assign var=i value=1}
        {foreach from=$menu key=k item=v}
        {if $v.type=='regular' && $v.show_in_public_menu==1}
        <td class="grid">
            <a class="menu-item" href="../{$v.controller}/">
                <img alt="{$v.module}" class="module-icon" src="{$session.app.project.urls.systemMedia}module_icons/{$v.icon}" />
                <div>{t}{$v.module}{/t}</div>
            </a>
        </td>
        {assign var=i value=$i+1}
        {elseif $v.show_in_public_menu==1}
        <td class="grid">
            {if $useJavascriptLinks}
            <span class="a" onclick="goMenuModule({$v.id});">
                <img alt="{$v.module}" class="module-icon" src="{$session.app.project.urls.systemMedia}module_icons/custom.png" />
                <p>{t}{$v.module}{/t}</p>
            </span>
            {else}
            <a class="menu-item" href="../module/?modId={$v.id}">
                <img alt="{$v.module}" class="module-icon" src="{$session.app.project.urls.systemMedia}module_icons/custom.png" />
                <div>{t}{$v.module}{/t}</div>
            </a>
            {/if}
        </td>
        {assign var=i value=$i+1}
        {/if}
        {if $i % 3 ==1}</tr><tr>{/if}
        {/foreach}
        </tr>
    </table>
	<div id="content">
	{$content}
	</div>
</div>
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	allLookupContentOverrideUrl('../search/ajax_interface.php');
});
</script>
{/literal}