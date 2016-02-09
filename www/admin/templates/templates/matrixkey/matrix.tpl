{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
<form id="theForm" method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" id="id" value="{$matrix.id}" />
<input type="hidden" name="action" id="action" value="" />

<p>
{t}Edit matrix names:{/t}
</p>

<table>
	<tr>
    	<td>{t}Internal name{/t}:</td>
        <td><input type="text" name="sys_name" value="{$matrix.sys_name}" maxlength="64" /></td>
	</tr>                
{foreach $languages v i}
	<tr>
    	<td>{$v.language} {t}name{/t}:</td>
        <td><input type="text" name="name[{$v.language_id}]" value="{$matrix.names[$v.language_id].name}" maxlength="64" /></td>
	</tr>                
{/foreach}		
</table>

<p>
	<input type="submit" value="{t}save{/t}" />
</p>

</form>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
