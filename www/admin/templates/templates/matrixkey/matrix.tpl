{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
<form id="theForm" method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" id="id" value="{$matrix.id}" />
<input type="hidden" name="action" id="action" value="save" />

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

<a href="matrices.php">{t}back{/t}</a>

</form>

</p>

</div>

{include file="../shared/admin-messages.tpl"}

<script>
$(document).ready(function(e)
{
	$('#page-block-messages').fadeOut(3000);    
});


</script>


{include file="../shared/admin-footer.tpl"}
