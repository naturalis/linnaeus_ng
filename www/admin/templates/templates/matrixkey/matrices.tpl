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
	</td>
	<td>[<span class="pseudo-a" onclick="$('#id').val({$val.id});$('#action').val('activate');$('#theForm').submit()">{t}edit matrix{/t}</span>]</td>
	<td>[<span class="pseudo-a" onclick="window.open('matrix.php?id={$val.id}','_self')">{t}edit name{/t}</span>]</td>
	<td>[<span class="pseudo-a" onclick="matrixMatrixDelete({$val.id},'{$val.names[$activeLanguage].name|@addslashes}')">{t}delete{/t}</span>]</td>
</tr>
{/foreach}
</table>
{if $k==-1}No matrices have been defined.{/if}
</p>
<p>
{t _s1='<a href="matrix.php">' _s2='</a>'}Go %shere%s to define a new matrix.{/t}
</p>
<form id="theForm" method="post" action="">
<input type="hidden" name="id" id="id" value="" />
<input type="hidden" name="action" id="action" value="" />
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}