{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
{t}Below is a list of matrices that are currently defined. In order to edit a matrix' name, click "edit name". In order to edit the actual matrix, click its name.{/t}
<table>
{foreach from=$matrices key=k item=val}
<tr class="tr-highlight">
	<td style="min-width:200px;">
	    <span class="a" onclick="$('#id').val({$val.id});$('#action').val('activate');$('#theForm').submit()">
		{$val.sys_name}
        </span>
		{if $val.default==1}<span title="default">*</span>{/if}
	</td>
	<td><span class="a" onclick="window.open('matrix.php?id={$val.id}','_self')">{t}edit name{/t}</span></td>
    <td><a href="?default={$val.id}">{t}set as default{/t}</a></td>
	<td><span class="a" onclick="matrixMatrixDelete({$val.id},'{$val.sys_name|@addslashes}')">{t}delete{/t}</span></td>
	<!-- td><a href="?imgdim={$val.id}">{t}acquire state image dimensions{/t}</a></td -->
</tr>
{/foreach}
</table>
{if $matrices|@count==0}{t}No matrices have been defined.{/t}{/if}
</p>

<form id="theForm" method="post" action="">
<input type="hidden" name="id" id="id" value="" />
<input type="hidden" name="action" id="action" value="" />
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
