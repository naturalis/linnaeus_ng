{include file="../shared/header.tpl"}
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
                <div class="module-icon {$v.controller}"></div>
                <div>{t}{$v.module}{/t}</div>
            </a>
        </td>
        {assign var=i value=$i+1}
        {elseif $v.show_in_public_menu==1}
        <td class="grid">
            {if $useJavascriptLinks}
            <span class="a" onclick="goMenuModule({$v.id});">
                <div class="module-icon custom custom-{$v.id}"></div>
                <p>{t}{$v.module}{/t}</p>
            </span>
            {else}
            <a class="menu-item" href="../module/?modId={$v.id}">
                <div class="module-icon custom custom-{$v.id}"></div>
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

<div id="splash" style="display: none;"></div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){

	var splashWidth = $('#splash').width() + 12;
	
	showDialog(
		'{/literal}{t}Initializing{/t} {$session.app.project.title}{literal}...',
		'<div id="splash"></div>', 
		vars={width:{/literal}splashWidth{literal}});
	
	$('#dialog').click(function () {
        return false;
 	});
 	
	$('#dialog-mask').click(function () {
        return false;
 	});

	$('#dialog-header').click(function () {
        return false;
 	});

	$('#dialog').load('?go=load', function(response,status,xhr) {
		if (status=='error') {
			$('#status').html('<a href="{/literal}{$startUrl}{literal}">'+_('Continue to ')+'{/literal}{$session.app.project.title}{literal}</a>');
		} else {			
			$('#dialog').html('done').fadeOut(200, function() {
				window.location.href='{/literal}{$startUrl}{literal}';
			});
		}
	});
	
});
</script>
{/literal}




{include file="../shared/footer.tpl"}