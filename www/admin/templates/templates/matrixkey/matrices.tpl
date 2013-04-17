{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
{t}Below is a list of matrices that are currently defined. In order to edit a matrix' name, click "edit name". In order to edit the actual matrix, click "edit matrix".{/t}
<table>
{assign var=k value=-1}
{foreach from=$matrices key=k item=val}
<tr class="tr-highlight">
	<td style="width:300px">
		{$val.names[$activeLanguage].name}
		{if $val.default==1}<span title="default">*</span>{/if}
	</td>
	<td>[<span class="a" onclick="$('#id').val({$val.id});$('#action').val('activate');$('#theForm').submit()">{t}edit matrix{/t}</span>]</td>
	<td>[<span class="a" onclick="window.open('matrix.php?id={$val.id}','_self')">{t}edit name{/t}</span>]</td>
    <td>[<a href="?default={$val.id}">{t}set as default{/t}</a>]</td>
	<td>[<span class="a" onclick="matrixMatrixDelete({$val.id},'{$val.names[$activeLanguage].name|@addslashes}')">{t}delete{/t}</span>]</td>
	<td>[<a href="?imgdim={$val.id}">{t}acquire state image dimensions{/t}</a>]</td>
</tr>
{/foreach}
</table>
{if $k==-1}{t}No matrices have been defined.{/t}{/if}
</p>
<p>
[<a href="matrix.php">{t}create a new matrix{/t}</a>]
</p>
<form id="theForm" method="post" action="">
<input type="hidden" name="id" id="id" value="" />
<input type="hidden" name="action" id="action" value="" />
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}